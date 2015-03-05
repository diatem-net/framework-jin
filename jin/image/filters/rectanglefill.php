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
 * Filtre image. Permet de dessiner dans l'image un rectangle
 * 
 *  @auteur     Loïc Gerard
 */
final class RectangleFill extends ImageFilter implements FilterInterface {
    /**
     * Coordonnées X du point 1
     * @var int
     */
    private $x1;
    
    /**
     * Coordonnées Y du point 1
     * @var int
     */
    private $y1;

    /**
     * Coordonnées X du point 2
     * @var int
     */
    private $x2;
    
    /**
     * Coordonnées Y du point 2
     * @var int
     */
    private $y2;
    
    /**
     * Couleur. Composante rouge.
     * @var int
     */
    private $r;
    
     /**
     * Couleur. Composante verte.
     * @var int
     */
    private $g;
    
     /**
     * Couleur. Composante bleue.
     * @var int
     */
    private $b;
    
    
    /**
     * Constructeur
     * @param int $x1   Coordonnées X du point 1
     * @param int $y1   Coordonnées Y du point 1
     * @param int $x2   Coordonnées X du point 2
     * @param int $y2   Coordonnées Y du point 2
     * @param int $r    Couleur de remplissage. Composante rouge.
     * @param int $g    Couleur de remplissage. Composante verte.
     * @param int $b    Couleur de remplissage. Composante bleue.
     */
    public function __construct($x1, $y1, $x2, $y2 , $r, $g, $b) {
        parent::__construct();

        $this->b = $b;
        $this->g = $g;
        $this->r = $r;
        $this->x1 = $x1;
        $this->x2 = $x2;
        $this->y1 = $y1;
        $this->y2 = $y2;
    }

    
    /**
     * Application du filtre
     * @param resource $imageRessource	ImageRessource GD sur lequel appliquer le filtre
     * @return resource	ImageRessource GD modifié
     */
    public function apply($imageRessource) {

        $color = imagecolorallocate($imageRessource, $this->r, $this->g, $this->b);
        imagefilledrectangle($imageRessource, $this->x1, $this->y1, $this->x2, $this->y2, $color);
        
 
        return $imageRessource;
    }

}
