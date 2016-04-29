<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\diatem\sherlock\searchcriterias;

use jin\external\diatem\sherlock\SearchItemInterface;

/** Filtre Sherlock de type critère : recherche approximative sur plusieurs termes
 *
 *  @auteur     Samuel Marchal
 *  @version    0.0.2
 *  @check
 */
class ApproximateOnPhrase implements SearchItemInterface{
    /**
     *
     * @var array Noms des champs sur lesquels appliquer le filtre
     */
    private $fields;

    /**
     *
     * @var string  Valeur de test
     */
    public $values;

    /**
     *
     * @var integer  Écart acceptable
     */
    public $slop;


    /** Constructeur
     *
     * @param array $fields Noms des champs sur lesquels appliquer le filtre
     * @param string $values  Valeur de test
     */
    public function __construct($fields, $values, $slop = 10) {
        $this->fields = $fields;
        $this->values = $values;
        $this->slop   = $slop;
    }


    /** Construit le tableau destiné à être ajouté dans une requête de recherche par SherlockSearch
     *
     * @return array    Paramètres de recherche SherlockSearch
     */
    public function getParamArray() {
        $critArray = array();
        $critArray['multi_match'] = array();
        $critArray['multi_match']['type'] = 'phrase';
        $critArray['multi_match']['slop'] = $this->slop;
        $critArray['multi_match']['query'] = $this->values;
        $critArray['multi_match']['fields'] = $this->fields;

        return $critArray;
    }
}