<?php

/**
 * Jin Framework
 * Diatem
 */

namespace sylab\system\frontend\cache;

use \DateTime;
use sylab\system\common\core\Config;
use sylab\system\interfaces\CacheInterface;
use \Memcache;

/** Gestion du cache via memCache. (Nécessite que le serveur soit configuré et fonctionne nominativement.)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 * 	@maj		04/06/2013	:	[Loïc gerard]	Création initiale de la classe
 */
class MemcacheCache implements CacheInterface {

    /**
     * 	Serveur MemCache
     */
    private $memcache = NULL;

    /**
     * 	Constructeur
     * 	@return void
     */
    public function __construct() {
	$this->memcache = new Memcache();
	$this->memcache->addServer(Config::get('memcacheHost'), Config::get('memcachePort'));
    }

    /**
     * 	Permet de savoir si une clé donnée est définie dans le cache
     * 	@param 	String	$key		Clé à rechercher
     * 	@return boolean			TRUE si définie dans le cache
     */
    public function isInCache($key) {
	$valeur = $this->memcache->get($this->buildMKey($key));

	if ($valeur) {
	    return true;
	} else {
	    return false;
	}
    }

    /**
     * 	Permet de retourner une valeur du cache à partir de sa clé.
     * 	@param 	String	$key		Clé à rechercher
     * 	@return	mixed			Valeur trouvée ou NULL si aucune valeur n'est trouvée
     */
    public function getFromCache($key) {
	$valeur = $this->memcache->get($this->buildMKey($key));

	if ($valeur) {
	    return unserialize($valeur);
	} else {
	    return NULL;
	}
    }

    /**
     * 	Supprime une valeur du cache
     * 	@param 	String	$key		Clé à supprimer
     * 	@return	void
     */
    public function deleteFromCache($key) {
	$this->memcache->delete($this->buildMKey($key));
    }

    /**
     * 	Sauvegarde une valeur dans le cache
     * 	@param	String	$key		Clé à sauvegarder
     * 	@param 	mixed	$value	Valeur à sauvegarder
     * 	@return	void
     */
    public function saveInCache($key, $value) {
	$this->deleteFromCache($key);

	$this->memcache->set($this->buildMKey($key), serialize($value));
    }

    /**
     * 	Supprime tout le contenu du cache
     * 	@return	void
     */
    public function clearCache() {
	$this->memcache->flush();
    }

    /**
     * 	Retourne une clé MemCache Unique à partir d'une clé standard
     * 	@param	String 	$key		Clé à rendre unique
     * 	@return string			Clé unique
     */
    private function buildMKey($key) {
	return Config::get('envName') . '_' . $key;
    }

}
