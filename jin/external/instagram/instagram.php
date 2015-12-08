<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\instagram;

use jin\com\Curl;
use jin\dataformat\Json;

/**
 * Méthodes d'implémentation de l'API Instagram.
 * https://instagram.com/developer/
 */
class Instagram
{

    /**
     * @var string  Instagram CLIENT_ID
     */
    private $client_id;

    /**
     * @var string  Instagram ACCESS_TOKEN
     * Pour en obtenir un : https://www.instagram.com/oauth/authorize/?client_id=CLIENT_ID&redirect_uri=REDIRECT_URI&response_type=token&scope=public_content
     */
    private $access_token;

    /**
     *
     * @var boolean Debug mode
     */
    private $debug_mode;

    /**
     * url de l'API Instagram
     */
    const INSTAGRAM_API_URL = 'https://api.instagram.com/v1/';

    /**
     * Constructeur
     * @param string $client_id  Id Client. (A générer sur https://instagram.com/developer/)
     */
    public function __construct($client_id, $access_token, $debug_mode = false) {
        $this->client_id = $client_id;
        $this->access_token = $access_token;
        $this->debug_mode = $debug_mode;
    }

    /**
     * Effectue une requête directe sur l'API
     * @param string $query         Requête
     * @param array  $params        [optionel] Paramètres
     * @return Array
     */
    public function query($query, $params = array()) {
        $curl = new Curl();
        $params['client_id'] = $this->client_id;
        $params['access_token'] = $this->access_token;
        $result = Json::decode($curl->call(self::INSTAGRAM_API_URL . trim($query, '/') . '?' . http_build_query($params), array(), 'GET', true));
        if($result['meta']['code'] == 200) {
            return $result['data'];
        }
        return $this->debug_mode ? $result : null;
    }

    /**
     * Retourne les dernières photos contenant le tag indiqué
     * @param string  $hashtag      Tag (avec ou sans #)
     * @param integer $count        Nombre de posts à retourner
     * @return array                Tableau de tableaux associatifs contenant les données des photos
     */
    public function getLastPicturesContainingHashtag($hashtag, $count = 100){
        return $this->query('tags/'.trim($hashtag, '#').'/media/recent', array(
            'count' => $count
        ));
    }

    /**
     * Retourne les dernières photos contenant le tag indiqué
     * @param string  $user_id      Tag (avec ou sans #)
     * @param integer $count        Nombre de posts à retourner
     * @return array                Tableau de tableaux associatifs contenant les données des photos
     */
    public function getLastPicturesFromUser($user_id, $count = 100){
        return $this->query('users/'.$user_id.'/media/recent', array(
            'count' => $count
        ));
    }

}