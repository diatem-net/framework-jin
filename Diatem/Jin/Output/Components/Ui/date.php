<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Components\Ui;

use Diatem\Jin\Output\Components\Ui\UIComponent;
use Diatem\Jin\Output\Components\ComponentInterface;
use Diatem\Jin\FileSystem\AssetFile;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\Log\Debug;

/** Composant UI Date. Affiche une valeur de type Date
 * 	@auteur		Loïc Gerard
 */
class Date extends UIComponent implements ComponentInterface{
    /**
     *
     * @var \DateTime Objet DateTime représentant la valeur
     */
    private $value;


    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name){
	parent::__construct($name, 'ui_date');
    }


    /**
     * Effectue le rendu du composant
     * @return string
     */
    public function render(){
	$html = parent::render();
	$html = StringTools::replaceAll($html, '%value%', $this->getValue());

	return $html;
    }


    /**
     * Retourne la valeur courante au format String
     * @param type $format  [optionel] Format de date en sortie. (Par défaut d/m/Y)
     * @return string
     */
    public function getValue($format = 'd/m/Y'){
	return $this->value->format($format);
    }


   /**
    * Définit la valeur courante au format String
    * @param string $value  Date sous une chaine libre
    */
    public function setValue($value){
	$this->value = new \DateTime($value);
    }


    /**
     * Définit la valeur courante
     * @param \DateTime $dt Objet DateTime
     */
    public function setDateTimeValue(\DateTime $dt){
	$this->value = $dt;
    }


    /**
     * Retourne la valeur au format DateTime
     * @return \DateTime
     */
    public function getDateTimeValue(){
	return $this->value;
    }
}

