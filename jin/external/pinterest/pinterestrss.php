<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\pinterest;

use jin\com\Curl;
use jin\dataformat\Json;

/**
 * Classe pour accéder au flux RSS d'un utilisateur Pinterest
 */
class PinterestRSS
{

    /**
     *
     * @var boolean Debug mode
     */
    private $debug_mode;

    /**
     * Constructeur
     * @param string $debug_mode       [optionel] Activer le mode debug
     */
    public function __construct($debug_mode = false) {
        $this->debug_mode = $debug_mode;
    }

    /**
     * Récupère les 50 derniers pins d'un utilisateur
     * @param  string  $user        Identifiant de l'uilisateur
     * @return array                Tableau de pins
     */
    public function getLastPinsFromUser($user){
        $pins = array();
        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file('https://www.pinterest.com/'.$user.'/feed.rss');
        if($xml !== false) {
            for($i = 0; $i < 50; $i++) {
                if(!isset($xml->channel->item[$i])) {
                    break;
                }
                $datas = array();
                $tags = array();
                preg_match ('/<img src=\"(?<media>.+?)\"><\/a><\/p><p>(?<text>.+?)<\/p>/', $xml->channel->item[$i]->description, $datas);
                preg_match_all('/(?<=#)\w+/', $datas['text'], $tags);
                $pins[] = array(
                    'url'       => $xml->channel->item[$i]->link->asXML(),
                    'media'     => $datas['media'],
                    'text'      => $datas['text'],
                    'tags'      => $tags[0],
                    'timestamp' => strtotime($xml->channel->item[$i]->pubDate)
                );
            }
            return $pins;
        } else {
            $error = libxml_get_last_error();
            return $this->debug_mode && $error ? $error->message : null;
        }
    }

}