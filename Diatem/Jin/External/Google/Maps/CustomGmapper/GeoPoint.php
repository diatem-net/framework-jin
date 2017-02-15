<?php
/**
 * Framework JIN
 */
namespace Diatem\Jin\External\Google\Maps\CustomGmapper;


/**
 * Décrit un point géolocalisé
 */
class GeoPoint{
    /**
     * Latitude
     * @var float
     */
    private $lat;

    /**
     * Longitude
     * @var float
     */
    private $long;


    /**
     * Constructeur
     * @param float $lat    Latitude du point
     * @param float $long   Longitude du point
     */
    public function __construct($lat, $long) {
        $this->lat = $lat;
        $this->long = $long;
    }


    /**
     * Retourne la latitude
     * @return float
     */
    public function getLatitude(){
        return $this->lat;
    }


    /**
     * Retourne la longitude
     * @return float
     */
    public function getLongitude(){
        return $this->long;
    }
}
