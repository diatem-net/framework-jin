<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\diatem\sherlock\searchconditions;

use jin\external\diatem\sherlock\SearchItemInterface;

/** Filtre Sherlock de type condition : filtre sur une plage de valeurs numériques ou de type date
 *
 *  @auteur     Loïc Gerard
 *  @version    0.0.1
 *  @check
 */
class ConditionOnNumericRange implements SearchItemInterface{
    /**
     *
     * @var array Noms des champs sur lesquels appliquer le filtre
     */
    private $fields;


    /** Valeur de bas de plage
     *
     * @var int|string
     */
    private $min;


    /** Valeur de haut de plage
     *
     * @var int|string
     */
    private $max;


    /** Constructeur
     *
     * @param array $fields Noms des champs sur lesquels appliquer le filtre
     * @param int|string $values  Valeur de test
     */
    public function __construct($fields, $values) {
        $this->fields = $fields;
        if(is_int($values)) {
            $this->min = $values;
        } elseif(is_array($values)) {
            if(isset($values[0])) {
                $this->min = $values[0];
            }
            if(isset($values[1])) {
                $this->max = $values[1];
            }
        }
    }


    /** Construit le tableau destiné à être ajouté dans une requête de recherche par SherlockSearch
     *
     * @return array    Paramètres de recherche SherlockSearch
     */
    public function getParamArray(){
        $outArray = array();
        foreach ($this->fields as $field) {
            $condArray['range'] = array();
            if($this->min) {
                $condArray['range'][$field]['gte'] = $this->min;
            }
            if($this->max) {
                $condArray['range'][$field]['lte'] = $this->max;
            }
            $outArray[] = $condArray;
        }

        return $outArray;
    }
}
