<?php

/**
 * Jin Framework
 * Diatem
 */
namespace jin\lang;

use jin\lang\StringTools;

/** Boite à outil pour les tableaux
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		
 */
class ArrayTools {

    /** Permet de trier un tableau associatif en précisant la colonne à utiliser pour trier
     * 	@param 	array	$array	Tableau à trier
     * 	@param 	string 	$index	Index de la colonne à utiliser pour effectuer le tri
     * 	@return array		Tableau trié
     */
    public static function sortAssociativeArray($array, $index) {
	$sort = array();
	//préparation d'un nouveau tableau basé sur la clé à trier
	foreach ($array as $key => $val) {
	    $sort[$key] = $val[$index];
	}

	//tri par ordre naturel et insensible à la casse
	natcasesort($sort);

	//formation du nouveau tableau trié selon la clé
	$output = array();
	foreach ($sort as $key => $val) {
	    $output[$key] = $array[$key];
	}
	return $output;
    }
    
    public static function append($array, $value){
	$array[] = $value;
    }
    
    public static function avg($array){
	$t = 0;
	foreach($array AS $v){
	    if(is_numeric($v)){
		$t += $v;
	    }
	}
	return $t/count($array);
    }
    
    public static function deleteAt($array, $index){
	if(count($array) < $index){
	    throw new \Exception('Index non valide : ce tableau a '.count($array).' élément(s)');
	    return $array;
	}
	
	unset($array[$index]);
	return $array;
    }
    
    public static function mergeArrays($array1, $array2){
	return array_merge($array1, $array2);
    }
    
    public static function insertAt($array, $index, $value){
	if(count($array) < $index){
	    throw new \Exception('Index non valide : ce tableau a '.count($array).' élément(s)');
	    return $array;
	}
	
	$tabLen = count($array);
	$tabEnd = array_slice ($array, $index, $tabLen-$index+1);
	$tabDeb	= array_slice ($array, 0, $index);
	if(!is_array($value)){
	    $value = array($value);
	}
	$tabOut = array_merge(array_merge($tabDeb, $value),$tabEnd);
	return $tabOut;
    }
    
    public static function max($array){
	$m = 0;
	foreach($array AS $v){
	    if(is_numeric($v) && $v > $m){
		$m = $v;
	    }
	}
	return $m;
    }
    
    public static function min($array){
	$m = null;
	foreach($array AS $v){
	    if(is_numeric($v) && (is_null($m) || $v < $m)){
		$m = $v;
	    }
	}
	return $m;
    }
    
    public static function prepend($array, $value){
	return self::insertAt($array, 0, $value);
    }
    
    public static function sortNumeric($array){
	sort($array, SORT_NUMERIC);
	return $array;
    }
    
    
    public static function sum($array){
	$t = 0;
	foreach($array AS $v){
	    if(is_numeric($v)){
		$t += $v;
	    }
	}
	return $t;
    }
    
    public static function toList($array, $separateur = ','){
	return StringTools::implode($array, $separateur);
    }
    
    public static function shuffle($array){
	shuffle($array);
	return $array;
    }
    
    public static function getAllValues($array){
	return array_values($array);
    }
    
    public static function deleteDoublons($array){
	return array_unique($array);
    }
    
    public static function getArrayPart($array, $index, $length){
	return array_slice($array, $index, $length);
    }
    
    public static function reverse($array){
	return array_reverse($array);
    }
    
    public static function getRandomValue($array, $num = 1){
	return array_rand($array, $num);
    }
    
    public static function find($array, $value){
	return array_search($value, $array);
    }
    
    public static function findNoCase($array, $value){
	return array_search(strtolower($value), array_map('strtolower', $array));
    }

}
