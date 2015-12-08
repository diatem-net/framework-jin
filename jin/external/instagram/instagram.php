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
 * https://www.instagram.com/developer/clients/manage/
 */
class Instagram
{

    /**
     * @var string  Instagram CLIENT_ID
     */
    private $client_id;

    /**
     * @var string  Instagram ACCESS_TOKEN
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
     * @param string $client_id      Identifiant de l'application
     * @param string $access_token   Token d'accès
     * @param string $debug_mode     [optionel] Activer le mode debug
     */
    public function __construct($client_id, $access_token, $debug_mode = false) {
        $this->client_id = $client_id;
        $this->access_token = $access_token;
        $this->debug_mode = $debug_mode;
    }

    /**
     * Génère un token d'accès
     * @param string $client_id      Identifiant de l'application
     * @param string $redirect_uri   URL de redirection
     * @param string $scope          [optionel] Degrés d'authorisation dont l'application à besoin (Défault : public_content)
     */
    public static function generateToken($client_id, $redirect_uri, $scope = 'public_content') {
        $params = array(
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'token',
            'scope'         => $scope
        );
        header('location:https://www.instagram.com/oauth/authorize/?' . http_build_query($params));
        die;
    }

    /**
     * Effectue une requête directe sur l'API
     * @param  string $query        Requête
     * @param  array  $params       [optionel] Paramètres
     * @return array                Tableau de données
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
     * @param  string  $hashtag     Tag (avec ou sans #)
     * @param  integer $count       [optionel] Nombre de posts à retourner (Défault : 100)
     * @return array                Tableau de photos
     */
    public function getLastPicturesContainingHashtag($hashtag, $count = 100){
        return $this->query('tags/'.trim($hashtag, '#').'/media/recent', array(
            'count' => $count
        ));
    }

    /**
     * Retourne les dernières photos contenant le tag indiqué
     * @param  string  $user_id     Nom de l'utilisateur
     * @param  integer $count       [optionel] Nombre de posts à retourner (Défault : 100)
     * @return array                Tableau de photos
     */
    public function getLastPicturesFromUser($user_id, $count = 100){
        return $this->query('users/'.$user_id.'/media/recent', array(
            'count' => $count
        ));
    }

}