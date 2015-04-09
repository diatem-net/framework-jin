<?php
/**
* Jin Framework
* Diatem
*/
namespace jin\output\components\form;

use jin\output\components\form\FormComponent;
use jin\output\components\ComponentInterface;


/** Composant InputHidden (champ input hidden)
*
*  @auteur     Samuel Marchal
*  @version    0.0.1
*  @check
*/
class InputHidden extends FormComponent implements ComponentInterface
{

    /**
    * Constructeur
    * @param string $name  Nom du composant
    */
    public function __construct($name) {
        parent::__construct($name, 'inputhidden');
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
