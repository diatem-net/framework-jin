<?php

namespace jin\output\webapp\template;

use jin\output\webapp\template\Template;
use jin\output\webapp\WebApp;
use jin\lang\StringTools;

class TemplateManager{
    private static $templates = array();
    
    public static function addTemplate($templateCode){
        if(!is_dir(WebApp::getTemplateFolder().$templateCode)){
            throw new \Exception('La template '.$templateCode.' n\'existe pas.');
        }else{
            self::$templates[] = new Template($templateCode);
        }
    }
    
    public static function render($content){
        $temp = '#content#';
        foreach(self::$templates AS $template){
            $temp = $template->render($temp);
        }
        
        $temp = StringTools::replaceAll($temp, '#content#', $content);
        return $temp;
    }
}
