<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\diatem\sherlock;

use jin\lang\ArrayTools;
use jin\lang\ListTools;
use jin\dataformat\Json;
use jin\external\diatem\sherlock\searchcriterias\AbsoluteOnSingleTerm;
use jin\external\diatem\sherlock\searchcriterias\ApproximateOnSingleTerm;
use jin\external\diatem\sherlock\searchcriterias\ApproximateOnText;
use jin\external\diatem\sherlock\searchcriterias\AbsoluteOnText;
use jin\external\diatem\sherlock\searchcriterias\ApproximateOnPhrase;
use jin\external\diatem\sherlock\searchcriterias\AbsoluteOnPhrase;
use jin\external\diatem\sherlock\searchcriterias\NumericRange;
use jin\external\diatem\sherlock\searchconditions\ConditionOnSingleTerm;
use jin\external\diatem\sherlock\searchconditions\ConditionOnNumericRange;
use jin\external\diatem\sherlock\SherlockResult;
use jin\external\diatem\sherlock\Sherlock;
use jin\external\diatem\sherlock\SherlockCore;
use jin\external\diatem\sherlock\SherlockFacets;
use jin\log\Debug;

/** Permet d'effectuer des recherches
 *
 *  @auteur     Loïc Gerard
 *  @version    0.0.1
 *  @check
 */
class SherlockSearch extends SherlockCore {

    /**
     * @var \jin\external\diatem\sherlock\Sherlock    Instance d'un objet Sherlock
     */
    private $sherlock;

    /**
     * @var array   Critères de recherche.
     */
    private $criterias = array();

    /**
     * @var array Condition de recherche.
     */
    private $conditions = array();

    /**
     * @var array Facets
     */
    private $facets = array();

    /**
     *
     * @var string Liste des types de documents concernés par la recherche
     */
    private $documentTypes = '';

    /**
     *
     * @var string  Mode d'application des conditions. (should | must | must_not)
     */
    private $defaultMode = 'must';

    /**
     *
     * @var int Nombre maximum de résultats recherchés
     */
    private $maxResults = -1;

    /**
     *
     * @var int Index de début de parsing
     */
    private $index = 0;
    
    /**
     *
     * @var string  Attribut utilisé pour trier les résultats (si null = tri par score)
     */
    private $sortby;
    
    /**
     *
     * @var string Sens du sorting
     */
    private $sortside = 'asc';

    /**
     *
     * @var init Nombre minimum de critères devant être réussis pour qu'un résultat soit retourné
     */
    private $minimumShouldMatch = null;

    /** Constructeur
     *
     * @param \jin\external\diatem\sherlock\Sherlock $sherlock  Instance d'un objet Sherlock
     */
    public function __construct(Sherlock $sherlock) {
        $this->sherlock = $sherlock;
        parent::__construct($this->sherlock);
    }

    //--------------------------------------------------------------------------
    //DEFINITION DES CRITERES ET CONDITIONS DE LA RECHERCHE

