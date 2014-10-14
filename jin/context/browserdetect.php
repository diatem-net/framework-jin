<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\context;

use jin\log\Debug;
use jin\JinCore;

/** Classe permettant la détection du navigateur et de ses capacités (basé sur phpbrowscap : https://github.com/browscap/browscap-php)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		22/04/2014
 *	@maj		11/06/2014  :	[Loïc Gerard]	ajout de la méthode isCrawler()
 */
class BrowserDetect {

    /**	Données issues de l'analyse des capacités du navigateur par phpbrowscap
     *
     * @var array
     */
    private static $browserData;

    
    /**	Retourne une chaîne identifiant le navigateur/plate-forme client
     * 
     * @return string	chaîne d'identification
     */
    public static function getUserAgent(){
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->browser_name;
    }
    
    
    /**	Retourne le nom du navigateur
     * 
     * @return string	Nom du navigateur
     */
    public static function getBrowser() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Browser;
    }

    
    /**	Retourne la version du navigateur
     * 
     * @return string	Version du navigateur
     */
    public static function getBrowserVersion() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Version;
    }

    
    /**	Retourne la version majeure du navigateur
     * 
     * @return string	Version majeure
     */
    public static function getBrowserMajorVersion() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->MajorVer;
    }

    
    /** Retourne si Javascript est supporté
     * 
     * @return boolean	Javascript supporté
     */
    public static function isJavascriptEnabled() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->JavaScript;
    }
    
    
    /** Permet de connaître le système de pointage utilisé (mouse ou finger)
     * 
     * @return string	Système de pointage utilisé
     */
    public static function getDevicePointingMethod() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Device_Pointing_Method;
    }

    
    /** Retourne si les applets Java sont supportés
     * 
     * @return boolean	Applets Java supportés
     */
    public static function isJavaAppletsEnabled() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->JavaApplets;
    }

    
    /** Retourne si les cookies sont supportés
     * 
     * @return boolean	Cookies supportés
     */
    public static function isCookiesEnabled() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Cookies;
    }

    
    /**	Retourne la version de CSS supportée
     * 
     * @return string	Version de CSS supportée
     */
    public static function getCssVersionAllowed() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->CssVersion;
    }

    
    /**	Retourne le nom du materiel utilisé
     * 
     * @return string	Nom du matériel utilisé
     */
    public static function getDeviceName() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Device_Name;
    }
    
    /**	Retourne le type de materiel utilisé
     * 
     * @return string	Type matériel utilisé
     */
    public static function getDeviceType() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Device_Type;
    }

    
    /**	Retourne le nom de l'OS utilisé
     * 
     * @return string	Nom de l'OS utilisé
     */
    public static function getPlateformName() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Platform_Description;
    }

    
    /**	Retourne la version de l'OS utilisé
     * 
     * @return string	Version de l'OS
     */
    public static function getPlateformVersion() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->Platform_Version;
    }

    
    /**	Retourne le nom du moteur de rendu utilisé
     * 
     * @return string	Nom du moteur de rendu
     */
    public static function getRenderingEngineName() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->RenderingEngine_Name;
    }

    
    /**	Retourne la version du moteur de rendu utilisé
     * 
     * @return string	Version du moteur de rendu
     */
    public static function getRenderingEngineVersion() {
	if (is_null(self::$browserData)) {
	    self::detectBrowser();
	}

	return self::$browserData->RenderingEngine_Version;
    }
    
    
    /**
     * Permet de savoir si il s'agit d'un robot
     * @return boolean TRUE si il s'agit d'un robot
     */
    public static function isCrawler(){
	if ( preg_match('/(bot|spider|yahoo)/i', $_SERVER[ "HTTP_USER_AGENT" ] )){
	    return true;
	}
	return false;
    }

    
    /**	Permet la détection du navigateur
     * 
     */
    private static function detectBrowser() {
	require_once(JinCore::getJinRootPath() . '_extlibs/phpbrowscap/Browscap.php');
	$bc = new \phpbrowscap\Browscap(JinCore::getJinRootPath() . '_extlibs/phpbrowscap/cache/');
	self::$browserData = $bc->getBrowser();
    }

}
