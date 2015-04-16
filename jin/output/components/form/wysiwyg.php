<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\output\components\form;

use jin\output\components\form\FormComponent;
use jin\output\components\ComponentInterface;
use jin\filesystem\AssetFile;

/** Composant wysiwyg (champ textarea wysiwyg)
 *
 * 	@auteur		JL Fritz
 * 	@version	0.0.1
 * 	@check		
 */
class Wysiwyg extends FormComponent implements ComponentInterface{
    
    
    public static $isJsLoaded = FALSE;
    
    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name) {
	parent::__construct($name, 'wysiwyg');
    }
     
    
    
    
    /**
     * Rendu du composant
     * @return type
     */
    public function render(){
        
        if(!self::$isJsLoaded){
            $html = self::loadWysiwygJs(); 
        }               
        
	$html .= parent::render();	
	return $html;
    }
    
    public static function loadWysiwygJs(){
        self::$isJsLoaded = TRUE;
        $af = new AssetFile('wysiwyg/js.tpl');        
        return $af->getContent();
    }
    
    
    
}
