<?php

namespace jin\output\webapp\request;

use jin\output\webapp\request\Argument;
use \jin\JinCore;

class Request{
    private static $args = array();
    
    public function __construct() {
        foreach($_GET AS $k => $v){
            self::$args[$k] = new Argument($k, 'GET', $v);
        }
        foreach($_POST AS $k => $v){
            self::$args[$k] = new Argument($k, 'POST', $v);
        }
        foreach($_FILES AS $k => $v){
            self::$args[$k] = new Argument($k, 'FILES', $v);
        }
    }
    
    public static function getArgument($name, $nullIfNotExists = false){
        if(isset(self::$args[$name])){
            return self::$args[$name];
        }else if($nullIfNotExists){
            return null;
        }else{
            throw new \Exception('Argument '.$name.' inexistant.');
        }
    }
    
    public static function getArgumentValue($name, $nullIfNotExists = false){
        if(isset(self::$args[$name])){
            return self::$args[$name]->getValue();
        }else if($nullIfNotExists){
            return null;
        }else{
            throw new \Exception('Argument '.$name.' inexistant.');
        }
    }
    
    public static function setArgumentValue($name, $value, $createIfNotExists = true, $argTypeIfCreated = 'POST'){
        if(isset(self::$args[$name])){
            self::$args[$name]->setValue($value);
        }else if($createIfNotExists){
            self::$args[$name] = new Argument($name, $argTypeIfCreated, $value);
        }else{
            throw new \Exception('Argument '.$name.' inexistant.');
        }
    }
    
    public static function getRequestMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function getUrl(){
        $args = '';
        $first = true;
        foreach(self::$args AS $k => $v){
            if($v->getType() == 'GET'){
                if($first){
                    $args .= '?';
                    $first = false;
                }else{
                    $args .= '&';
                }
                $args .= $k.'='.$v->getValue();
            }
        }
        
        return JinCore::getContainerUrl().$args;
    }
    
    public static function getAllArguments(){
        return self::$args;
    }
    
    public static function getAllPostArguments(){
        return self::getArgumentsByType('POST');
    }
    
    public static function getAllGetArguments(){
        return self::getArgumentsByType('GET');
    }
    
    public static function getAllFilesArguments(){
        return self::getArgumentsByType('FILES');
    }
    
    private static function getArgumentsByType($type){
        $args = array();
        foreach(self::$args AS $a){
            if($a->getType() == $type){
                $args[$a->getName()] = $a->getValue();
            }
        }
        return $args;
    }
}
