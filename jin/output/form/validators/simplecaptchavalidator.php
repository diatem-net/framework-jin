<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\output\form\validators;

use jin\output\form\validators\ValidatorInterface;
use jin\output\form\validators\GlobalValidator;
use jin\language\Trad;
use jin\JinCore;

/** Validateur : teste si une valeur issue d'un composant SimpleCaptcha est valide
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		
 */
class Simplecaptchavalidator extends GlobalValidator implements ValidatorInterface{
    /**
     * Constructeur
     * @param type $args    Tableau d'arguments. (Aucun argument requis)
     */
    public function __construct($args) {
	parent::__construct($args, array());
    }
    
    
     /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	include_once JinCore::getRoot().JinCore::getRelativeExtLibs() . 'securimage/securimage.php';
	$securimage = new \Securimage();
	
	if($valeur == ''){
	    parent::addError(Trad::trad('simplecaptcha_required'));
	    return false;
	}
	
	if ($securimage->check($valeur) == false) {
	    parent::addError(Trad::trad('simplecaptcha_check'));
	    return false;
	}
	
	return true;
    }
    
    
    /**
     * Priorité NIV1 du validateur
     * @return boolean
     */
    public function isPrior(){
	return true;
    }
}

