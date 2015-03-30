<?php

namespace jin\image;

use jin\log\Debug;

class ImageInfo{
    private $im;
    
    public function __construct($imagePath) {
        $this->im = new \Imagick($imagePath);
    }
    
    public function getFullInfos(){
        $datas = $this->im->getImageProperties();
        return $datas;
    }
    
    public function dumpInfos(){
        Debug::dump($this->getFullInfos());
    }
    
    public function getWidth(){
        return $this->im->getimagewidth();
    }
    
    public function getHeight(){
        return $this->im->getimageheight();
    }
}