<?php

namespace sylab\system\frontend\cache;

use sylab\framework\query\Query;
use sylab\system\common\core\Buffer;
use \DateTime;
use sylab\system\common\core\Config;
use sylab\system\interfaces\CacheInterface;
use sylab\system\common\sgbd\DbConnexion;

/** Gestion du cache via la base de données
 *
 *	@auteur		Loïc Gerard
 *	@version	alpha
 *	@check
 *	@maj		04/06/2013	:	[Loïc gerard]	Création initiale de la classe
 */
class DatabaseCache implements CacheInterface{
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

		$query = new Query();
		$query->setRequest('
		SELECT * FROM <envname>.tb_cache
		WHERE tt_key='.$query->argument($key, Query::$SQL_STRING).'
		');

		$query->execute();

		$resultat = $query->getQueryResults();
		if($resultat->count() == 0){
			return false;
		}else{
			return true;
		}

	}


	/**
	 * 	Permet de retourner une valeur du cache à partir de sa clé.
	 *	@param 	String	key		Clé à rechercher
	 *	@return	mixed			Valeur trouvée ou NULL si aucune valeur n'est trouvée
	 */
	public function getFromCache($key){
		$query = new Query();
		$query->setRequest('
		SELECT * FROM <envname>.tb_cache
		WHERE tt_key='.$query->argument($key, Query::$SQL_STRING).'
		');

		$query->execute();

		$resultat = $query->getQueryResults();
		if($resultat->count() == 0){
			return NULL;
		}else{
			return unserialize($resultat->getValueAt('tt_value'));
		}
	}


	/**
	 * 	Supprime une valeur du cache
	 *	@param 	String	key		Clé à supprimer
	 *	@return	void
	 */
	public function deleteFromCache($key){
		$query = new Query();
		$sql = '
		DELETE
		FROM <envname>.tb_cache
		WHERE tt_key='.$query->argument($key, Query::$SQL_STRING).'
		';

		$query->setRequest($sql);
		$query->execute();
	}


	/**
	 * 	Sauvegarde une valeur dans le cache
	 *	@param	String	key		Clé à sauvegarder
	 *	@param 	mixed	value	Valeur à sauvegarder
	 *	@return	void
	 */
	public function saveInCache($key, $value){
		//Ouverture d'une transaction
		DbConnexion::beginTransaction();

		//On supprime si la clé est déjà présente
		$this->deleteFromCache($key);

		//On sérialise les données
		$value = serialize($value);

		//Insérer dans le cache
		$query = new Query();
		$sql = '
		INSERT INTO <envname>.tb_cache
		(
			tt_key,
			tt_value,
			tt_cachetype
		)
		VALUES
		(
			'.$query->argument($key, Query::$SQL_STRING).',
			'.$query->argument($value, Query::$SQL_STRING).',
			\'QUERY\'
		)
		';
		$query->setRequest($sql);
		$query->execute();

		//Fermeture de la transaction
		DbConnexion::commitTransaction();
	}


	/**
	 * 	Supprime tout le contenu du cache
	 * 	@return	void
	 */
	public function clearCache(){
		$query = new Query();


		$sql = '
		DELETE FROM <envname>.tb_cache
		';

		$query->setRequest($sql);
		$query->execute();

	}



}