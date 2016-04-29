<?php
/**
* Jin Framework
* Diatem
*/

namespace jin\external\diatem\sherlock\searchcriterias;

use jin\external\diatem\sherlock\SearchItemInterface;

/** Filtre Sherlock de type critère : recherche approximative sur un terme unique
*
* 	@auteur		Loïc Gerard
* 	@version	0.0.1
* 	@check
*/
class ApproximateOnSingleTerm implements SearchItemInterface{
    /**
    *
    * @var array Noms des champs sur lesquels appliquer le filtre
    */
    private $fields;

    /**
    *
    * @var string  Valeur de test
    */
    private $value;

    /**
    *
    * @var string  Écart acceptable (utilisé par l'algorithme de Levenstein)
    */
    private $fuzziness;


    /**	Constructeur
    *
    * @param array $fields Noms des champs sur lesquels appliquer le filtre
    * @param string $value  Valeur de test
    */
    public function __construct($fields, $value, $fuzziness = 'AUTO') {
        $this->fields    = $fields;
        $this->value    = $value;
        $this->fuzziness = $fuzziness;
    }


    /**	Construit le tableau destiné à être ajouté dans une requête de recherche par SherlockSearch
    *
    * @return array	Paramètres de recherche SherlockSearch
    */
    public function getParamArray(){
        // Ce critère nécessite d'être englober dans un SHOULD pour la recherche multi-champs
        $outArray = array('bool' => array('should' => array()));
        foreach ($this->fields as $field){
            $critArray = array();
            $critArray['fuzzy'] = array();
            $critArray['fuzzy'][$field] = array();
            $critArray['fuzzy'][$field]['value'] = $this->value;
            $critArray['fuzzy'][$field]['fuzziness'] = $this->fuzziness;
            $outArray['bool']['should'][] = $critArray;
        }

        return $outArray;
    }
}
