<?php

namespace Diatem\Jin\Output\WebApp;

use Diatem\Jin\Output\WebApp\Request\Routeur;
use Diatem\Jin\Output\WebApp\Request\Request;
use Diatem\Jin\Jin;
use Diatem\Jin\Lang\StringTools;

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

        self::$page->onInit();
        self::$page->onPost();
        self::$page->beforeRender();
        echo self::$page->render();
        self::$page->afterRender();
    }

    public static function getRootFolder(){
        $folder = Jin::getAppPath().self::$appFolder;
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
