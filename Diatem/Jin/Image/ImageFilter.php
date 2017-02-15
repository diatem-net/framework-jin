<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Image;

/** Classe parent de tous filtre Image
 *
 *  @auteur     Loïc Gerard
 */
class ImageFilter
{
    /**
     *
     * @var \Diatem\Jin\Image\Image Instance de la classe Image sur lequel appliquer le filtre
     */
    protected $image;

    /**
     * Constructeur
     */
    public function __construct()
    {
    }

    /**
     * Methode appelée à l'initialisation du filtre par l'objet Image
     * @param \Diatem\Jin\Image\Image $image	Instance de la classe Image sur lequel appliquer le filtre
     */
    public function init(Image $image)
    {
        $this->image = $image;
    }
}
