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


    /**	Constructeur
    *
    * @param array $fields      Noms des champs sur lesquels appliquer le filtre
    * @param string $value      Valeur de test
    * @param string $fuzziness  DEPRECATED
    */
    public function __construct($fields, $value, $fuzziness = null) {
        $this->fields          = $fields;
        $this->value           = $value;
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
            $critArray['query_string'] = array();
            $critArray['query_string']['default_field'] = $field;
            $critArray['query_string']['query'] = $this->value;
            $outArray['bool']['should'][] = $critArray;
        }

        return $outArray;
    }
}
