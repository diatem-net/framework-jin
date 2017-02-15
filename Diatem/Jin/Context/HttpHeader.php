<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Context;

/**
 * Gestion des en-tpete HTTP
 */
class HttpHeader
{
    /**
     * Envoie un header de type 404 (non trouvé)
     * @param string $message   Message spécifique. (Par défaut : 404 - Not found)
     */
    public static function return404($message = '404 - Not Found')
    {
        header('HTTP/1.0 404 '.$message);
    }


    /**
     * Redirection 301 : ressource déplacée de manière permanente.
     * @param string $newLocation   Url de destination
     */
    public static function redirect301($newLocation)
    {
        header('Location: '.$newLocation, true, 301);
        exit;
    }


    /**
     * Redirection 302 : ressource déplacée de manière temporaire
     * @param string $newLocation   Url de destination
     */
    public static function redirect302($newLocation)
    {
        header('Location: '.$newLocation, true, 302);
        header('Location: '.$newLocation);
    }
}
