<?php

namespace jin\output\webapp\utils;

use jin\lang\StringTools;
use jin\output\webapp\request\Request;
use jin\output\webapp\request\Routeur;
use jin\lang\ArrayTools;
use jin\output\webapp\context\Url AS JinUrl;

class Navigation{
    private static $initialized = false;
    
    public static function clearQueryArg($code){
        if(StringTools::left($code, 1) == '/'){
            $code = StringTools::right($code, StringTools::len($code)-1);
        }
        if(StringTools::right($code, 1) == '/'){
            $code = StringTools::left($code, StringTools::len($code)-1);
        }
        
        return $code;
    }
    
    public static function redirectTo($code, $addedArgs = array(), $anchor = null){
        self::initialize();
       
        
        header('Location: '.self::getUrlFromCode($code, $addedArgs, $anchor));
        exit;
    }
    
    public static function redirectToSame($exceptedArgs = array(), $addedArgs = array(), $anchor = null){
        self::initialize();
        
        header('Location: '.self::getCurrentUrl($exceptedArgs, $addedArgs, $anchor));
        exit;
    }
    
    public static function getUrlFromCode($code, $addedArgs = array(), $anchor = null){
        self::initialize();
        
        $code = self::clearQueryArg($code);
        
        if($code == '_root'){
            return BASE_URL;
        }
        
        if(StringTools::right($code, 1) != '/'){
            $code .= '/';
        }
        
        $args = array('q' => $code);
        $args = ArrayTools::merge($args, $addedArgs);
        return JinUrl::getUrl(null, $args, true, $anchor);
    }
    
    public static function getCurrentUrl($exceptedArgs = array(), $addedArgs = array(), $anchor = null){
        self::initialize();
        
        return JinUrl::getCurrentUrl(null, $addedArgs, $exceptedArgs, true, $anchor);
    }
    
    
    private static function initialize(){
        if(!self::$initialized){
            JinUrl::setDefaultUrlPattern('%q%');
            self::$initialized = true;
        }
    }
    
   
}