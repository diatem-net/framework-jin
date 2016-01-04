<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\db\nosql;

/** Connexion aux bases de données MongoDB
 *
 * 	@auteur		Loïc Gerard
 */
class MongoDB{
	/**
     * @var object  Objet connexion
     */
    private static $cnxHandler;
	
	/**
	 *
	 * @var string	Host
	 */
	private static $host;
	
	/**
	 *
	 * @var integer	Port
	 */
	private static $port;
	
	
	/**
	 *
	 * @var string DB actuellement selectionnée
	 */
	private static $db;
	
	
	//--------------------------------------------------------------------------
	//Gestion de la connexion
	
	/**
	 * Etablit la connexion avec le serveur MongoDB
	 * @param string $db	Database
	 * @param string $host	Host. (Localhost par défaut)
	 * @param type $port	Port. (27017 par défaut)
	 * @return	boolean
	 */
	public static function connect($db, $host = 'localhost', $port = 27017) {
		self::$cnxHandler = new \MongoClient($host.':'.$port);
		self::$db = self::$cnxHandler->$db;

		if(!self::$cnxHandler || !self::$db){
			return false;
		}else{
			return true;
		}
	}
	
	
	/**
	 * Vérifie que la connexion à  MongoDB est bien initialisée.
	 * @throws \Exception
	 */
	private static function checkConnexion(){
		if(!self::$cnxHandler){
			throw new \Exception('Connexion à MongoDB non initialisée. (Utilisez MongoDB::connect())');
		}
	}
	
	
	//--------------------------------------------------------------------------
	//Travail sur les documents
	
	
	/**
	 * Effectue une requête.
	 * @param string $collection	Collection
	 * @param array|string $query	Requête, au format JSON ou array
	 * @param array	$fields			Champs souhaités
	 * @return array
	 * @throws \Exception
	 */
	public static function find($collection, $query = null, $fields = array()){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		
		if(is_string($query)){
			$query = json_decode($query, true);
			if(!$query){
				throw new \Exception('Requête JSON formulée incorrectement');
			}
		}
		
		if($query){
			return $collection->find($query, $fields);
		}else{
			return $collection->find(array(), $fields);
		}
	}
	
	
	/**
	 * Modifie un ou plusieurs documents d'une collection.
	 * @param string $collection			Collection
	 * @param array|string $criteriaQuery	Critères de selection du ou des documents. Au format array ou JSON. Ex: array('id'=>1) ou '{"id":1}'
	 * @param array|string $data			Données Ã  mettre à  jour ou remplacer. Au format array ou JSON. Ex. pour remplacer : array('nom'=>'moi') ou '{"nom":"moi"}' et pour modifier : array("$set" => array("nom" => "moi)) ou '{"$set":{"nom":"moi"}}'
	 * @param boolean $multiple				par défaut : FALSE. Si true modifie N documents. Non compatible avec des données Ã  remplacer. (Uniquement mise à  jour de données)
	 * @return boolean
	 * @throws \Exception
	 */
	public static function update($collection, $criteriaQuery, $data, $multiple = false){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		
		if(is_string($criteriaQuery)){
			$criteriaQuery = json_decode($criteriaQuery, true);
			if(!$criteriaQuery){
				throw new \Exception('criteriaQuery JSON formulée incorrectement');
			}
		}
		
		if(is_string($data)){
			$data = json_decode($data, true);
			if(!$data){
				throw new \Exception('data JSON formulé incorrectement');
			}
		}
		
		$r = $collection->update($criteriaQuery, $data, array('multiple' => $multiple, 'upsert' => false));
		
		if($r['updatedExisting'] && $r['ok']){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Supprime un ou plusieurs enregistrements d'une collection
	 * @param string		$collection			Collection
	 * @param array|string	$criteriaQuery		Critères de selection du ou des documents. Au format array ou JSON. Ex: array('id'=>1) ou '{"id":1}'
	 * @return boolean
	 * @throws \Exception
	 */
	public static function delete($collection, $criteriaQuery = array()){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		
		if(is_string($criteriaQuery)){
			$criteriaQuery = json_decode($criteriaQuery, true);
			if(!$criteriaQuery){
				throw new \Exception('criteriaQuery JSON formulée incorrectement');
			}
		}
		
		$r = $collection->remove($criteriaQuery, array('justOne' => false));
		
		if($r['ok']){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Compte les résultats d'une requête
	 * @param string $collection	Collection
	 * @param array|string $query	Requête, au format JSON ou array
	 * @return integer
	 * @throws \Exception
	 */
	public static function count($collection, $query = null){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		
		if(is_string($query)){
			$query = json_decode($query, true);
			if(!$query){
				throw new \Exception('Requête JSON formulée incorrectement');
			}
		}
		
		if($query){
			return $collection->count($query);
		}else{
			return $collection->count();
		}
	}
	
	
	/**
	 * Ajoute un nouveau document à une collection
	 * @param type $collection
	 * @param type $data
	 * @return boolean
	 * @throws \Exception
	 */
	public static function add($collection, $data){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		
		
		if(is_string($data)){
			$data = json_decode($data, true);
			if(!$data){
				throw new \Exception('Requête JSON formulée incorrectement');
			}
		}
		
		$r = $collection->insert($data);
		
		if($r['ok']){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * Crée et retourne une référence à un document.
	 * @param string $collection			Nom de la collection
	 * @param string|integer $documentId	Id du document
	 * @return array
	 */
	public static function getDbRef($collection, $documentId){
		self::checkConnexion();
		
		$ref = self::$db->createDBRef($collection, $documentId);
		return $ref;
	}
	
	
	//--------------------------------------------------------------------------
	//Travail sur les collections
	
	
	/**
	 * Supprime une collection
	 * @param string $collection	Nom de la Collection
	 * @return boolean
	 */
	public static function dropCollection($collection){
		self::checkConnexion();
		
		$collection = self::$db->$collection;
		$r = $collection->drop();
		
		if($r['ok']){
			return true;
		}
		return false;
	}
	
	
	/**
	 * Ajoute une collection
	 * @param string $collection	Nom de la collection
	 * @return boolean
	 */
	public static function addCollection($collection){
		self::checkConnexion();
		
		$r = self::$db->createCollection($collection);
	
		if(gettype($r) == 'object' && get_class($r) == 'MongoCollection'){
			return true;
		}
		
		return false;
	}

	
}