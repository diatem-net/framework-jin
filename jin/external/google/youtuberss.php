<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\external\google;

use jin\com\Curl;
use jin\dataformat\Json;

/**
 * Classe pour accéder au flux RSS d'un utilisateur YouTube
 */
class YouTubeRSS
{

    /**
     * Constructeur
     */
    public function __construct() {}

    /**
     * Récupère les dernières vidéos d'un utilisateur
     * @param  string  $user_name   Nom de l'utilisateur
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromUser($user_name, $count = 100){
        $videos = array();
        $xml = simplexml_load_file('https://www.youtube.com/feeds/videos.xml?user='.$user_name);
        for($i = 0; $i < $count; $i++) {
            if(!isset($xml->entry[$i])) {
                break;
            }
            $id = str_replace('yt:video:', '', $xml->entry[$i]->id);
            $videos[] = array(
                'id'        => $id,
                'url'       => 'http://www.youtube.com/watch?v='.$id,
                'media'     => 'http://i.ytimg.com/vi/'.$id.'/hqdefault.jpg',
                'text'      => (string) $xml->entry[$i]->title,
                'timestamp' => strtotime($xml->entry[$i]->published)
            );
        }
        return $videos;
    }

    /**
     * Récupère les dernières vidéos d'un canal
     * @param  string  $channel_id  Identifiant du canal
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromChannel($channel_id, $count = 100){
        $videos = array();
        $xml = simplexml_load_file('https://www.youtube.com/feeds/videos.xml?channel_id='.$channel_id);
        for($i = 0; $i < $count; $i++) {
            if(!isset($xml->entry[$i])) {
                break;
            }
            $id = str_replace('yt:video:', '', $xml->entry[$i]->id);
            $videos[] = array(
                'id'        => $id,
                'url'       => 'http://www.youtube.com/watch?v='.$id,
                'media'     => 'http://i.ytimg.com/vi/'.$id.'/hqdefault.jpg',
                'text'      => (string) $xml->entry[$i]->title,
                'timestamp' => strtotime($xml->entry[$i]->published)
            );
        }
        return $videos;
    }

    /**
     * Récupère les dernières vidéos d'un canal
     * @param  string  $playlist_id  Identifiant du canal
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromPlaylist($playlist_id, $count = 100){
        $videos = array();
        $xml = simplexml_load_file('https://www.youtube.com/feeds/videos.xml?playlist_id='.$playlist_id);
        for($i = 0; $i < $count; $i++) {
            if(!isset($xml->entry[$i])) {
                break;
            }
            $id = str_replace('yt:video:', '', $xml->entry[$i]->id);
            $videos[] = array(
                'id'        => $id,
                'url'       => 'http://www.youtube.com/watch?v='.$id,
                'media'     => 'http://i.ytimg.com/vi/'.$id.'/hqdefault.jpg',
                'text'      => (string) $xml->entry[$i]->title,
                'timestamp' => strtotime($xml->entry[$i]->published)
            );
        }
        return $videos;
    }

}