<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\External\Google;

use Diatem\Jin\Com\Curl;
use Diatem\Jin\DataFormat\Json;

/**
 * Classe pour accéder au flux RSS d'un utilisateur YouTube
 */
class YouTubeRSS
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
     * Récupère les dernières vidéos d'un utilisateur
     * @param  string  $user_name   Nom de l'utilisateur
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromUser($user_name, $count = 100){
        $videos = array();
        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file('https://www.youtube.com/feeds/videos.xml?user='.$user_name);
        if($xml !== false) {
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
        } else {
            $error = libxml_get_last_error();
            return $this->debug_mode && $error ? $error->message : null;
        }
    }

    /**
     * Récupère les dernières vidéos d'un canal
     * @param  string  $channel_id  Identifiant du canal
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromChannel($channel_id, $count = 100){
        $videos = array();
        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file('https://www.youtube.com/feeds/videos.xml?channel_id='.$channel_id);
        if($xml !== false) {
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
        } else {
            $error = libxml_get_last_error();
            return $this->debug_mode && $error ? $error->message : null;
        }
    }

    /**
     * Récupère les dernières vidéos d'un canal
     * @param  string  $playlist_id  Identifiant du canal
     * @param  string  $count       [optionel] Nombre maximal de vidéos à retourner (Défault : 100)
     * @return array                Tableau de vidéos
     */
    public function getLastVideosFromPlaylist($playlist_id, $count = 100){
        $videos = array();
        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file('https://www.youtube.com/feeds/videos.xml?playlist_id='.$playlist_id);
        if($xml !== false) {
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
        } else {
            $error = libxml_get_last_error();
            return $this->debug_mode && $error ? $error->message : null;
        }
    }

}