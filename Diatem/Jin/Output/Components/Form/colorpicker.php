<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Components\Dorm;

use Diatem\Jin\Output\Components\ComponentInterface;
use Diatem\Jin\FileSystem\AssetFile;

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
		$html = '';
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
