<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\output\components\ui;

use jin\output\components\ui\UIComponent;
use jin\output\components\ComponentInterface;
use jin\filesystem\AssetFile;
use jin\lang\StringTools;
use jin\log\Debug;

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

