<?php
/**
* Jin Framework
* Diatem
*/

namespace jin\lang;

/** Boite à outils pour les opérations temporelles
*
*  @auteur     Loïc Gerard, Samuel Marchal
*  @version    0.0.3
*  @check      20/05/2015
*/
class TimeTools {

    /** Retourne le timestamp courant en milisecondes
    *
    * @return integer  Timestamp courant en MS
    */
    public static function getTimestampInMs() {
        return round(microtime(true) * 1000);
    }

    /** Passe une date d'un format X à un format Y
    *
    * @param  string $date        Date quelconque
    * @param  string $fromFormat  Format d'entrée
    * @param  string $toFormat    Format de sortie
    * @return string              Date formatée
    */
    public static function fromFormatToFormat($date, $fromFormat, $toFormat) {
        $dt = \DateTime::createFromFormat($fromFormat, $date);
        return $dt->format($toFormat);
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

    /**
    * Retourne TRUE si $date est entre $from et $to
    * @param string|DateTime $date   Date à vérifier
    * @param string|DateTime $from   Date de début de la plage
    * @param string|DateTime $to     Date de fin de la plage
    * @param boolean         $strict [optionnel] TRUE pour exclure les limites
    * @return boolean                Si TRUE : $from <(=) $date <(=) $to
    */
    public static function isBetween($date, $from, $to, $strict = false){
        $operator = $strict ? '<' : '<=';
        return TimeTools::compare($from, $date, $operator) && TimeTools::compare($date, $to, $operator);
    }

    /**
    * Retourne la version littérale d'un mois
    * @param integer $month Mois
    * @return string        Mois littéral
    */
    public static function literalMonth($month){
        $month = intval($month);
        if($month < 1 || $month > 12) {
            return '';
        }
        $months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return $months[$month - 1];
    }
    
    
    /**
     * Vérifie si une date est valide
     * @param string $date      Date
     * @param string $format    Format
     * @return type
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }


    /**
     * Modifie un objet DateTime en y ajoutant N jours ouvrés
     * @param DateTime  $date    Date
     * @param int       $nb      Nombre de jours ouvrés à ajouter
     * @return DateTime
     */
    public static function dateAddWorkDays(\DateTime $date, $nb){
        $t = $date->getTimestamp();

        // loop for X days
        for($i=0; $i<$nb; $i++){
            // add 1 day to timestamp
            $addDay = 86400;

            // get what day it is next day
            $nextDay = date('w', ($t+$addDay));

            // if it's Saturday or Sunday get $i-1
            if($nextDay == 0 || $nextDay == 6) {
                $i--;
            }

            // modify timestamp, add 1 day
            $t = $t+$addDay;
        }

        $date->setTimestamp($t);

        return $date;
    }
}
