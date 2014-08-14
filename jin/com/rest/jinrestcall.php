<?php

namespace jin\com\rest;

use jin\com\Curl;
use jin\lang\StringTools;
use jin\dataformat\Json;
use jin\log\Debug;

class JinRestCall{
    private $secured = false;
    private $publicKey;
    private $privateKey;
    private $url;
    private $args;
    private $method;
    private $throwError = true;
    
    public function __construct($url, $args = NULL, $method = 'POST') {
	$this->url = $url;
	
	if(!is_null($args) && $method != 'POST'){
	    foreach($args as $a => $v){
		if(!is_string($v)){
		    $args[$a] = (String)$v;
		}
	    }
	}else if(!is_null($args) && $method == 'POST'){
	    foreach($args as $key=>$value){
		if(is_bool($value) ){
		    $args[$key] = ($value) ? 'true' : 'false';
		}
	    }
	}
	
	$this->args = $args;
	$this->method = $method;
    }
    
    public function setSecured($publicKey, $privateKey){
	$this->secured = true;
	$this->publicKey = $publicKey;
	$this->privateKey = $privateKey;
    }
    
    public function setErrorThrowed($etat){
	$this->throwError = true;
    }
    
    public function call(){
	
	$plus = '';
	if($this->secured){
	    if(StringTools::contains($this->url, '?')){
		$plus = '&secure='.$this->getHMAC().'&publickey='.$this->publicKey;
	    }else{
		$plus = '?secure='.$this->getHMAC().'&publickey='.$this->publicKey;
	    }
	    
	}
	
	if($this->method == 'POST'){
	}else{
	    if($this->args){
		foreach($this->args as $arg => $val){
	
		    if($plus == '' && !StringTools::contains($this->url, '?')){
			$plus .= '?'.$arg.'='.$val;
		    }else{
			$plus .= '&'.$arg.'='.$val;
		    }
		}
	    }
	    $this->args = array();
	}
	
	$results = Curl::call($this->url.$plus, $this->args, $this->method, $this->throwError);
	return $results;
    }
    
    public function getLastErrorCode(){
	return Curl::getLastErrorCode();
    }
    
    public function getLastErrorVerbose(){
	return Curl::getLastErrorVerbose();
    }
    
    public function getLastHttpCode(){
	return Curl::getLastHttpCode();
    }
    
    public function getLastHttpCodeVerbose(){
	return Curl::getLastHttpCodeVerbose();
    }
    
    private function getHMAC() {
	$toEncode = $this->method;
	$toEncode .= $this->publicKey;
	if ($this->args) {
	    $toEncode .= Json::encode($this->args, true);
	} else {
	    $toEncode .= Json::encode(array());
	}

	return StringTools::hmac($toEncode, $this->privateKey, 'sha256');
    }

}
