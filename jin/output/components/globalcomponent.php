<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\output\components;

use jin\filesystem\AssetFile;
use jin\lang\StringTools;

/** Classe parent de tout composant
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		
 */
class GlobalComponent{
    /**
     *
     * @var string  Nom du composant
     */
    protected $name;
    
    
    /**
     *
     * @var string  Nom du type de composant (Ex. InputText)
     */
    protected $componentName;
    
    
    /**
     * Constructeur
     * @param string $name  Nom du composant
     * @param string $componentName Nom du type de composant (Ex. InputText)
     */
    protected function __construct($name, $componentName) {
	$this->name = $name;
	$this->componentName = $componentName;
    }
    
    
    /**
     * Retourne le rendu de l'asset du composant
     * @return string
     */
    protected function getAsset(){
	$af = new AssetFile('inputtext/html.tpl');
	return $af->getContent();
    }
    
    
    /**
     * Retourne le nom du composant
     * @return string
     */
    public function getName(){
	return $this->name;
    }
    
    
    /**
     * Rendu par défaut d'un composant(prise en compte de %name%)
     * @return	string
     */
    protected function render(){
	$html = $this->getAsset();
	$html = StringTools::replaceAll($html, '%name%', $this->getName());
	
	return $html;
    }
}

