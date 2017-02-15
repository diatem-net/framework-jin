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
 * Filtre image. Adoucit les contours.
 *
 * @auteur     Loïc Gerard
 */
final class Smooth extends ImageFilter implements FilterInterface{
    /**
     *
     * @var integer Intensité. 0 = intensité max.
     */
    private $intensite;

    /**
     * Constructeur
     * @param integer $intensite   Degré de lissage 0 = intensité maximale
     */
    public function __construct($intensite) {
	parent::__construct();
	$this->intensite = $intensite;
    }


    /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource){
	imagefilter ($imageRessource , IMG_FILTER_SMOOTH, $this->intensite);

	return $imageRessource;
    }
}