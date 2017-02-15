<?php

namespace Diatem\Jin\Output\WebApp\Template;

use Diatem\Jin\Output\WebApp\WebApp;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\FileSystem\File;
use Diatem\Jin\Jin;
use Diatem\Jin\Output\WebApp\Context\Output;

class Template{
    private $templateFolder;
    private $templateName;

    public function __construct($templateName) {
        $this->templateName = $templateName;
        $this->templateFolder = WebApp::getTemplateFolder().$templateName;
        if(StringTools::right($this->templateFolder, 1) != '/'){
            $this->templateFolder .= '/';
        }
    }

    public function render($enveloppe){
        $f = new File($this->templateFolder.'template.tpl');
        $content = $f->getContent();
        $content = $this->replaceIncludes($content);
        $content = $this->replaceCustomWords($content);
        $content = $this->replaceMagicWords($content);

        $enveloppe = StringTools::replaceAll($enveloppe, '#content#', $content);
        return $enveloppe;
    }

    private function replaceIncludes($content){
        $matches = StringTools::getMatches($content, '/#include::.*?#/');

        $nb = count($matches);
        for ($i = 0; $i < $nb; $i++) {
            $fileName = $matches[$i][0];
            $fileName = StringTools::replaceAll($fileName, '#include::', '');
            $fileName = StringTools::replaceAll($fileName, '#', '');
            if(!is_file(Jin::getAppPath().$fileName)){
                throw new \Exception('Fichier include:: introuvable : '.$fileName);
            }
            ob_start();
            include Jin::getAppPath().$fileName;
            $blocContent = ob_get_contents();
            ob_clean();

            $content = StringTools::replaceAll($content, $matches[$i][0], $blocContent);
	}

        return $content;
    }

    private function replaceCustomWords($content){
        $vars = Output::getAllVars();
        foreach($vars AS $k => $v){
            $content = StringTools::replaceAll($content, '#custom::'.$k.'#', $v);
        }

        return $content;
    }


    private function replaceMagicWords($content){
        $content = StringTools::replaceAll($content, '#magic::url#', BASE_URL);
        $content = StringTools::replaceAll($content, '#magic::root#',ROOT_PATH);

        return $content;
    }
}