    /** Ajoute une condition sur un terme EXACT. Une condition dois TOUJOURS être respectée pour qu'un resultat soit retourné.
     *
     * @param string $value         Terme exact
     * @param string $fieldNames    Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param string $mode          Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addCondition($value, $fieldNames, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        $this->conditions[$mode][] = new ConditionOnSingleTerm(ListTools::toArray($fieldNames), $value);
        return true;
    }

    /** Ajoute une condition portant sur une plage de valeurs. (Adapté aux champs de type DATE et NUMERIQUES) Une condition dois TOUJOURS être respectée pour qu'un resultat soit retourné.
     *
     * @param numeric|string $minValue  Valeur de bas de plage.
     * @param numeric|string $maxValue  Valeur de haut de plage.
     * @param string $fieldNames        Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param string $mode              Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addRangeCondition($minValue, $maxValue, $fieldNames, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        $this->conditions[$mode][] = new ConditionOnNumericRange(ListTools::toArray($fieldNames), array($minValue, $maxValue));
        return true;
    }

    /** Ajoute un critère de recherche portant sur un terme unique.
     *
     * @param string $value         Terme recherché
     * @param string $fieldNames    Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param boolean $approximate  [optionel] Définit si la recherche doit porter sur le terme EXACT (FALSE) ou sur une approximation (TRUE). (TRUE par défaut)
     * @param string $mode          Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addSingleTermCriteria($value, $fieldNames, $approximate = true, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        if ($approximate) {
            $this->criterias[$mode][] = new ApproximateOnSingleTerm(ListTools::toArray($fieldNames), $value);
        } else {
            $this->criterias[$mode][] = new AbsoluteOnSingleTerm(ListTools::toArray($fieldNames), $value);
        }
        return true;
    }

    /** Ajoute un critère de recherche portant sur une chaîne de caractère pouvant comprendre plusieurs termes.
     *
     * @param string $value         Termes ou phrase recherchée
     * @param string $fieldNames    Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param type $approximate     [optionel] Définit si la recherche doit porter sur le terme EXACT (FALSE) ou sur une approximation (TRUE). (TRUE par défaut)
     * @param string $mode          Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addTextCriteria($value, $fieldNames, $approximate = true, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        if ($approximate) {
            $this->criterias[$mode][] = new ApproximateOnText(ListTools::toArray($fieldNames), $value);
        } else {
            $this->criterias[$mode][] = new AbsoluteOnText(ListTools::toArray($fieldNames), $value);
        }
        return true;
    }

    /** Ajoute un critère de recherche portant sur une suite de termes dans un ordre donné.
     *
     * @param string $value         Phrase recherchée
     * @param string $fieldNames    Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param type $approximate     [optionel] Définit si la recherche doit porter sur le terme EXACT (FALSE) ou sur une approximation (TRUE). (TRUE par défaut)
     * @param string $mode          Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addPhraseCriteria($value, $fieldNames, $approximate = true, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        if ($approximate) {
            $this->criterias[$mode][] = new ApproximateOnPhrase(ListTools::toArray($fieldNames), $value);
        } else {
            $this->criterias[$mode][] = new AbsoluteOnPhrase(ListTools::toArray($fieldNames), $value);
        }
        return true;
    }

    /** Ajoute un critère de recherche portant sur une plage de valeurs. S'applique essentiellement à des champs de type DATE ou NUMERIQUES.
     *
     * @param numeric|string $minValue  Valeur de bas de plage.
     * @param numeric|string $maxValue  Valeur de haut de plage.
     * @param string $fieldNames        Liste des champs dans lesquels effectuer la recherche. (Séparés par des virgules, sans espaces)
     * @param string $mode              Mode (must | should | default)
     * @return boolean  Succes ou echec
     */
    public function addRangeCriteria($minValue, $maxValue, $fieldNames, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        $this->criterias[$mode][] = new NumericRange(ListTools::toArray($fieldNames), array($minValue, $maxValue));
        return true;
    }

    /** Ajoute une facette.
     *
     * @param object $facet    Facette
     * @param string $mode     Mode (must | should | default)
     * @return boolean         Succes ou echec
     */
    public function addFacet($facet, $mode = null) {
        if(!in_array($mode, array('must', 'should'))) {
            $mode = 'default';
        }
        $this->facets[$mode][] = $facet;
        return true;
    }

    /** Ajoute un type de document dans lequel effectuer la recherche.
     *
     * @param string $documentType  Nom du type de document. Doit correspondre à ce qui est défini dans le XML d'initialisation de l'application.
     * @return boolean  Succes ou echec
     */
    public function addDocumentType($documentType) {
        if (!ListTools::contains($this->documentTypes, $documentType)) {
            $this->documentTypes = ListTools::append($this->documentTypes, $documentType);
            return true;
        }
        return false;
    }

    /** Modifie le mode d'application des conditions en optant pour un mode Cumulatif (toutes les conditions doivent être justes)
     *
     */
    public function setDefaultModeToMust() {
        $this->defaultMode = 'must';
    }

    /** Modifie le mode d'application des conditions en optant pour un mode exclusif (aucune condition ne doit être juste)
     *
     */
    public function setDefaultModeToMustNot() {
        $this->defaultMode = 'must_not';
    }

    /** Modifie le mode d'application des conditions en optant pour un mode optionnel. (Une condition au moins doit être juste)
     *
     */
    public function setDefaultModeToShould() {
        $this->defaultMode = 'should';
    }

    /** Définit le nombre maximal de résultats qui seront retournés. -1 pour ne définir aucune limite.
     *
     * @param int $nb   Nombre max de résultats
     */
    public function setResultNbLimit($nb) {
        $this->maxResults = $nb;
    }

