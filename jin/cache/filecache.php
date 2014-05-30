<?php

namespace sylab\system\frontend\cache;

use \DateTime;
use sylab\system\common\core\Config;
use sylab\system\interfaces\CacheInterface;
use sylab\framework\lang\StringTools;


/** Gestion du cache via le système de fichiers
 *
 *	@auteur		Loïc Gerard
 *	@version	alpha
 *	@check
 *	@maj		04/06/2013	:	[Loïc gerard]	Création initiale de la classe
 */
class FileCache implements CacheInterface{

	/**
	 * 	Constructeur
	 * 	@return void
	 */
	public function __construct(){

	}


	/**
	 * 	Permet de savoir si une clé donnée est définie dans le cache
	 *	@param 	String	key		Clé à rechercher
	 *	@return boolean			TRUE si définie dans le cache
	 */
	public function isInCache($key){
		if(file_exists(ENV_ROOT.'cache/'.$this->getEncodedKey($key))){
			return true;
		}else{
			return false;
		}
	}


	/**
	 * 	Permet de retourner une valeur du cache à partir de sa clé.
	 *	@param 	String	key		Clé à rechercher
	 *	@return	mixed			Valeur trouvée ou NULL si aucune valeur n'est trouvée
	 */
	public function getFromCache($key){
		$key = $this->getEncodedKey($key);

		if(file_exists(ENV_ROOT.'cache/'.$key)){
			return unserialize(file_get_contents('cache/'.$key));
		}else{
			return NULL;
		}
	}


	/**
	 * 	Supprime une valeur du cache
	 *	@param 	String	key		Clé à supprimer
	 *	@return	void
	 */
	public function deleteFromCache($key){
		$key = $this->getEncodedKey($key);
		if(file_exists(ENV_ROOT.'cache/'.$key)){
			unlink('cache/'.$key);
		}
	}


	/**
	 * 	Sauvegarde une valeur dans le cache
	 *	@param	String	key		Clé à sauvegarder
	 *	@param 	mixed	value	Valeur à sauvegarder
	 *	@return	void
	 */
	public function saveInCache($key, $value){
		$key = $this->getEncodedKey($key);
		$this->deleteFromCache($key);

		file_put_contents(ENV_ROOT.'cache/'.$key, serialize($value), LOCK_EX);
	}


	/**
	 * 	Supprime tout le contenu du cache
	 * 	@return	void
	 */
	public function clearCache(){
		$files = glob(ENV_ROOT.'cache/*');
		foreach($files as $file){
			if(is_file($file)){
				unlink($file);
			}

		}
	}


	/**
	 * 	Retourne une clé encodée à partir d'une clé en clair
	*	@param	String 	key		Clé à encoder
	*	@return string			Clé encodée
	 */
	private function getEncodedKey($key){
		return StringTools::hashCode($key);
	}



}