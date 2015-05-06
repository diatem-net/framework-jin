<?php

namespace jin\output\webapp\context;

use jin\output\webapp\WebApp;

class View{
    private $file;
    
    public function __construct($file) {
        $this->file = $file;
    }
    
    public function getFile(){
        return $this->file;
    }
    
    public function executeAndReturnContent(){
        ob_start();
        Output::$controller = WebApp::$page->controller;
        include $this->file;
        $content = ob_get_contents();
        ob_clean();
        
        return $content;
    }
}