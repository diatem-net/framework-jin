<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\image\filters;

use jin\image\FilterInterface;
use jin\image\ImageFilter;
use jin\image\Image;

/**
 * Filtre image. Permet de redimentionner une image sans déformation en spécifiant
 * une largeur fixe de sortie. La hauteur sera variable
 * 
 * @auteur     Loïc Gerard
 */
final class ResizeToWidth extends ImageFilter implements FilterInterface{
    /**
     *
     * @var integer Largeur souhaitée
     */
    private $width;
    
    
    /**
     * Constructeur
     * @param integer $width	Largeur souhaitée
     */
    public function __construct($width) {
	parent::__construct();
	$this->width = $width;
    }
    
    
     /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource){
	$startWidth = imagesx($imageRessource);
	$startHeight = imagesy($imageRessource);
	
	$xratio = $startWidth/$this->width;
	
	$nouvelleLargeur = $this->width;
	$nouvelleHauteur = (($startHeight*(($nouvelleLargeur)/$startWidth)));
	
	$resized = $this->image->getEmptyContainer($nouvelleLargeur, $nouvelleHauteur);
	
	imagecopyresampled($resized, $imageRessource, 0, 0, 0, 0, $nouvelleLargeur, $nouvelleHauteur, $startWidth, $startHeight);
	imagedestroy($imageRessource);
	
	return $resized;
    }
}