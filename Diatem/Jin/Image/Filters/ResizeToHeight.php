<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Image\Filters;

use Diatem\Jin\Image\FilterInterface;
use Diatem\Jin\Image\ImageFilter;
use Diatem\Jin\Image\Image;

/**
 * Filtre image. Permet de redimentionner une image sans déformation en spécifiant
 * une hauteur fixe de sortie. La largeur sera variable
 *
 * @auteur     Loïc Gerard
 */
final class ResizeToHeight extends ImageFilter implements FilterInterface{
    /**
     *
     * @var integer Hauteur souhaitée
     */
    private $height;


    /**
     * Constructeur
     * @param integer $height	Hauteur souhaitée
     */
    public function __construct($height) {
	parent::__construct();
	$this->height = $height;
    }


     /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource){
	$startWidth = imagesx($imageRessource);
	$startHeight = imagesy($imageRessource);

	$yratio = $startHeight/$this->height;

	//La hauteur de l'image fait foi. (Seule dimension permettant un redimentionnement sans perte de matière)
	//L'image fera la hauteur souhaitée. La largeur sera moindre et proportionnelle.

	$nouvelleHauteur = $this->height;
	$nouvelleLargeur = (($startWidth*(($nouvelleHauteur)/$startHeight)));

	$resized = $this->image->getEmptyContainer($nouvelleLargeur, $nouvelleHauteur);

	imagecopyresampled($resized, $imageRessource, 0, 0, 0, 0, $nouvelleLargeur, $nouvelleHauteur, $startWidth, $startHeight);
	imagedestroy($imageRessource);

	return $resized;
    }
}