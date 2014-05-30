<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\lang;

/** Boite à outils pour les opérations temporelles
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		05/05/2014
 */
class TimeTools {
    /**	Retourne le timestamp courant en milisecondes
     * 
     * @return integer	Timestamp courant en MS
     */
    public static function getTimestampInMs() {
	return round(microtime(true) * 1000);
    }
}
