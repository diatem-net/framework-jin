<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\GlobalValidator;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\Language\Trad;

/** Validateur : teste si une valeur est un numérique compris entre minValue et maxValue (bornes inclues)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Numrange extends GlobalValidator implements ValidatorInterface{
/**
     * Constructeur
     * @param type $args    Tableau d'arguments. minValue (valeur minimale testée) et maxValue  (valeur maximale testée) requis
     */
    public function __construct($args) {
	parent::__construct($args, array('minValue', 'maxValue'));
    }


    /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	parent::resetErrors();
        if(!is_numeric($valeur)){
            return true;
        }
	if(!is_numeric($valeur) || $valeur < parent::getArgValue('minValue') || $valeur > parent::getArgValue('maxValue')){
	    $eMsg = Trad::trad('numrange');
	    $eMsg = StringTools::replaceAll($eMsg, '%minValue%', parent::getArgValue('minValue'));
	    $eMsg = StringTools::replaceAll($eMsg, '%maxValue%', parent::getArgValue('maxValue'));
	    parent::addError($eMsg);
	    return false;
	}
	return true;
    }


    /**
     * Priorité NIV1 du validateur
     * @return boolean
     */
    public function isPrior(){
	return false;
    }
}
