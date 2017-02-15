<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\GlobalValidator;
use Diatem\Jin\Language\Trad;
use Diatem\Jin\Jin;
use Diatem\Jin\FileSystem\IniFile;


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
	include_once Jin::getJinPath().Jin::getRelativePathExtLibs() . 'securimage/securimage.php';

	$config = new IniFile(Jin::getJinPath().Jin::getRelativePathAssets().'simplecaptcha/config.ini');
	$sfile = Jin::getAppPath() . Jin::getSurchargeRelativePath() . '/' . Jin::getRelativePathAssets().'simplecaptcha/config.ini';
	if(is_file($sfile)){
	    $config->surcharge($sfile);
	}

	$securimage = new \Securimage(array('session_name' => $config->get('session_name')));

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

