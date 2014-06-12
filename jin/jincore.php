<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin;

use jin\filesystem\IniFile;

/** Méthodes de bas niveau du framework
 * 	@auteur		Loïc Gerard
 * 	@check		27/03/2014
 */
class JinCore {
    
    /**	Configuration JIN
     * @var array
     */
    private static $config;

    
    /** Fonction appelée automatiquement à chaque besoin d'une classe par le système
     * 
     * 	@param	$className  string  Chemin de la classe
     */
    public static function autoload($className) {

	$tab = explode('\\', $className);
	$path = strtolower(implode(DIRECTORY_SEPARATOR, $tab)) . '.php';
	
	$surcharge = self::getProjectRoot() . self::getConfigValue('surchargeAbsolutePath') . '/' . str_replace('jin/', '', $path);
	
	if(self::getConfigValue('surcharge') && file_exists($surcharge)){
	    //Surcharge
	    $path = $surcharge;
	}else{
	    //Fichier natif
	    $path = str_replace('jin/jincore.php', '', __FILE__) . $path;
	}
	
	if (is_file($path)) {
	    require($path);
	}
    }
    
    
    /**	Retourne le chemin absolu de la racine de la librairie Jin
     * 
     * @return string	Chemin absolu de la racine de la librairie Jin
     */
    public static function getRoot(){
	return str_replace('jincore.php', '', __FILE__);
    }
    
    
    /**	Retourne le chemin absolu du dossier contenant le framework Jin
     * 
     * @return string	Chemin absolu de le dossier contenant la librairie Jin
     */
    public static function getProjectRoot(){
	return str_replace('jin/jincore.php', '', __FILE__);
    }
    
    
    /**	Retourne la valeur d'une variable de configuration Jin (défini dans le fichier config.ini)
     * 
     * @param string $configParam   Nom de la variable de configuration
     * @return string
     */
    public static function getConfigValue($configParam){
	if(is_null(self::$config)){
	    self::$config = new IniFile(self::getRoot() . 'config.ini');
	}
	
	return self::$config->get($configParam);
    }
    
    
    /**	Retourne le chemin relatif des assets Jin
     * 
     * @return string	Chemin relatif
     */
    public static function getRelativePathAssets(){
	return '_assets/';
    }
    
    
    /**	Retourne le chemin relatif des fichiers de langue
     * 
     * @return string	Chemin relatif
     */
    public static function getRelativePathLanguage(){
	return '_languages/';
    }

}
