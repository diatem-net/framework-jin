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
 * Filtre image. Permet de coller sur l'image une autre image
 * 
 *  @auteur     Loïc Gerard
 */
final class ImageImport extends ImageFilter implements FilterInterface{
    /**
     * Image à coller
     * @var \jin\image\Image    
     */
    private $imagePasted;
    
    /**
     * Calage X
     * @var integer
     */
    private $x;
    
    /**
     * Calage Y
     * @var integer 
     */
    private $y;

    
    /**
     * Constructeur
     * @param String $imagePath Chemin de l'image à coller. (image ou imagePath requis)
     * @param Image $image  Objet image à coller. (image ou imagePath requis)
     * @param integer $x    Calage X du point supérieur gauche de l'image à coller.
     * @param integer $y    Calage Y du point supérieur gauche de l'image à coller.
     * @throws \Exception
     */
    public function __construct($imagePath = null, \jin\image\Image $image = null, $x, $y) {
	parent::__construct();
        
        $this->x = $x;
        $this->y = $y;
        
        if($imagePath){
            $this->imagePasted = new Image($imagePath);
        }else if($image){
            $this->imagePasted = $image;
        }else{
            throw new \Exception('Vous devez fournir au filtre ImageImport le chemin d\'une image existante ou un objet jin\image\Image');
        }
        
    }
    
    
    /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource){
        
        $source = $this->imagePasted->getImageRessource();
        imagecopymerge($imageRessource, 
                $source, 
                $this->x, 
                $this->y, 
                0, 
                0, 
                $this->imagePasted->getWidth(),
                $this->imagePasted->getHeight(),
                100);
	
	return $imageRessource;
    }
}