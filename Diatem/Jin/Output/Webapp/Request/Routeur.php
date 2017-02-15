<?php

namespace Diatem\Jin\Output\WebApp\Request;

use Diatem\Jin\Output\WebApp\Request\Request;
use Diatem\Jin\Output\WebApp\WebApp;
use Diatem\Jin\Output\WebApp\Context\Page;
use Diatem\Jin\Context\HttpHeader;

class Routeur{
    private static $rootArgumentName = 'q';

    public static function setRootArgumentName($rootArgumentName){
        self::$rootArgumentName = $rootArgumentName;
    }

    public static function getRootArgumentName(){
        return self::$rootArgumentName;
    }

    public function __construct() {
        if(!Request::getArgumentValue(self::$rootArgumentName, true) || Request::getArgumentValue(self::$rootArgumentName, true) == 'index.html'){
            $this->rootToIndex();
        }else{
            if(is_dir(WebApp::getPagesFolder().Request::getArgumentValue(self::$rootArgumentName))){
                $this->rootToPage(Request::getArgumentValue(self::$rootArgumentName));
            }else{
                $this->rootTo404();
            }
        }
    }

    private function rootToIndex(){
        if(is_dir(WebApp::getPagesFolder().'_root/')){
            WebApp::$page = new Page('_root', Request::getRequestMethod());
        }else{
            throw new \Exception('Page root introuvable (_root)');
        }
    }

    private function rootTo404(){
        if(is_dir(WebApp::getPagesFolder().'_404/')){
            WebApp::$page = new Page('_404', Request::getRequestMethod());
        }else{
            HttpHeader::return404();
            throw new \Exception('Page 404 introuvable (_404)');
        }
    }


    private function rootToPage($page){
        WebApp::$page = new Page($page, Request::getRequestMethod());
    }



}
