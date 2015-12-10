<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\external\facebook;

use jin\JinCore;

/**
 * Facilite l'utilisation de l'API Facebook.
 * Utilise le Facebook PHP SDK officiel.
 *
 * @auteur  Samuel Marchal
 * @aide    http://stackoverflow.com/questions/28124078/get-latest-facebook-posts-of-page-with-php-sdk
 */
class Facebook
{

    /**
     * @var string  Facebook CLIENT_KEY
     */
    private $client_id;

    /**
     * @var string  Facebook CLIENT_SECRET
     */
    private $client_secret;

    /**
     * @var string  Facebook ACCESS TOKEN
     */
    private $access_token;

    /**
     *
     * @var boolean Debug mode
     */
    private $debug_mode;

    /**
     * @var FacebookSession Instance de la classe FacebookSession
     */
    private $session;

    /**
     * Constructeur
     * @param string $client_id        Identifiant de l'application
     * @param string $client_secret    Clé secrète de l'application
     * @param string $access_token     Token d'accès
     * @param string $debug_mode       [optionel] Activer le mode debug
     */
    public function __construct($client_id, $client_secret, $access_token, $debug_mode = false) {
        $this->client_id          = $client_id;
        $this->client_secret      = $client_secret;
        $this->access_token       = $access_token;
        $this->debug_mode         = $debug_mode;

        $libPath = JinCore::getJinRootPath().JinCore::getRelativeExtLibs().'facebook-php-sdk/';
        require_once $libPath.'autoload.php';
        \Facebook\FacebookSession::setDefaultApplication($this->client_id, $this->client_secret);
        $this->session = new \Facebook\FacebookSession($this->access_token);
    }

    /**
     * Génère un token d'accès longue durée (60 jours)
     * @param  string $client_id      Identifiant de l'application
     * @param  string $client_secret  Clé secrète de l'application
     * @param  string $client_secret  Clé secrète de l'application
     * @return string                 Token d'accès valable 60 jours
     */
    public static function generateLongLifeToken($client_id, $client_secret, $exchange_token) {
        $params = array(
            'client_id'         => $client_id,
            'client_secret'     => $client_secret,
            'fb_exchange_token' => $exchange_token,
            'grant_type'        => 'fb_exchange_token'
        );
        header('location:https://graph.facebook.com/oauth/access_token?' . http_build_query($params));
        die;
    }



    /**
     * Retourne la liste des derniers statuts d'une page
     * @param  string $page_name    Nom de la page
     * @param  int $count           [optionel] Nombre max de résultats (Défault: 100)
     * @return array                Tableau de statuts
     * @return array
     */
    public function getLastStatusesFromPage($page_name, $count = 100) {

        try {
            $data = (new \Facebook\FacebookRequest(
                $this->session, 'GET', '/'.$page_name.'/posts?fields=id,created_time,message,picture'
            ))->execute()->getGraphObject()->getPropertyAsArray("data");
            return $data;
        } catch (\Exception $e) {
            return $this->debug_mode ? $e->getMessage() : null;
        }
        return null;
    }

}