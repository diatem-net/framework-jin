<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\com;

/** Classe permettant de gérer des appels CURL
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Curl {

    /**
     *
     * @var string  Dernière erreur rencontrée
     */
    private static $lastErrorText = '';
    
    /**
     *
     * @var int	Dernier code d'erreur rencontré
     */
    private static $lastErrorCode = 0;

    
    /** 	Permet d'appeler un script en lui transmettant des arguments en POST
     * 
     * @param string $url	    Url du script distant à appeler. (Ex. http://serveur.com/fichier.php)
     * @param array $args	    [optionel] Tableau associatif des arguments à transmettre au script. (Ex. array('username' => $login, 'password' => $password) ) A noter : si NULL est transmis l'appel s'effectue en GET, si un array est transmis, même vide, l'appel s'effectue en POST. (NULL par défaut).
     * @param boolean $throwError   [optionel] Définit si la méthode doit générer une exception en cas d'impossibilité d'appel. (TRUE par défaut)
     * * @return	mixed	    Contenu généré par le script appelé ou FALSE en cas d'erreur
     */
    public static function call($url, $args = null, $throwError = true, $customRequest = null) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	if ($customRequest) {
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $customRequest);
	}

	if (!is_null($args)) {
	    //Mode POST
	    curl_setopt($curl, CURLOPT_POST, true);
	    //On transmet les arguments
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
	}

	$return = curl_exec($curl);

	if (!$return) {
	    $errornum = curl_errno($curl);
	    $errortxt = 'Impossible d\'appeler l\'url : ' . $url . ' : (ERREUR CURL ' . $errornum . ') ' . curl_error($curl);
	    if ($throwError) {
		throw new \Exception($errortxt);
	    }
	    self::$lastErrorText = $errortxt;
	    self::$lastErrorCode = $errornum;


	    return false;
	}
	curl_close($curl);

	return $return;
    }

    
    /**
     * Retourne le dernier code d'erreur rencontré
     * @return int
     */
    public static function getLastErrorCode() {
	return self::$lastErrorCode;
    }

    
    /**
     * Retourne la dernière erreur rencontrée (verbose)
     * @return string
     */
    public static function getLastErrorVerbose() {
	return self::$lastErrorText;
    }

}
