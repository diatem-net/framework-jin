<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\query;

use \Exception;
use \Iterator;


/** Gestion d'un résultat Query. Peut être parcouru : foreach($objet as $ligne){ echo $ligne['columnName']; }
 *
 * 	@auteur	    Loïc Gerard
 * 	@check
 */
class QueryResult implements Iterator {
    /**
     *
     * @var array   Data
     */
    private $resultat = array();

    
    /**
     * Constructeur
     * @param type $data    Données à initialiser (tableau d'objets)
     * @throws Exception
     */
    public function __construct($data) {
	if (!is_array($data)) {
	    throw new Exception('Les données doivent être sous la forme d\'un tableau d\'objets');
	}

	$this->resultat = $data;
    }

    
    /**
     * Limite les résultats de la query
     * @param int $from	Index de début de parsing
     * @param int $to	Index de fin de parsing
     * @throws Exception
     */
    public function limitResults($from, $to = -1) {
	if ($from < 0) {
	    throw new Exception('Le paramètre from doit être positif.');
	} elseif ($from > $this->count()) {
	    throw new Exception('Le paramètre from est supérieur au nombre de résultats de la requête.');
	} elseif ($to > $this->count()) {
	    throw new Exception('Le paramètre to est supérieur au nombre de résultats de la requête.');
	}

	$l = NULL;
	if ($to >= 0) {
	    $l = $to - $from + 1;
	}
	$this->resultat = array_slice($this->resultat, $from, $l);
    }

    
    /**
     * Ajoute une colonne
     * @param string $columnName    Nom de la colonne
     * @param string $defaultValue  Valeur par défaut à initialiser
     */
    public function addColumn($columnName, $defaultValue = '') {
	$nb = count($this->resultat);
	for ($i = 0; $i < $nb; $i++) {
	    $this->resultat[$i][$columnName] = $defaultValue;
	}
    }

    
    /**
     * Redéfinit la valeur d'une cellule
     * @param string $value Valeur
     * @param string $column	Nom de la colonne
     * @param int $row	Numéro de la ligne
     */
    public function setValueAt($value, $column, $row = 0) {
	$this->resultat[$row][$column] = $value;
    }

    
    /**
     * Retourne le nombre de lignes
     * @return int
     */
    public function count() {
	return count($this->resultat);
    }

    
    /**
     * Retourne la valeur d'une cellule
     * @param string $column	Nom de la colonne
     * @param int $row	Numéro de la ligne
     * @return mixed
     */
    public function getValueAt($column, $row = 0) {
	return $this->resultat[$row][$column];
    }

    
    /**
     * Retourne les données en un tableau
     * @return array
     */
    public function getDatasInArray() {
	if ($this->count() == 1) {
	    return $this->resultat[0];
	} else {
	    return $this->resultat;
	}
    }
    
    
    /** Retourne les en-tête de colonne
     * @return	array
     */
    public function getHeaders(){
	$cols = array();
	foreach($this->resultat[0] as $c => $v){
	    if(!is_numeric($c)){
		$cols[] = $c;
	    }
	}
	
	return $cols;
    }
    

    //Fonctions d'itération
    
    /**
     * Itération : current
     * @return mixed
     */
    public function current() {
	return current($this->resultat);
    }

    
    /**
     * Itération : key
     * @return string
     */
    public function key() {
	return key($this->resultat);
    }

    
    /**
     * Itération : rewind
     * @return \jin\query\QueryResult
     */
    public function rewind() {
	reset($this->resultat);
	return $this;
    }

    
    /**
     * Itération : next
     */
    public function next() {
	next($this->resultat);
    }

    
    /**
     * Itération valid
     * @return boolean
     */
    public function valid() {
	return array_key_exists(key($this->resultat), $this->resultat);
    }

}
