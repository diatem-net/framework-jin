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
        
        $this->filter_opacity($imageRessource, 50);
        return $imageRessource;
    }

    
    /**
     * Application d'une opacité
     * @param resource $img Ressource image
     * @param integer $opacity  Opacité
     * @return boolean
     */
    private function filter_opacity(&$img, $opacity) {
        if (!isset($opacity)) {
            return false;
        }
        $opacity /= 100;

        //get image width and height
        $w = imagesx($img);
        $h = imagesy($img);

        //turn alpha blending off
        imagealphablending($img, false);

        //find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++)
            for ($y = 0; $y < $h; $y++) {
                $alpha = ( imagecolorat($img, $x, $y) >> 24 ) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }

        //loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($img, $x, $y);
                $alpha = ( $colorxy >> 24 ) & 0xFF;
                //calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha );
                } else {
                    $alpha += 127 * $opacity;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha);
                //set pixel with the new color + opacity
                if (!imagesetpixel($img, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        return true;
    }

}
