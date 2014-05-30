<?php

namespace jin\dataformat;

use jin\log\Debug;

class Xml{
    public static function xmlToArray($xmlData, $typeValues = false){
	$xml = simplexml_load_string($xmlData);
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	return $array;
    }
    
    public static function arrayToXml(){
	
    }
}

