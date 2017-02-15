<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Image\Filters;

use Diatem\Jin\Image\FilterInterface;
use Diatem\Jin\Image\ImageFilter;
use Diatem\Jin\Image\Image;
use Diatem\Jin\Image\ImagickGd;
use Diatem\Jin\Log\Debug;

/**
 * Filtre image. Applique un masque sur l'image à partir d'une image en niveaux de gris.
 *
 * @auteur     Loïc Gerard
 */
final class ImageMask extends ImageFilter implements FilterInterface{

    /**
     * Objet Imagick du masque
     * @var \Imagick
     */
    private $mask;

    /**
     * Constructeur
     * @param string $maskFilePath    Chemin absolu ou relatif de l'image servant de masque.
     */
    public function __construct($maskFilePath = null, $imgRessource = null) {
	parent::__construct();

        if(!$maskFilePath && !$imgRessource){
            throw new \Exception('Vous devez spécifier maskFilePath ou imgRessource');
        }

        if($maskFilePath){
            $this->mask = new \Imagick($maskFilePath);
        }else if($imgRessource){
            $this->mask = ImagickGd::convertGDRessourceToImagick($imgRessource);
        }
    }


    /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource){
        if ($this->image->getExtension() != 'png') {
            throw new \Exception('Le filtre ImageMask n\'est appliquable que sur des images PNG');
        }

        $i1 = ImagickGd::convertGDRessourceToImagick($imageRessource);
        $i1->setImageMatte(0);

        $i1->compositeImage($this->mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0, \Imagick::CHANNEL_ALL);

        $gd = ImagickGd::convertImagickToGDRessource($i1, true);

        return $gd;


        //$image = ImagickGd::convertGDRessourceToImagick($imageRessource, true);
        $image->compositeImage($this->mask, \Imagick::COMPOSITE_DSTIN, 0, 0, \Imagick::CHANNEL_ALPHA);


        if ($image->getImageMatte()) {
            $image->compositeImage($this->mask, \Imagick::COMPOSITE_DSTIN, 0, 0, \Imagick::CHANNEL_ALPHA);
        } else {
            $image->compositeImage($this->mask, \Imagick::COMPOSITE_COPYOPACITY, 0, 0);
        }
        echo 'SAL';
        $image->writeimage('paf3.png');

        //$image->writeimage('test.png');
        $gd = ImagickGd::convertImagickToGDRessource($image, true);
        return $gd;


	return $imageRessource;
    }
}