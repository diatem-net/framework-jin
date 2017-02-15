<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Ressources;

use Diatem\Jin\Output\Ressources\GlobalLoader;
use Diatem\Jin\Output\Ressources\Optimizer\CssMinify;
use Diatem\Jin\Jin;

/** Permet l'automatisation du chargement de fichiers CSS et la gestion auto d'un Minifer et du cache.
 * @auteur  Loïc Gerard
 */
class CssLoader extends GlobalLoader{
   /**
    *
    * @var boolean Minify activé ou non
    */
    private $minify;


    /**
     * Constructeur
     * @param string $uniqueId	Identifiant unique du pack de CSS (pour gestion du cache). Ne pas mettre pour deux usages différents le même identifiant.
     * @param boolean $minify	[optionel] usage ou non de la compression de données (TRUE par défaut)
     */
    public function __construct($uniqueId, $minify = true){
	$this->minify = $minify;
	parent::__construct($uniqueId);
    }


    /**
     * Retourne le code HTML à ajouter dans le HEAD
     * @return string
     */
    public function getHTMLCode(){
	parent::getHTMLLink();
	return '<link rel="stylesheet" href="'.Jin::getJinUrl().'_script/ressource/css.php?uid='.parent::getKey().'">';
    }


    /**
     * Génère le contenu à partir des fichiers et le pousse en cache
     */
    protected function generateContentInCache(){
	$fcontent = parent::generateContent();
	if($this->minify){
	    $fcontent = CssMinify::cssMinify($fcontent);
	}
	parent::saveContentInCache($fcontent);
    }
}

