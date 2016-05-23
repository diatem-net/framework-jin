<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\output\components\form;

use jin\output\components\form\FormComponent;
use jin\output\components\ComponentInterface;
use jin\filesystem\AssetFile;

/** Composant ColorPicker (Choix couleur)

 */
class ColorPicker extends FormComponent implements ComponentInterface {

	public static $isJsLoaded = FALSE;
	
	/**
	 * Constructeur
	 * @param string $name  Nom du composant
	 */
	public function __construct($name) {
		parent::__construct($name, 'colorpicker');
	}

	/**
	 * Rendu du composant
	 * @return type
	 */
	public function render() {
		if(!self::$isJsLoaded){
            $html = self::loadWysiwygJs(); 
        }               
        
		$html .= parent::render();	
		return $html;
	}
	
	
	private static function loadWysiwygJs(){
        self::$isJsLoaded = TRUE;
        $af = new AssetFile('colorpicker/js.tpl');  
		$af2 = new AssetFile('colorpicker/css.tpl');  
        return $af->getContent().$af2->getContent();
    }

}
