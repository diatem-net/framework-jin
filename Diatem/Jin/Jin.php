<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin;

use Diatem\Jin\FileSystem\IniFile;
use Diatem\Jin\Log\Debug;
use Diatem\Jin\Lang\StringTools;

/**
 * Classe de base pour charger le framework JIN
 */
class Jin
{

    /**
     * Configuration JIN
     * @var array
     */
    private static $config = array(
        /* Chemin absolu du dossier contenant les fichiers surchargeant */
        'surchargeAbsolutePath' => 'surcharge/',
        /* Dossier d'installation de Jin (relatif à la racine du projet) */
        'jinPath' => 'vendor/diatem-net/framework-jin/',
        /* Mode de cache (memcache ou file) */
        'cacheMode' => 'file',
        /* Cache de type FILE : dossier de stockage */
        'cacheFileFolder' => 'cache/',
        /* Cache de type MEMCACHE */
        'cacheMemCacheHost' => 'localhost',
        'cacheMemCachePort' => 11211,
        /* Nombre de secondes pendant lequel le navigateur client doit conserver le cache sur les fichiers Js et CSS gérés par JIN */
        'RessourceNavigatorCacheTime' => 604800
    );


    /**
     * Url racine de JIN
     * @var string
     */
    private static $jinUrl;

    /**
     * Chemin racine de JIN
     * @var string
     */
    private static $jinPath;

    /**
     * Retourne le chemin absolu du dossier contenant le framework Jin
     * @return string   Chemin absolu de le dossier contenant la librairie Jin
     */
    public static function getAppPath()
    {
        return rtrim(StringTools::replaceAll(self::getJinPath(), self::getConfigValue('jinPath'), ''), '/') . '/';
    }

    /**
     * Retourne l'url de la racine du projet
     * @return string
     */
    public static function getAppUrl()
    {
        $url = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $url .= 's';
        }
        $url .= '://' . $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != '80') {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }
        return $url . '/' . trim(StringTools::replaceAll(self::getAppPath(), $_SERVER['DOCUMENT_ROOT'], ''), '/') . '/';
    }

    /**
     * Retourne le chemin absolu de la racine de la librairie Jin
     * @return string   Chemin absolu de la racine de la librairie Jin
     */
    public static function getJinPath()
    {
        $namespace = StringTools::replaceAll(__NAMESPACE__, '\\', '/');
        return rtrim(StringTools::replaceAll(__DIR__, $namespace, ''), '/') . '/';
    }

    /**
     * Retourne l'url de la racine de la librairie Jin
     * @return string
     */
    public static function getJinUrl()
    {
        return self::getAppUrl() . trim(self::getConfigValue('jinPath'), '/') . '/';
    }

    /**
     * Retourne le chemin relatif (à partir de la racine du projet) du dossier de surcharge
     */
    public static function getSurchargeRelativePath()
    {
        return self::getConfigValue('surchargeAbsolutePath');
    }

    /**
     * Retourne la valeur d'une variable de configuration Jin (défini dans le fichier config.ini)
     * @param string $key   Nom de la variable de configuration
     * @return string
     */
    public static function getConfigValue($key)
    {
        if (!array_key_exists($key, self::$config)) {
            throw new \Exception(sprintf('Unknown Jin configuration key: «%s».', $key));
            return null;
        }
        return self::$config[$key];
    }

    /**
     * Retourne le chemin relatif des assets Jin
     * @return string   Chemin relatif
     */
    public static function getRelativePathAssets()
    {
        return '_assets/';
    }

    /**
     * Retourne le chemin relatif des librairies externes
     * @return string   Chemin relatif
     */
    public static function getRelativePathExtLibs()
    {
        return '_extlibs/';
    }

    /**
     * Retourne le chemin relatif des fichiers de langue
     * @return string   Chemin relatif
     */
    public static function getRelativePathLanguage()
    {
        return '_languages/';
    }

}
