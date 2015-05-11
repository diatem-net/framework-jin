<?php

namespace jin\output\webapp\context;

use jin\output\webapp\template\TemplateManager;

class Output{
    
    private static $vars;
    public static $controller;
    
    public static function addTemplate($code){
        TemplateManager::addTemplate($code);
    }
    
    public static function set($key, $value){
        self::$vars[$key] = $value;
    }
    
    public static function addTo($key, $valueToAdd){
        if(self::$vars[$key]){
            self::$vars[$key] .= $valueToAdd;
        }else{
            self::$vars[$key] = $valueToAdd;
        }
    }
    
    public static function get($key, $nullIfUndefined = true, $defaultValueIfUndefined = null){
        if(!isset(self::$vars[$key])){
            if($nullIfUndefined){
                if($defaultValueIfUndefined){
                    return $defaultValueIfUndefined;
                }
                return null;
            }else{
                throw new \Exception('Valeur '.$key.' non définie');
            }
        }else{
            return self::$vars[$key];
        }
    }
    
    public static function getAllVars(){
        return self::$vars;
    }
}