    /** Définit l'index de début de parsing
     *
     * @param int $index    Index de début de parsing
     */
    public function setIndex($index) {
        $this->index = $index;
    }

    /** Définit le nombre maximum de critères qui doivent être justes pour qu'un résultat soit retourné. Si le nombre transmis est supérieur au nombre de critères cette option ne s'appliquera pas.
     *
     * @param int $nb   Nombre de critères
     */
    public function setMinimumShouldMatch($nb) {
        $this->minimumShouldMatch = $nb;
    }
    
    /**
     * Ajoute une contrainte d'ordonancement des résultats
     * @param string $sortBy    Attribut sur lequel ordonner
     * @param string $sens      Sens. (asc ou desc)
     */
    public function setSorting($sortBy, $sens){
        $this->sortby = $sortBy;
        $this->sortside = $sens;
    }

    //--------------------------------------------------------------------------
    //GETTERS

    /** Renvoie le mode d'application des conditions. (or ou and)
     *
     * @return string   Mode d'application des conditions (or ou and)
     */
    public function getConditionOperator() {
        return $this->searchOperator;
    }

    /** Retourne le nombre maximal de résultats qui seront retournés. -1 si pas de limite.
     *
     * @return int  Nombre max de résultats.
     */
    public function getResultNbLimit() {
        return $this->maxResults;
    }

    /** Retourne l'index de début de parsing
     *
     * @return int  Index de début de parsing
     */
    public function getIndex() {
        return $this->index;
    }

    /** Retourne le nombre maximum de critères qui doivent être justes pour qu'un résultat soit retourné.
     *
     * @return int  Nombre de critères
     */
    public function getMinimumShouldMatch() {
        return $this->minimumShouldMatch;
    }

    /** Retourne les paramètres qui seront transmis à Sherlock pour effectuer la recherche. Au format Json.
     *
     * @return string   Query JSon
     */
    public function getJsonQuery() {
        $callParams = array();
        //ordonnancement
        if($this->sortby){
            $callParams['sort'][$this->sortby]['order'] = $this->sortside;
        }
        $callParams['aggregations'] = array();
        $callParams['query']['bool'][$this->defaultMode] = array();

        $hasShould = false;

        //Ajout des critères de recherche
        if (isset($this->criterias['default']) && count($this->criterias['default']) > 0) {
            foreach ($this->criterias['default'] as $criteria) {
                $this->criterias[$this->defaultMode][] = $criteria;
            }
            unset($this->criterias['default']);
        }
        if (isset($this->criterias['must']) && count($this->criterias['must']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'must') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['must'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['must'];
            }
            foreach ($this->criterias['must'] as $criteria) {
                $qr = ArrayTools::merge($qr, $criteria->getParamArray());
            }
        }
        if (isset($this->criterias['should']) && count($this->criterias['should']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'should') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['should'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['should'];
            }
            foreach ($this->criterias['should'] as $criteria) {
                $hasShould = true;
                $qr = ArrayTools::merge($qr, $criteria->getParamArray());
            }
        }

        //Ajout des conditions de recherche
        if (isset($this->conditions['default']) && count($this->conditions['default']) > 0) {
            foreach ($this->conditions['default'] as $condition) {
                $this->conditions[$this->defaultMode][] = $condition;
            }
            unset($this->conditions['default']);
        }
        if (isset($this->conditions['must']) && count($this->conditions['must']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'must') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['must'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['must'];
            }
            foreach ($this->conditions['must'] as $condition) {
                $qr = ArrayTools::merge($qr, $condition->getParamArray());
            }
        }
        if (isset($this->conditions['should']) && count($this->conditions['should']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'should') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['should'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['should'];
            }
            foreach ($this->conditions['should'] as $condition) {
                $hasShould = true;
                $qr = ArrayTools::merge($qr, $condition->getParamArray());
            }
        }

        //Ajout des facets
        if (isset($this->facets['default']) && count($this->facets['default']) > 0) {
            foreach ($this->facets['default'] as $facet) {
                $this->facets[$this->defaultMode][] = $facet;
            }
            unset($this->facets['default']);
        }
        if (isset($this->facets['must']) && count($this->facets['must']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'must') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['must'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['must'];
            }
            foreach ($this->facets['must'] as $facet) {
                if ($facet->getArgArrayForSearchQuery()) {
                    $qr = ArrayTools::merge($qr, $facet->getArgArrayForSearchQuery());
                }
                $callParams['aggregations'] = ArrayTools::merge($callParams['aggregations'], $facet->getArgArrayForAggregate());
            }
        }
        if (isset($this->facets['should']) && count($this->facets['should']) > 0) {
            $qr = &$callParams['query']['bool'][$this->defaultMode];
            if($this->defaultMode !== 'should') {
                $callParams['query']['bool'][$this->defaultMode]['bool']['should'] = array();
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool']['should'];
            }
            foreach ($this->facets['should'] as $facet) {
                if ($facet->getArgArrayForSearchQuery()) {
                    $hasShould = true;
                    $qr = ArrayTools::merge($qr, $facet->getArgArrayForSearchQuery());
                }
                $callParams['aggregations'] = ArrayTools::merge($callParams['aggregations'], $facet->getArgArrayForAggregate());
            }
        }

        if ($hasShould && !is_null($this->minimumShouldMatch)) {
            $qr = &$callParams['query']['bool'];
            if($this->defaultMode !== 'should') {
                $qr = &$callParams['query']['bool'][$this->defaultMode]['bool'];
            }
            $qr['minimum_should_match'] = $this->minimumShouldMatch;
        }

        if(count($callParams['aggregations']) == 0) {
            unset($callParams['aggregations']);
        }
        if(count($callParams['query']['bool'][$this->defaultMode]) == 0) {
            unset($callParams['query']['bool']);
            $callParams['query']['match_all'] = array();
        }

        return Json::encode($callParams);
    }

