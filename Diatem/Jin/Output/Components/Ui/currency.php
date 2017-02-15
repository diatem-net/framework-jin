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

/** Composant UI Currency. Affiche une valeur de type monétaire
 * 	@auteur		Loïc Gerard
 */
class Currency extends UIComponent implements ComponentInterface{
    /**
     *
     * @var int
     */
    private $value;


    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name){
	parent::__construct($name, 'ui_currency');
    }


     /**
     * Effectue le rendu du composant
     * @return string
     */
    public function render(){
        $html = parent::render();
	$html = StringTools::replaceAll($html, '%value%', number_format($this->getValue(), 2));

	return $html;
    }


    /**
     * Retourne la valeur courante
     * @return string
     */
    public function getValue(){
	return $this->value;
    }

    /**
    * Définit la valeur courante au format String
    * @param string $value  Date sous une chaine libre
    */
    public function setValue($value){
	$this->value = $value;
    }
}

