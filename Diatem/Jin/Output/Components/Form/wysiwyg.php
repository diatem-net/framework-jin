<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Components\Form;

use Diatem\Jin\Output\Components\Form\FormComponent;
use Diatem\Jin\Output\Components\ComponentInterface;
use Diatem\Jin\FileSystem\AssetFile;

/** Composant wysiwyg (champ textarea wysiwyg)
 *
 * 	@auteur		JL Fritz
 * 	@version	0.0.1
 * 	@check
 */
class Wysiwyg extends FormComponent implements ComponentInterface {

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
	public function render() {

		if (!self::$isJsLoaded) {
			$html = self::loadWysiwygJs();
			$html .= parent::render();
		} else {
			$html = parent::render();
		}


		return $html;
	}

	public static function loadWysiwygJs() {
		self::$isJsLoaded = TRUE;
		$af = new AssetFile('wysiwyg/js.tpl');
		return $af->getContent();
	}

}
