<?php

namespace jin\output\webapp\context;

class DefaultController{
    public function onInit(){
        
    }
    
    public function beforeRender(){
    }
    
    public function render($content){
        return $content;
    }
    
    public function afterRender(){
    }
}

