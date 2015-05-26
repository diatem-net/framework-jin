<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\com;

use jin\log\Debug;
use jin\lang\StringTools;

/** Classe permettant de g�n�rer des appels CURL
 *
 * 	@auteur		Lo�c Gerard
 */
class Curl {

    /**
     *
     * @var string  Derni�re erreur rencontr�e
     */
    private static $lastErrorText = '';
    
    /**
     *
     * @var int	Dernier code d'erreur rencontr�
     */
    private static $lastErrorCode = 0;

    
    private static $lastCurlInfo;
    
    /**
     * Appelle une Url
     * @param string        $url                Url � appeler
     * @param array|string  $args               [optional] Arguments � transmettre (NULL par d�faut)
     * @param string        $requestType        [optionel] Type de requ�te. (POST,GET, DELETE ou PUT) (POST par d�faut)
     * @param boolean       $throwError         [optionel] G�n�re les erreurs directement (True par d�faut)
     * @param string        $contentType        [optionel] Header 'Content-type'. Par d�faut : application/json
     * @param array         $headers            [optionel] Headers additionnels. (Sous la forme d'un tableau cl�/valeur)
     * @param string        $outputTraceFile    [optionel] Si renseign� : effectue une trace des flux r�seaux dans le fichier ainsi d�termin�. (Chemin absolu du fichier)
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
	    if(!StringTools::contains($url, '?') && !empty($args)){
		$url .= '?' . http_build_query($args);
	    }else if(!empty($args)){
		$url .= '&' . http_build_query($args);
	    }
	    curl_setopt($curl, CURLOPT_URL, $url);
	}

        //Get
	if ($requestType == 'GET') {
	    if(!StringTools::contains($url, '?') && !empty($args)){
		$url .= '?' . http_build_query($args);
	    }else if(!empty($args)){
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
            if(!empty($args)){
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($args));
            }
            
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
     * Retourne le dernier code d'erreur rencontré
     * @return int
     */
    public static function getLastErrorCode() {
	return self::$lastErrorCode;
    }

    
    /**
     * Retourne la derni�re erreur rencontr�e (verbose)
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
     * Retourne le d�tail textuel du dernier code HTTP retourn�
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
