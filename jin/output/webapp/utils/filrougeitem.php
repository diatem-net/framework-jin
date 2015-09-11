<?php

namespace jin\output\webapp\utils;

use jin\output\webapp\utils\Navigation;
use jin\output\webapp\request\Request;

class FilRougeItem{
    private $label;
    private $urlCode;
    private $url;
    private $addedArgs;
    
    public function __construct($label, $urlCode = null, $addedArgs = array()) {
        $this->label = $label;
        
        if($urlCode){
            $this->urlCode = $urlCode;
            $this->url = Navigation::getUrlFromCode($urlCode, $addedArgs);
            $this->addedArgs = $addedArgs;
        }
    }
    
    public function isSelected(){
        if(Navigation::clearQueryArg(Request::getArgumentValue('q')) == Navigation::clearQueryArg($this->urlCode)){
            return true;
        }
        return false;
    }
    
    public function isLinkable(){
        if($this->urlCode){
            return true;
        }
        return false;
    }
    
    public function getLabel(){
        return $this->label;
    }
    
    public function getCode(){
        return $this->urlCode;
    }
    
    public function getUrl(){
        return $this->url;
    }
}

