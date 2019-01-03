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
use jin\filesystem\IniFile;


/** Validateur : teste si une valeur issue d'un composant SimpleCaptcha est valide
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class ReCaptchavalidator extends GlobalValidator implements ValidatorInterface{
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
	$valeur = $_POST['g-recaptcha-response'];
    $config = new IniFile(JinCore::getJinRootPath().JinCore::getRelativePathAssets().'recaptcha/config.ini');
    $sfile = JinCore::getContainerPath() . JinCore::getSurchargeRelativePath() . '/' . JinCore::getRelativePathAssets().'recaptcha/config.ini';
    if(is_file($sfile)){
        $config->surcharge($sfile);
    }

    $url = $config->get('url');
    $data = array(
      'secret' => $config->get('secret_key'),
      'response' => $valeur
    );
    $options = array(
      'http' => array (
        'method' => 'POST',
        'content' => http_build_query($data)
      )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success=json_decode($verify);

    if ($captcha_success->success==false) {
      parent::addError(Trad::trad('recaptcha_required'));
      return false;
    } else if ($captcha_success->success==true) {
      return true;
    }

  }


    /**
     * Priorité NIV1 du validateur
     * @return boolean
     */
    public function isPrior(){
	    return true;
    }
}

