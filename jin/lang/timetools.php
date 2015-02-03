<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\lang;

/** Boite à outils pour les opérations temporelles
 *
 *  @auteur     Loïc Gerard, Samuel Marchal
 *  @version    0.0.2
 *  @check      23/09/2014
 */
class TimeTools {

    /** Retourne le timestamp courant en milisecondes
     *
     * @return integer  Timestamp courant en MS
     */
    public static function getTimestampInMs() {
       return round(microtime(true) * 1000);
    }

    /** Passe une date au format européen jj/mm/aaaa hh:mm:ss
     *
     * @param  string|DateTime $date Date quelconque
     * @param  boolean $withHour     Retourner les heures ou non
     * @return string                Date au format européen
     */
    public static function toEuropeanFormat($date = null, $withHour = false) {
        if(is_string($date)) {
            $time = strtotime($date);
        } elseif(is_a($date, 'DateTime')) {
            $time = $date->getTimestamp();
        } else {
            return null;
        }
        return date('d/m/Y' . ($withHour ? ' H:i:s' : ''), $time);
    }

    /** Passe une date au format américain aaaa-mm-jj hh:mm:ss
     *
     * @param  string|DateTime $date Date quelconque
     * @param  boolean $withHour     Retourner les heures ou non
     * @return string                Date au format américain
     */
    public static function toAmericanFormat($date = null, $withHour = false) {
        if(is_string($date)) {
            $time = strtotime($date);
        } elseif(is_a($date, 'DateTime')) {
            $time = $date->getTimestamp();
        } else {
            return null;
        }
        return date('Y-m-d' . ($withHour ? ' H:i:s' : ''), $time);
    }

    /** Passe une date au format HTML5 (= américain)
     *
     * @param  string|DateTime $date Date quelconque
     * @param  boolean $withHour     Retourner les heures ou non
     * @return string                Date au format HTML5
     */
    public static function toHTML5Format($date = null, $withHour = false) {
        return self::toAmericanFormat($date, $withHour);
    }
    
    
    /**
     * Retourne TRUE si date1 est plus récente que date2
     * @param string|DateTime $date1	Date1
     * @param string|DateTime $date2	Date2
     * @param	string		$operator (Opérateur de comparaison : =, <, <=, >=, >)
     * @return boolean|null	    Si TRUE : date1 > date2
     */
    public static function compare($date1, $date2, $operator = '='){
	if(is_string($date1)) {
            $time1 = strtotime($date1);
        } elseif(is_a($date1, 'DateTime')) {
            $time1 = $date1->getTimestamp();
        } else {
            throw new \Exception('Format date1 invalide');
        }
	
	if(is_string($date2)) {
            $time2 = strtotime($date2);
        } elseif(is_a($date2, 'DateTime')) {
            $time2 = $date2->getTimestamp();
        } else {
            throw new \Exception('Format date2 invalide');
        }
	
	if($operator == '='){
	    if($time1 == $time2){
		return true;
	    }
	}else if($operator == '>'){
	    if($time1 > $time2){
		return true;
	    }
	}else if($operator == '<'){
	    if($time1 < $time2){
		return true;
	    }
	}else if($operator == '>='){
	    if($time1 >= $time2){
		return true;
	    }
	}else if($operator == '<='){
	    if($time1 <= $time2){
		return true;
	    }
	}else{
	    throw new \Exception('Opérateur '.$operator.' non supporté');
	}
	
	return false;
    }
}
