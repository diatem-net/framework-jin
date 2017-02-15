<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\FileSystem;

use Diatem\Jin\FileSystem\File;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\Jin;
use Diatem\Jin\Log\Debug;

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
     * @var jin\FileSystem\File
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
     */
    public function __construct($relativePath) {
	$surcharge = Jin::getAppPath() . Jin::getSurchargeRelativePath() . '/' . Jin::getRelativePathAssets() . $relativePath;

	if(Jin::getConfigValue('surcharge') && file_exists($surcharge)){
	    //Surcharge du fichier
	    $this->file = new File($surcharge);
	    $this->url = Jin::getAppUrl() . Jin::getSurchargeRelativePath() . '/' . Jin::getRelativePathAssets();
	}else{
	    //Fichier natif
	    $this->file = new File(Jin::getJinPath() . Jin::getRelativePathAssets() . $relativePath);
	    $this->url = Jin::getJinUrl() . Jin::getRelativePathAssets();
	}
    }


    /**	Retourne le contenu HTML généré
     *
     * @return string
     */
    public function getContent() {
	$content = $this->file->getContent();
	$content = StringTools::replaceAll($content, '%asseturl%', $this->url);
	$content = StringTools::replaceAll($content, '%jinurl%', Jin::getJinUrl());
	return $content;
    }

}
