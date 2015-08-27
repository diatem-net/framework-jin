<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\instagram;

use jin\com\Curl;
use jin\lang\StringTools;
use jin\log\Debug;
use jin\dataformat\Json;

/**
 * Méthodes d'implémentation de l'API Instagram.
 * https://instagram.com/developer/
 */
class Instagram{
    /**
     * Id client
     * @var string
     */
    private $clientId;
    
    
    /**
     * url de l'API Instagram
     */
    const INSTAGRAM_API_URL = 'https://api.instagram.com/v1/';
    
    
    /**
     * Constructeur
     * @param string $clientId  Id Client. (A générer sur https://instagram.com/developer/)
     */
    public function __construct($clientId) {
        $this->clientId = $clientId;
    }
    
    
    /**
     * Retourne les dernières photos contenant le tag indiqué
     * @param string $hashTag   tag (avec ou sans #)
     * @param integer   $count  Nombre de posts à retourner
     * @return array Tableau de tableaux associatifs contenant les données des photos
     */
    public function getLastPicturesContainingHashtag($hashTag, $count = 100){
        $hashTag = StringTools::replaceAll($hashTag, '#', '');
        
        $curl = new Curl();
        $r = $curl->call(self::INSTAGRAM_API_URL.'tags/'.$hashTag.'/media/recent?client_id='.$this->clientId.'&count='.$count, array(), 'GET', true);
        
        return Json::decode($r);
    }
}
