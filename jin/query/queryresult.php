<?php

namespace jin\query;

use \Exception;
use \Iterator;

class QueryResult implements Iterator {

    private $resultat = array();

    public function __construct($data) {
	if (!is_array($data)) {
	    throw new Exception('Les données doivent être sous la forme d\'un tableau d\'objets');
	}

	$this->resultat = $data;
    }

    public function limitResults($from, $to = -1) {
	if ($from < 0) {
	    throw new Exception('Le paramètre from doit être positif.');
	} elseif ($from > $this->count()) {
	    throw new Exception('Le paramètre from est supérieur au nombre de résultats de la requête.');
	} elseif ($to > $this->count()) {
	    throw new Exception('Le paramètre to est supérieur au nombre de résultats de la requête.');
	}

	$l = NULL;
	if ($to > 0) {
	    $l = $to - $from + 1;
	}
	$this->resultat = array_slice($this->resultat, $from, $l);
    }

    public function addColumn($columnName, $defaultValue = '') {
	$nb = count($this->resultat);
	for ($i = 0; $i < $nb; $i++) {
	    $this->resultat[$i][$columnName] = $defaultValue;
	}
    }

    public function setValueAt($value, $column, $row = 0) {
	$this->resultat[$row][$column] = $value;
    }

    public function count() {
	return count($this->resultat);
    }

    public function getValueAt($column, $row = 0) {

	return $this->resultat[$row][$column];
    }

    public function getDatasInArray() {
	if ($this->count() == 1) {
	    return $this->resultat[0];
	} else {
	    return $this->resultat;
	}
    }

    //Fonctions d'itération
    public function current() {
	return current($this->resultat);
    }

    public function key() {
	return key($this->resultat);
    }

    public function rewind() {
	reset($this->resultat);
	return $this;
    }

    public function next() {
	next($this->resultat);
    }

    public function valid() {
	return array_key_exists(key($this->resultat), $this->resultat);
    }

}
