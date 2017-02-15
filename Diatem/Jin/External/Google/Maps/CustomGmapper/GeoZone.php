<?php
/**
 * Framework JIN
 */
namespace Diatem\Jin\External\Google\Maps\CustomGmapper;

/**
 * Décrit une zone géographique rectangulaire
 */
class GeoZone{
    /**
     * Point NO de la zone
     * @var \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    private $no;

    /**
     * Point NE de la zone
     * @var \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    private $ne;

    /**
     * Point SO de la zone
     * @var \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    private $so;

    /**
     * Point SE de la zone
     * @var \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    private $se;


    /**
     * Constructeur
     * @param float $lat1   Latitude du point A
     * @param float $lat2   Latitude du point B
     * @param float $lon1   Longitude du point A
     * @param float $lon2   Longitude du point B
     */
    public function __construct($lat1, $lat2, $lon1, $lon2) {
        $this->no = new GeoPoint(max($lat1, $lat2), min($lon1, $lon2));
        $this->ne = new GeoPoint(max($lat1, $lat2), max($lon1, $lon2));
        $this->so = new GeoPoint(min($lat1, $lat2), min($lon1, $lon2));
        $this->se = new GeoPoint(min($lat1, $lat2), max($lon1, $lon2));
    }


    /**
     * Retourne le point NO
     * @return \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    public function getNordOuestPoint(){
        return $this->no;
    }


    /**
     * Retourne le point NE
     * @return \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    public function getNordEstPoint(){
        return $this->ne;
    }


    /**
     * Retourne le point SO
     * @return \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    public function getSudOuestPoint(){
        return $this->so;
    }


    /**
     * Retourne le point SE
     * @return \Diatem\Jin\External\Google\Maps\CustomGmapper\GeoPoint
     */
    public function getSudEstPoint(){
        return $this->se;
    }
}

