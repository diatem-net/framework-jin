<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\External\Facebook;

use Diatem\Jin\Jin;

/**
 * Facilite l'utilisation de l'API Facebook.
 * Utilise le Facebook PHP SDK officiel.
 *
 * @aide    http://stackoverflow.com/questions/28124078/get-latest-facebook-posts-of-page-with-php-sdk
 *
 * Procédure :
 *
 * Facebook::generateShortLifeToken(CLIENT_ID, REDIRECT_URL, SCOPE);            // génère SHORT_LIFE_TOKEN
 * Facebook::generateLongLifeToken(CLIENT_ID, CLIENT_SECRET, SHORT_LIFE_TOKEN); // génère LONG_LIFE_TOKEN
 * Facebook::generatePermanentToken(ACCOUNT_ID, LONG_LIFE_TOKEN);               // génère PERMANENT_TOKEN
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
     * @param string $access_token     Token d'accès (short-life, long-life ou permanent)
     * @param string $debug_mode       [optionel] Activer le mode debug
     */
    public function __construct($client_id, $client_secret, $access_token, $debug_mode = false) {
        $this->client_id          = $client_id;
        $this->client_secret      = $client_secret;
        $this->access_token       = $access_token;
        $this->debug_mode         = $debug_mode;

        $libPath = Jin::getJinPath().Jin::getRelativePathExtLibs().'facebook-php-sdk/';
        require_once $libPath.'autoload.php';
        \Facebook\FacebookSession::setDefaultApplication($this->client_id, $this->client_secret);
        $this->session = new \Facebook\FacebookSession($this->access_token);
    }

    /**
     * Génère un token d'accès courte durée (2h)
     * @param  string $client_id      Identifiant de l'application
     * @param  string $redirect_uri   Url de redirection (doit correspondre à l'URL du site définie dans les paramètres de l'application)
     * @param  string $scope          Scope du token
     * @return string                 JSON contenant un token d'accès valable 2 heures
     */
    public static function generateShortLifeToken($client_id, $redirect_uri, $scope = null) {
        $params = array(
            'client_id'         => $client_id,
            'redirect_uri'      => $redirect_uri,
            'response_type'     => 'token'
        );
        if(!is_null($scope)) {
            $params['scope'] = $scope;
        }
        header('location:https://www.facebook.com/dialog/oauth?' . http_build_query($params));
        die;
    }

    /**
     * Génère un token d'accès longue durée (60 jours)
     * @param  string $client_id        Identifiant de l'application
     * @param  string $client_secret    Clé secrète de l'application
     * @param  string $short_life_token Token d'accès (short-life)
     * @return string                   JSON contenant un token d'accès valable 60 jours
     */
    public static function generateLongLifeToken($client_id, $client_secret, $short_life_token) {
        $params = array(
            'client_id'         => $client_id,
            'client_secret'     => $client_secret,
            'fb_exchange_token' => $short_life_token,
            'grant_type'        => 'fb_exchange_token'
        );
        header('location:https://graph.facebook.com/oauth/access_token?' . http_build_query($params));
        die;
    }

    /**
     * Génère un token d'accès permanent
     * @param  string $account_id      Identifiant d'un compte administrateur de l'application
     * @param  string $long_life_token Token d'accès (long-life)
     * @return string                  JSON contenant les token permanents de toutes les applications liées au compte
     */
    public static function generatePermanentToken($account_id, $long_life_token) {
        $params = array(
            'access_token'      => $long_life_token
        );
        header('location:https://graph.facebook.com/'.$account_id.'/accounts?' . http_build_query($params));
        die;
    }



    /**
     * Retourne la liste des derniers statuts d'une page (inclut les posts)
     * @param  string $page_name    Nom de la page
     * @param  int $count           [optionel] Nombre max de résultats (Défault: 100)
     * @return array                Tableau de statuts
     */
    public function getLastStatusesFromPage($page_name, $count = 100, $fields = 'id,created_time,message,picture,full_picture') {

        try {
            $data = (new \Facebook\FacebookRequest(
                $this->session, 'GET', '/'.$page_name.'/feed?fields='.$fields.'&limit='.$count
            ))->execute()->getGraphObject()->getPropertyAsArray("data");
            return $data;
        } catch (\Exception $e) {
            return $this->debug_mode ? $e->getMessage() : null;
        }
        return null;
    }



    /**
     * Retourne la liste des derniers posts d'une page
     * @param  string $page_name    Nom de la page
     * @param  int $count           [optionel] Nombre max de résultats (Défault: 100)
     * @return array                Tableau de posts
     */
    public function getLastPostsFromPage($page_name, $count = 100, $fields = 'id,created_time,message,picture,full_picture') {

        try {
            $data = (new \Facebook\FacebookRequest(
                $this->session, 'GET', '/'.$page_name.'/posts?fields='.$fields.'&limit='.$count
            ))->execute()->getGraphObject()->getPropertyAsArray("data");
            return $data;
        } catch (\Exception $e) {
            return $this->debug_mode ? $e->getMessage() : null;
        }
        return null;
    }

}