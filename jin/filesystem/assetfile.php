<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\filesystem;

use jin\filesystem\File;
use jin\lang\StringTools;
use jin\JinCore;

/** Permet d'utiliser des Assets, autrement dit des composants graphiques simples pouvant être rendus n'importe ou.
 * <br>Ils peuvt être soit des composant définis par Jin par défaut, soit des composants ajoutés. (Via le dossier surcharge)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		22/04/2014
 */
class AssetFile {

    /**	Fichier
     *
     * @var jin\filesystem\File
     */
    private $file;
    
    
    /**	Url relative
     *
     * @var string
     */
    private $url;
    
    

    
    /**	Constructeur
     * 
     * @param string $relativePath	    Chemin relatif
     * @param boolean $surchargeAllowed	    [optionel] Définit si la surcharge est autorisée (TRUE par défaut)
     */
    public function __construct($relativePath, $surchargeAllowed = true) {
	$surcharge = JinCore::getProjectRoot() . JinCore::getConfigValue('surchargeAbsolutePath') . '/' . JinCore::getRelativePathAssets() . $relativePath;

	if(JinCore::getConfigValue('surcharge') && file_exists($surcharge)){
	    //Surcharge du fichier
	     $this->file = new File($surcharge);
	     $this->url = 'http://'.$_SERVER['HTTP_HOST'].'/surcharge/_assets/';
	}else{
	    //Fichier natif
	    $this->file = new File(JinCore::getRoot() . JinCore::getRelativePathAssets() . $relativePath);
	    $this->url = 'http://'.$_SERVER['HTTP_HOST'].'/_assets/';
	}
    }

    
    /**	Retourne le contenu HTML généré
     * 
     * @return string
     */
    public function getContent() {
	$content = $this->file->getContent();
	$content = StringTools::replaceAll($content, '%asseturl%', $this->url);
	$content = StringTools::replaceAll($content, '%url%', 'http://'.$_SERVER['HTTP_HOST']);
	return $content;
    }

}
