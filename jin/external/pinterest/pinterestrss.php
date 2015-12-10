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
     * Constructeur
     */
    public function __construct() {
    }

    /**
     * Récupère les 50 derniers pins d'un utilisateur
     * @param  string  $user        Identifiant de l'uilisateur
     * @return array                Tableau de pins
     */
    public function getLastPinsFromUser($user){
        $pins = array();
        $xml = simplexml_load_file('https://www.pinterest.com/'.$user.'/feed.rss');
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
    }

}