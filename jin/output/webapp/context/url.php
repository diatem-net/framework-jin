<?php

namespace jin\output\webapp\context;

use jin\lang\StringTools;
use jin\lang\ArrayTools;
use jin\log\Debug;
use jin\JinCore;
use jin\output\webapp\request\Request;

/**
 * Classe permettant la génération d'Urls
 */
class Url{
    /**
     * Modèle d'Url par défaut. (%a% désigne par ex. un argument a)
     * @var string
     */
    private static $defaultUrlPattern = '';
    
    
    /**
     * Arguments détectés dans le modèle d'Url par défaut
     * @var array
     */
    private static $defaultUrlPatternArgs = array();
    
    
    /**
     * Modifie le modèle d'Url par défaut. (%a% désigne par ex. un argument a)
     * @param string $urlPattern
     */
    public static function setDefaultUrlPattern($urlPattern){
        self::$defaultUrlPattern = $urlPattern;
        self::$defaultUrlPatternArgs = self::analyseUrlPattern($urlPattern);
    }
    
    
    /**
     * Construit une Url
     * @param string $urlPattern        Modèle d'url ou base de l'Url. (%a% désigne par ex. un argument a) NULL indique que c'est le modèle d'Url par défaut qui sera utilisé
     * @param array $arguments          Arguments à transmettre en GET array('nomArgument' => 'valeurArgument')
     * @param boolean $absolute         Url absolue (par défaut) ou relative
     * @param string $anchor            Ancre (null par défaut)
     * @return string
     */
    public static function getUrl($urlPattern = null, $arguments = array(), $absolute = true, $anchor = null){
        return self::buildUrl($urlPattern, $arguments, $absolute, false, array(), $anchor);
    }
    
    
    /**
     * Retourne l'Url courante modifiée selon les paramètres transmis
     * @param string $urlPattern        Modèle d'url ou base de l'Url. (%a% désigne par ex. un argument a) NULL indique que c'est le modèle d'Url par défaut qui sera utilisé
     * @param array $addedArguments     Arguments supplémentaires à transmettre en GET array('nomArgument' => 'valeurArgument')
     * @param array $ignoredArguments   Arguments GET à ignorer array('nomArgument1', 'nomArgument2')
     * @param boolean $absolute         url absolue (par défaut) ou relative
     * @param string $anchor            Ancre (null par défaut)
     * @return string
     */
    public static function getCurrentUrl($urlPattern = null, $addedArguments = array(), $ignoredArguments = array(), $absolute = true, $anchor = null){
        return self::buildUrl($urlPattern, $addedArguments, $absolute, true, $ignoredArguments, $anchor);
    }
    
    
    /**
     * Construit une Url
     * @param string $urlPattern        Modèle d'url ou base de l'Url. (%a% désigne par ex. un argument a) NULL indique que c'est le modèle d'Url par défaut qui sera utilisé
     * @param array $arguments          Arguments à transmettre en GET array('nomArgument' => 'valeurArgument')
     * @param boolean $absolute         url absolue (par défaut) ou relative
     * @param boolean $fromCurrent      si TRUE on détermine certains paramètres de l'Url courante
     * @param array $ignoredArguments   Arguments GET à ignorer array('nomArgument1', 'nomArgument2') (Appliqué uniquement si $fromCurrent = TRUE)
     * @param string $anchor            Ancre (null par défaut)
     * @return string
     */
    private static function buildUrl($urlPattern = null, $arguments = array(), $absolute = true, $fromCurrent = false, $ignoredArguments = array(), $anchor = null){
        $url = '';
        $urlPatternArgs = array();
        
        if($absolute){
            $url = JinCore::getContainerUrl();
            if(StringTools::right($url, 1) != '/'){
                $url .= '/';
            }
            if(StringTools::right($url, 2) == '//'){
                $url = StringTools::left($url, StringTools::len($url)-1);
            }
        }
        
        
        if($fromCurrent){
            $arguments = ArrayTools::merge(Request::getAllGetArguments(), $arguments);
        }
        
        if($urlPattern){
            $urlPatternArgs = self::analyseUrlPattern($urlPattern);
            $url .= self::executeUrlPattern($urlPattern, $urlPatternArgs, $arguments);
        }else{
            $urlPatternArgs = self::$defaultUrlPatternArgs;
            $url .= self::executeUrlPattern(self::$defaultUrlPattern, self::$defaultUrlPatternArgs, $arguments);
        }
        
        $first = true;
        foreach($arguments AS $k => $v){
            if(ArrayTools::find($urlPatternArgs, '%'.$k.'%') === false 
                    && (
                        !$fromCurrent
                        ||
                        ArrayTools::find($ignoredArguments, $k) === false
                    )){
                if($first){
                    $url .= '?';
                    $first = false;
                }else{
                    $url .= '&';
                }
                $url .= $k.'='.urlencode($v);
            }
        }
        
        if($anchor){
            $url .= '#'.$anchor;
        }
        
        return $url;
    }
    
    
    /**
     * Analyse un modèle d'Url pour retourner les arguments constritutifs
     * @param string $urlPattern    Modèle d'Url à analyser
     * @return array
     */
    private static function analyseUrlPattern($urlPattern){
        $retour = array();
        $matches = StringTools::getMatches($urlPattern, '/%.*?%/');
        foreach($matches AS $m){
            $retour[] = $m[0];
        }
        return $retour;
    }
    
    
    /**
     * Applique un modèle d'Url sur une Url
     * @param string $urlPattern        Modèle d'Url
     * @param array $urlPatternArgs     Arguments constitutifs du modèle
     * @param array $arguments          Arguments GET de l'Url
     * @return string
     */
    private static function executeUrlPattern($urlPattern, $urlPatternArgs, $arguments){
        $out = $urlPattern;
        foreach($urlPatternArgs AS $a){
            $argName = StringTools::replaceAll($a, '%', '');
            if(isset($arguments[$argName])){
                $out = StringTools::replaceAll($out, $a, $arguments[$argName]);
            }else{
                $out = StringTools::replaceAll($out, $a, '');
            }
        }
        return $out;
    }
}