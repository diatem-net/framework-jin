<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\External\Pinterest;

use Diatem\Jin\Com\Curl;
use Diatem\Jin\DataFormat\Json;

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
     * @param string $client_id     Identifiant de l'application
     */
    public function __construct($client_id, $access_token, $debug_mode = false) {
        $this->client_id = $client_id;
        $this->access_token = $access_token;
        $this->debug_mode = $debug_mode;
    }

    /**
     * Génère un token d'accès
     * @param string $client_id      Identifiant de l'application
     * @param string $client_secret  Clé secrète de l'application
     * @param string $redirect_uri   URL de redirection
     * @param string $scope          [optionel] Degrés d'authorisation dont l'application à besoin (Défault : read)
     */
    public static function generateToken($client_id, $client_secret, $redirect_uri, $scope = 'read') {
        $params = array(
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'token',
            'scope'         => $scope
        );
        header('location:https://api.pinterest.com/oauth/?' . http_build_query($params));
        die;
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
     * @param string  $user_id      Tag (avec ou sans #)
     * @return array                Tableau de pins
     */
    public function getLastPinsFromUser($user_id){
        return $this->query('pidgets/users/'.$user_id.'/pins');
    }

}