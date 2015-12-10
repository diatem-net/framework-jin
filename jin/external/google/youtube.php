<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\external\google;

use jin\com\Curl;
use jin\dataformat\Json;
use jin\JinCore;

/**
 * Facilite l'utilisation de l'API YouTube.
 *
 * @auteur  Samuel Marchal
 */
class YouTube
{

    /**
     * @var string  YouTube SERVER_KEY
     */
    private $server_key;

    /**
     *
     * @var boolean Debug mode
     */
    private $debug_mode;

    /**
     * url de l'API YouTube
     */
    const YOUTUBE_API_URL = 'https://www.googleapis.com/youtube/v3/';

    /**
     * Constructeur
     * @param string $server_key       Clé serveur (à créer sur https://console.developers.google.com/apis/credentials)
     * @param string $debug_mode       [optionel] Activer le mode debug
     */
    public function __construct($server_key, $debug_mode = false) {
        $this->server_key  = $server_key;
        $this->debug_mode  = $debug_mode;
    }

    /**
     * Effectue une requête directe sur l'API
     * @param  string $query        Requête
     * @param  array  $params       [optionel] Paramètres
     * @return array                Tableau de données
     */
    public function query($query, $params = array()) {
        $curl = new Curl();
        $params['key'] = $this->server_key;
        $result = Json::decode($curl->call(self::YOUTUBE_API_URL . trim($query, '/') . '?' . http_build_query($params), array(), 'GET', true));
        if(!isset($result['error'])) {
            return $result;
        }
        return $this->debug_mode ? $result : null;
    }

    /**
     * Retourne la liste des derniers statuts d'une page
     * @param  string $user_name    Nom de l'utilisateur
     * @param  int $count           [optionel] Nombre max de résultats (Défault: 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromUser($user_name, $count = 100) {
        $contentDetails = $this->query('channels', array(
            'part' => 'contentDetails',
            'forUsername' => $user_name,
        ));
        if($contentDetails) {
            $playlistId = $contentDetails['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
            return $this->query('playlistItems', array(
                'part' => 'snippet',
                'playlistId' => $playlistId,
                'count' => $count
            ));
        }
        return null;
    }

}