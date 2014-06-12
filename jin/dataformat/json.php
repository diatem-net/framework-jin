<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\dataformat;


/** Gestion de chaînes JSon
 *
 * 	@auteur	    Loïc Gerard
 * 	@check
 */
class Json {

    /**
     * Encode un tableau au format JSON
     * @param array $data   Données à encoder
     * @return string
     */
    public static function encode($data) {
	return json_encode($data);
    }

    
    /**
     * Décode des données JSon en un tableau
     * @param string $data  Données JSon
     * @return array	NULL si une erreur survient
     */
    public static function decode($data) {
	return json_decode($data, true);
    }

    
    /**
     * Retourne le dernier code d'erreur retourné
     * @return int
     */
    public static function getLastErrorCode() {
	return json_last_error();
    }

    
    /**
     * Retourne le dernier message d'erreur retourné (verbose)
     * @return string
     */
    public static function getLastErrorVerbose() {
	switch (json_last_error()) {
	    case JSON_ERROR_NONE:
		return json_last_error().' - Aucune erreur';
		break;
	    case JSON_ERROR_DEPTH:
		return json_last_error().' - Profondeur maximale atteinte';
		break;
	    case JSON_ERROR_STATE_MISMATCH:
		return json_last_error().' - Inadéquation des modes ou underflow';
		break;
	    case JSON_ERROR_CTRL_CHAR:
		return json_last_error().' - Erreur lors du contrôle des caractères';
		break;
	    case JSON_ERROR_SYNTAX:
		return json_last_error().' - Erreur de syntaxe ; JSON malformé';
		break;
	    case JSON_ERROR_UTF8:
		return json_last_error().' - Caractères UTF-8 malformés, probablement une erreur d\'encodage';
		break;
	    default:
		return json_last_error().' - Erreur inconnue';
		break;
	}
    }

}
