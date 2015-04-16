<?php

namespace jin\output\webapp\context;

use jin\output\webapp\template\TemplateManager;

class Output{
    
    private static $vars;
    
    public static function addTemplate($code){
        TemplateManager::addTemplate($code);
    }
    
    public static function set($key, $value){
        self::$vars[$key] = $value;
    }
    
    public static function get($key, $nullIfUndefined = true){
        if(!isset(self::$vars[$key])){
            if($nullIfUndefined){
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