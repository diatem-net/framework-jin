<?php

namespace jin\image;

/**
 * Outils de conversion de formats d'image
 */
class FormatConverter{
    /**
     * Chemin d'accès à la ressource image
     * @var string
     */
    private $imagePath;
    
    /**
     * Constructeur
     * @param string $imagePath     Chemin de la ressource image d'origine
     * @throws \Exception
     */
    public function __construct($imagePath) {
        if(!file_exists($imagePath)){
            //throw new \Exception('La ressource image '.$imagePath.' n\'existe pas.');
        }
        $this->imagePath = $imagePath;
    }
    
    
    /**
     * Convertit au format JPEG
     * @param string $outputFilePath    Fichier de sortie
     * @param tinteger $quality         Qualité. (De 1 à 100 - 100 par défaut)
     * @return boolean
     */
    public function saveInJpeg($outputFilePath, $quality = 100){
        $exec = 'convert -quality '.$quality.' '.$this->imagePath.' '.$outputFilePath.' 2>&1';
        exec($exec, $yaks);
        
        if(!empty($yaks)){
            $strError = '';
            foreach($yaks AS $e){
                $strError .= ' >> '.$e;
            }
            throw new \Exception($strError);
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Convertit au format PNG
     * @param string $outputFilePath    Fichier de sortie
     * @param tinteger $compression         Compression. (De 1 à 100 - 0 par défaut)
     * @return boolean
     */
    public function saveInPng($outputFilePath, $compression = 0){
        $exec = 'convert -quality '.$compression.' '.$this->imagePath.' '.$outputFilePath.' 2>&1';
        exec($exec, $yaks);
        
        if(!empty($yaks)){
            $strError = '';
            foreach($yaks AS $e){
                $strError .= ' >> '.$e;
            }
            throw new \Exception($strError);
            return false;
        }
        
        return true;
    }
}

