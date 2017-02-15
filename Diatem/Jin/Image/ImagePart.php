<?php
/**
 * Jin FrameWork
 */
namespace Diatem\Jin\Image;

/**
 * Classe permettant d'exploiter une portion d'un objet Image Jin.
 * @author  Loïc Gerard
 */
class ImagePart
{
    /**
     * Ressource image contenant la portion coupée
     * @var resource
     */
    private $cuttedRessource;

    /**
     * Objet jin\image\Image source
     * @var \Diatem\Jin\Image\Image
     */
    private $srcImage;


    /**
     * Constructeur
     * @param integer $x                Coordonnée X du point supérieur gauche où débuter la découpe
     * @param integer $y                Coordonnée Y du point supérieur gauche où débuter la découpe
     * @param integer $width            Largeur (en pixels) de la zone à découper
     * @param integer $height           Hauteur (en pixels) de la zone à découper
     * @param \Diatem\Jin\Image\Image $image   Objet image source
     */
    public function __construct($x, $y, $width, $height, Image $image)
    {
        $this->srcImage = $image;
        $this->cuttedRessource = $image->getEmptyContainer($width, $height);

        imagecopy($this->cuttedRessource, $image->getImageRessource(), 0, 0, $x, $y, $width, $height);
    }


    /**
     * Ecrit la portion d'image dans un fichier
     * @param string $path  Chemin du fichier
     * @throws \Exception
     */
    public function write($path)
    {
        if ($this->srcImage->getExtension() == 'jpg') {
            imagejpeg($this->cuttedRessource, $path, $this->srcImage->getJpgQuality());
        } elseif ($this->srcImage->getExtension() == 'png') {
            imagepng($this->cuttedRessource, $path, $this->srcImage->getPngCompression());
        } else {
            throw new \Exception('Impossible de générer l\'image : extension non supportée');
        }
    }


    /**
     * Retourne la ressource image resultante
     * @return resource
     */
    public function getRessource()
    {
        return $this->cuttedRessource;
    }
}
