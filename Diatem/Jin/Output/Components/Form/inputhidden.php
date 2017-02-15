<?php
/**
* Jin Framework
* Diatem
*/
namespace Diatem\Jin\Output\Components\Form;

use Diatem\Jin\Output\Components\Form\FormComponent;
use Diatem\Jin\Output\Components\ComponentInterface;


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
