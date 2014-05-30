<?php

namespace jin\dataformat;

class Json {

    public static function encode($data) {
	return json_encode($data);
    }

    public static function decode($data) {
	return json_decode($data, true);
    }

    public static function getLastErrorCode() {
	return json_last_error();
    }

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
