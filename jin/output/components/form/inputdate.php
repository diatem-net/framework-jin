<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\output\components\form;

use jin\output\components\form\FormComponent;
use jin\output\components\ComponentInterface;
use jin\lang\StringTools;
use jin\lang\TimeTools;


/** Composant InputDate (champ input simple)
 *
 *  @auteur     Samuel Marchal
 *  @version    0.0.1
 *  @check
 */
class InputDate extends FormComponent implements ComponentInterface{

    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name) {
    parent::__construct($name, 'inputdate');
    }


    /**
     * Rendu du composant
     * @return type
     */
    public function render(){
    $html = parent::render();

    return $html;
    }
    
    public function getValue(){
        $valeur = parent::getValue();
        
        if($valeur == ''){
            return '';
        }
        
        if(!TimeTools::validateDate($valeur, 'd/m/Y')){
            return $valeur;
        }
        
        if(StringTools::contains($valeur, '/')){
            $dt = \DateTime::createFromFormat('d/m/Y', $valeur);
        }else{
            $dt = new \DateTime($valeur);
        }
        
        return $dt->format('d/m/Y');
    }
    
    
    

}
