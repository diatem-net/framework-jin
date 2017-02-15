<?php

namespace Diatem\Jin\Image;

/**
 * Classe permettant d'effectuer des conversions entre Imagick et GD
 */
class ImagickGd
{
    /**
     * Convertit une ressource image GD en objet Imagick
     * @param resource $imgRessource    Ressource image GD
     * @param boolean $png              PNG (true) ou JPEG (false)
     * @return \imagick
     */
    public static function convertGDRessourceToImagick($imgRessource, $png = false){
        ob_start();                   // starts output buffering
        if($png){
            imagepng($imgRessource, null, 0);
        }else{
            imagejpeg($imgRessource, null, 100);
        }
        $blob = ob_get_clean();

        $image = new \imagick();
        $image->readImageBlob($blob);

        return $image;
    }


    /**
     * Convertir un objet Imagick en ressource image GD
     * @param \Imagick $imagick     Objet Imagick
     * @return resource
     */
    public static function convertImagickToGDRessource(\Imagick $imagick, $png = false){

        $imagick->setImageFormat('png');
        $data = $imagick->getimageblob();
        $im = imagecreatefromstring($data);

        if($png){
            imagealphablending($im, true); // setting alpha blending on
            imagesavealpha($im, true);
        }

        return $im;
    }
}

