<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Components\Form;

use Diatem\Jin\Output\Components\Form\FormComponent;
use Diatem\Jin\Output\Components\ComponentInterface;


/** Composant InputDateTime (champ input simple)
 *
 *  @auteur     Samuel Marchal
 *  @version    0.0.1
 *  @check
 */
class InputDateTime extends FormComponent implements ComponentInterface{

    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name) {
    parent::__construct($name, 'inputdatetime');
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
