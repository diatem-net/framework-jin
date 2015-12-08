<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\pinterest;

use jin\com\Curl;
use jin\dataformat\Json;

/**
 * Méthodes d'implémentation de l'API Pinterest.
 * https://developers.pinterest.com/docs/
 */
class Pinterest
{

    /**
     * @var string  Pinterest CLIENT_ID
     */
    private $client_id;

    /**
     * @var string  Pinterest ACCESS_TOKEN
     * Pour en obtenir un : https://developers.pinterest.com/tools/access_token/
     * Exemplt d'URL d'autorisation : https://api.pinterest.com/oauth/?response_type=token&scope=read&redirect_uri=HTTPS_REDIRECT_URI&client_id=CLIENT_ID&client_secret=CLIENT_SECRET
     */
    private $access_token;

    /**
     *
     * @var boolean Debug mode
     */
    private $debug_mode;

    /**
     * url de l'API Pinterest
     */
    const PINTEREST_API_URL = 'https://api.pinterest.com/v3/';

    /**
     * Constructeur
     * @param string $client_id  Id Client. (A générer sur https://developers.pinterest.com/apps/)
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
        $result = Json::decode($curl->call(self::PINTEREST_API_URL . trim($query, '/') . '/?' . http_build_query($params), array(), 'GET', true));
        if($result['status'] == 'success') {
            return $result['data'];
        }
        return $this->debug_mode ? $result['error'] : null;
    }

    /**
     * Retourne les deniers pins contenant le tag indiqué
     * @param string  $hashtag      Tag (avec ou sans #)
     * @param integer $count        Nombre de pins à retourner
     * @return array                Tableau de tableaux associatifs contenant les données des pins
     */
    // public function getLastPinsContainingHashtag($hashtag, $count = 100){
    //     return $this->query('tags/'.trim($hashtag, '#').'/media/recent', array(
    //         'count' => $count
    //     ));
    // }

    /**
     * Retourne les deniers pins contenant le tag indiqué
     * @param string  $user_id      Tag (avec ou sans #)
     * @param integer $count        Nombre de pins à retourner
     * @return array                Tableau de tableaux associatifs contenant les données des pins
     */
    public function getLastPinsFromUser($user_id, $count = 100){
        return $this->query('pidgets/users/'.$user_id.'/pins');
    }

}