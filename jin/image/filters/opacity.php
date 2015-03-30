<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\image\filters;

use jin\image\FilterInterface;
use jin\image\ImageFilter;
use jin\image\Image;
use jin\image\ImagickGd;


/**
 * Filtre image. Permet de modifier l'opactité d'une image PNG (uniquement !)
 * 
 *  @auteur     Loïc Gerard
 */
final class Opacity extends ImageFilter implements FilterInterface {
    /**
     * Degré d'opacité
     * @var integer
     */
    private $opacity;

    
    /**
     * Constructeur
     * @param integer $opacity  Opacité. (de 0 à 100)
     */
    public function __construct($opacity) {
        parent::__construct();

        $this->opacity = $opacity;
    }

    
    /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource) {

        if ($this->image->getExtension() != 'png') {
            throw new \Exception('Le filtre Opacity n\'est appliquable que sur des images PNG');
        }
        
        $imagick = ImagickGd::convertGDRessourceToImagick($imageRessource, 'png');
        $imagick->evaluateImage(\Imagick::EVALUATE_MULTIPLY, $this->opacity / 100, \Imagick::CHANNEL_ALPHA);
        $gd = ImagickGd::convertImagickToGDRessource($imagick, 'png');
        
        return $gd;
    }

    

}
