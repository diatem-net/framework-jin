<?php

namespace sylab\system\frontend\cache;

use sylab\system\common\core\Config;
use sylab\system\frontend\cache\DatabaseCache;
use sylab\system\frontend\cache\MemcacheCache;
use \Exception;
use sylab\framework\log\Debug;

/** Classe principale de gestion du cache. (Se charge d'initialiser les classes spécifiques de gestion du cache
 *	En fonction de la configuration de l'environnement.
 *
 *	@auteur		Loïc Gerard
 *	@version	alpha
 *	@check
 *	@maj		04/06/2013	:	[Loïc gerard]	Création initiale de la classe
 */
class Cache {
	/**
	 *	Classe de gestion du cache.
	*	@var 	mixed
	*	@see	sylab\common\cache\DatabaseCache
	*	@see	sylab\common\cache\FileCache
	*	@see	sylab\common\cache\MemCacheCache
	 */
	private static $cm = null;


	/**
	 * 	Permet de savoir si une clé donnée est définie dans le cache
	*	@param 	String	key		Clé à rechercher
	*	@return boolean			TRUE si définie dans le cache
	 */
	public static function isInCache($key){
		self::initialize();

		if(self::$cm){
			return self::$cm->isInCache($key);
		}else{
			return false;
		}
	}

	/**
	 * 	Permet de retourner une valeur du cache à partir de sa clé.
	*	@param 	String	key		Clé à rechercher
	*	@return	mixed			Valeur trouvée ou NULL si aucune valeur n'est trouvée
	 */
	public static function getFromCache($key){
		self::initialize();

		if(self::$cm){
			$v = self::$cm->getFromCache($key);
			return $v['value'];
		}else{
			return NULL;
		}
	}

	/**
	 * 	Permet de retourner la valeur/date du cache à partir de sa clé.
	 *	@param 	String	key		Clé à rechercher
	 *	@return	Array			Valeur trouvée ou NULL si aucune valeur n'est trouvée. (array('time' => t, 'value' => v))
	 */
	public static function getFromCacheWithDate($key){
		self::initialize();

		if(self::$cm){
			return self::$cm->getFromCache($key);
		}else{
			return NULL;
		}
	}


	/**
	 * 	Supprime une valeur du cache
	*	@param 	String	key		Clé à supprimer
	*	@return	void
	 */
	public static function deleteFromCache($key){
		self::initialize();

		if(self::$cm){
			self::$cm->deleteFromCache($key);
		}
	}


	/**
	 * 	Sauvegarde une valeur dans le cache
	*	@param	String	key		Clé à sauvegarder
	*	@param 	mixed	value	Valeur à sauvegarder
	*	@return	void
	 */
	public static function saveInCache($key, $value){
		self::initialize();

		if(self::$cm){
			self::$cm->saveInCache($key, array('time' => time(), 'value' => $value));
		}
	}


	/**
	 * 	Supprime tout le contenu du cache
	 * 	@return	void
	 */
	public static function clearCache(){
		self::initialize();

		if(self::$cm){
			self::$cm->clearCache();
		}
	}


	/**
	 * 	Initialise le cache en fonction de la configuration de l'environnement
	 * 	@return	void
	*	@throws Exception
	 */
	private static function initialize(){
		if(is_null(self::$cm) && Config::isConfigLoaded()){
			if(Config::get('useCache') == 1){
				$cmode = Config::get('cacheMode');

				if($cmode == 'database'){
					self::$cm = new DatabaseCache();
				}elseif($cmode == 'memcache'){
					self::$cm = new MemcacheCache();
				}elseif($cmode == 'file'){
					self::$cm = new FileCache();
				}else{
					throw new Exception('Le système de gestion de cache '.$cmode.' n\'est pas supporté par le système');
				}

			}
		}
	}

	//---------------------------------------------------------------------------------------------------------------


	/**	Retourne le préfixe utilisé pour les clés cache des noeuds
	*	@return string	Préfixe
	*/
	public static function getNodeCachePrefixe(){
		return 'node_';
	}

	/**	Retourne le préfixe utilisé pour les clés cache des blocs
	 *	@return string	Préfixe
	 */
	public static function getBlocCachePrefixe(){
		return 'bloc_';
	}

	/** Retourne le préfixe utilisé pour les clés cache des modules
	*	@return string	Prefixe
	*/
	public static function getModuleCachePrefixe(){
		return 'module_';
	}

	/** Retourne le préfixe utilisé pour les clés cache des templates
	 *	@return string	Prefixe
	 */
	public static function getTemplateCachePrefixe(){
		return 'template_';
	}
}