    //--------------------------------------------------------------------------
    //RECHERCHE

    /** Effectue la recherche. Retourne FALSE ou un objet SherlockResult.
     *
     * @return boolean|\jin\external\diatem\sherlock\SherlockResult Objet SherlockResult décrivant les données issues de la recherche
     */
    public function search() {

        $callString = $this->sherlock->getAppzCode() . '/';

        //Recherche dans tous les documentTypes ou dans les documentType spécifiés
        if ($this->documentTypes != '') {
            $callString .= $this->documentTypes . '/';
        }

        //$callString .= '_search?sort=_score';
        $callString .= '_search';
        
        //On spécifie l'index (0 par défaut)
        $callString .= '?from=' . $this->index;

        //si limite en nb de résultats
        if ($this->maxResults > 0) {
            $callString .= '&size=' . $this->maxResults;
        }

        //Pas d'ordonnancement spécifique
        if(!$this->sortby){
            $callString .= '&sort=_score';
        }

        //On initialise l'array servant à déterminer le Json envoyé à elesticSearch
        $callParamsJson = $this->getJsonQuery();

        if (!$callParamsJson) {
            parent::throwError('Une erreur a eu lieu lors de la transformation des parametres au format Json : ' . Json::getLastErrorVerbose());
            return false;
        }

        $retour = parent::callMethod($callString, $callParamsJson, 'XGET');
        if (!$retour) {
            parent::throwError('Une erreur ElasticSearch a eu lieu : ' . $this->sherlock->getLastServerResponse());
            return false;
        }

        if (isset($retour['status']) && $retour['status'] != 200) {
            parent::throwError('Une erreur ElasticSearch a eu lieu : ' . $this->sherlock->getLastServerResponse());
            return false;
        }

        //Debug::dump($retour);

        foreach (array('must', 'should', 'default') as $mode) {
            if(isset($this->facets[$mode]) && count($this->facets[$mode]) > 0) {
                foreach ($this->facets[$mode] as $facet) {
                    $data = array();
                    if (isset($retour['aggregations'][$facet->getName()])) {
                        $data = array_map(function($item) {
                            // Remove empty facets (ie. if it's not like array('key', 'doc_count'))
                            if(!is_array($item)) {
                                return false;
                            }
                            return array_filter($item, function($v) {
                                return $v['key'];
                            });
                        }, $retour['aggregations'][$facet->getName()]);
                    }
                    $facet->setESReturnData($data);
                }
            }
        }


        return new SherlockResult($retour, $this->index);
    }

}
