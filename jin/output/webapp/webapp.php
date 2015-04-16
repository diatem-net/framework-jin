<?php

namespace jin\output\webapp;

use jin\output\webapp\request\Routeur;
use jin\output\webapp\request\Request;
use jin\JinCore;
use jin\lang\StringTools;

class WebApp {

    private static $appFolder;
    private static $pageFolder = 'pages';
    private static $templateFolder = 'templates';
    private static $cacheFolder = 'cache';
    public static $routeur;
    public static $page;
    
    public static function init($appFolder) {
        self::$appFolder = $appFolder;
        self::$routeur = new Routeur();
        
        self::$page->beforeRender();
        echo self::$page->render();
        self::$page->afterRender();
    }
    
    public static function getRootFolder(){
        $folder = JinCore::getContainerPath().self::$appFolder;
        if(StringTools::right($folder, 1) != '/'){
            $folder .= '/';
        }
        
        return $folder;
    }
    
    public static function getPagesFolder(){
        $folder = self::getRootFolder().self::$pageFolder;
        if(StringTools::right($folder, 1) != '/'){
            $folder .= '/';
        }
        
        return $folder;
    }
    
    public static function getCacheFolder(){
        $folder = self::getRootFolder().self::$cacheFolder;
        if(StringTools::right($folder, 1) != '/'){
            $folder .= '/';
        }
        
        return $folder;
    }
    
    public static function getTemplateFolder(){
        $folder = self::getRootFolder().self::$templateFolder;
        if(StringTools::right($folder, 1) != '/'){
            $folder .= '/';
        }
        
        return $folder;
    }

}
