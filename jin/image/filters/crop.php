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
 * Filtre image. Permet de croper une image sur des dimensions spécifiées
 * Cette méthode entraîne une perte de matière.
 */
final class Crop extends ImageFilter implements FilterInterface{
    /**
     *
     * @var integer Largeur souhaitée
     */
    private $width;
    
    
    /**
     *
     * @var integer Hauteur souhaitée
     */
    private $height;
    
    
    /**
     * Constructeur
     * @param integer $width	Largeur de l'image en sortie
     * @param integer $height	Hauteur de l'image en sortie
     */
    public function __construct($width, $height) {
	parent::__construct();
	$this->width = $width;
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
	
	if($startWidth == $this->width &&
		$startHeight == $this->height){
	    //Cas particulier. Image déjà à la bonne taille.
	    return $imageRessource;
	}
	
	// On calcule la portion d'image à récupérer
	if ($startWidth > $startHeight){
	    $y = 0;
	    $x = ($startWidth - $startHeight) / 2;
	    $smallestSide = $startHeight;
	 }else{
	    $x = 0;
	    $y = ($startHeight - $startWidth) / 2;
	    $smallestSide = $startWidth;
	 }

	 $resized = $this->image->getEmptyContainer($this->width, $this->height);
	 imagecopyresampled($resized, $imageRessource, 0, 0, $x, $y, $this->width, $this->height, $smallestSide, $smallestSide);

	 return $resized;
    }
}