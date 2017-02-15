<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Components\Form;

use Diatem\Jin\Output\Components\Form\FormComponent;
use Diatem\Jin\Output\Components\ComponentInterface;


/** Composant InputText (champ input simple)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class InputText extends FormComponent implements ComponentInterface{

    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name) {
	parent::__construct($name, 'inputtext');
    }


    /**
     * Rendu du composant
     * @return type
     */
    public function render(){
	$html = parent::render();

	return $html;
    }
}
