<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\com;

use jin\log\Debug;
use jin\lang\StringTools;

/** Classe permettant de générer des appels CURL
 *
 * 	@auteur		Loïc Gerard
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

    
    private static $lastCurlInfo;
    
    /**
     * Appelle une Url
     * @param string        $url                Url à  appeler
     * @param array|string  $args               [optional] Arguments à  transmettre (NULL par défaut)
     * @param string        $requestType        [optionel] Type de requête. (POST,GET, DELETE ou PUT) (POST par défaut)
     * @param boolean       $throwError         [optionel] Génère les erreurs directement (True par défaut)
     * @param string        $contentType        [optionel] Header 'Content-type'. Par défaut : application/json
     * @param array         $headers            [optionel] Headers additionnels. (Sous la forme d'un tableau clé/valeur)
     * @param string        $outputTraceFile    [optionel] Si renseigné : effectue une trace des flux réseaux dans le fichier ainsi déterminé. (Chemin absolu du fichier)
     * @return boolean
     * @throws \Exception
     */
    public static function call($url, 
            $args = null, 
            $requestType = 'POST', 
            $throwError = true, 
            $httpAuthUser = null, 
            $httpAuthPassword = null,
            $contentType = null,
            $headers = array(),
            $outputTraceFile = null){
        
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_COOKIESESSION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        //Set headers
        $h = array();
        if($contentType){
            $h[] = 'Content-type: '.$contentType;
        }
        if(!empty($headers)){
            foreach($headers AS $k => $v){
                $h[] = $k.': '.$v;
            }
        }
        if(!empty($h)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $h);
        }
        
        //Mode debug
        if(!empty($outputTraceFile)){
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            $verbose = fopen($outputTraceFile, 'w');
            curl_setopt($curl, CURLOPT_STDERR, $verbose);
        }
        
        //Authentification HTTP
        if(!is_null($httpAuthUser) && !is_null($httpAuthPassword)){
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $httpAuthUser.':'.$httpAuthPassword);
        }
	
	//Delete
	if ($requestType == 'DELETE') {
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
	    if(!StringTools::contains($url, '?')){
		$url .= '?' . http_build_query($args);
	    }else{
		$url .= '&' . http_build_query($args);
	    }
	    curl_setopt($curl, CURLOPT_URL, $url);
	}

        //Get
	if ($requestType == 'GET') {
	    if(!StringTools::contains($url, '?')){
		$url .= '?' . http_build_query($args);
	    }else{
		$url .= '&' . http_build_query($args);
	    }
	    curl_setopt($curl, CURLOPT_URL, $url);
	}

        //Post
	if($requestType == 'POST') {
	    curl_setopt($curl, CURLOPT_POST, TRUE);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $args);
	}

        //Put
	if ($requestType == 'PUT') {
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($args));
            
	}

	$return = curl_exec($curl);
        
	self::$lastCurlInfo = curl_getinfo($curl);
	
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
     * Retourne le dernier code d'erreur rencontrÃ©
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
    
    
    /**
     * Retourne le code HTTP de retour du dernier appel
     * @return string
     */
    public static function getLastHttpCode(){
        Debug::dump(self::$lastCurlInfo);
        
	if(isset(self::$lastCurlInfo['http_code'])){
	    return self::$lastCurlInfo['http_code'];
	}else{
	    return false;
	}
    }
    
    
    /**
     * Retourne le détail textuel du dernier code HTTP retourné
     * @return string
     */
    public static function getLastHttpCodeVerbose(){
	if(isset(self::$lastCurlInfo['http_code'])){
	    $status = array(
	    100 => 'Continue',
	    101 => 'Switching Protocols',
	    200 => 'OK',
	    201 => 'Created',
	    202 => 'Accepted',
	    203 => 'Non-Authoritative Information',
	    204 => 'No Content',
	    205 => 'Reset Content',
	    206 => 'Partial Content',
	    300 => 'Multiple Choices',
	    301 => 'Moved Permanently',
	    302 => 'Found',
	    303 => 'See Other',
	    304 => 'Not Modified',
	    305 => 'Use Proxy',
	    306 => '(Unused)',
	    307 => 'Temporary Redirect',
	    400 => 'Bad Request',
	    401 => 'Unauthorized',
	    402 => 'Payment Required',
	    403 => 'Forbidden',
	    404 => 'Not Found',
	    405 => 'Method Not Allowed',
	    406 => 'Not Acceptable',
	    407 => 'Proxy Authentication Required',
	    408 => 'Request Timeout',
	    409 => 'Conflict',
	    410 => 'Gone',
	    411 => 'Length Required',
	    412 => 'Precondition Failed',
	    413 => 'Request Entity Too Large',
	    414 => 'Request-URI Too Long',
	    415 => 'Unsupported Media Type',
	    416 => 'Requested Range Not Satisfiable',
	    417 => 'Expectation Failed',
	    500 => 'Internal Server Error',
	    501 => 'Not Implemented',
	    502 => 'Bad Gateway',
	    503 => 'Service Unavailable',
	    504 => 'Gateway Timeout',
	    505 => 'HTTP Version Not Supported');
	    return ($status[self::$lastCurlInfo['http_code']]) ? $status[self::$lastCurlInfo['http_code']] : $status[500];
	}else{
	    return false;
	}
    }

}
