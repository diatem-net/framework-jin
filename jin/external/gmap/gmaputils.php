<?php

namespace jin\external\gmap;

use jin\lang\ArrayTools;

/**
 * Fonctions utilitaires liées à l'API GMap
 */
class GMapUtils {

    /**
     * Permet d'obtenir les coordonnées GPS d'une adresse
     * @param string $adress  Adresse à géolocaliser.
	 * @param string $apiKey Clé Api Google
     * @return array    array('latitude' => 0, 'longitude' => 0)
     * @throws \Exception
     */
    public static function geolocalize($adress, $apiKey = null) {
		$url = 'https://maps.google.com/maps/api/geocode/json?address=' . urlencode($adress) . '&sensor=false';
		if($apiKey){
			$url .= '&key='.$apiKey;
		}
        $json = file_get_contents($url);
        $json = json_decode($json);

    
        if (ArrayTools::length($json->{'results'}) == 0) {
            throw new \Exception('L\'adresse transmise ne permet pas le calcul d\'une position géographique');
        } else {
            $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            return array('latitude' => $lat, 'longitude' => $long);
        }
    }

}
