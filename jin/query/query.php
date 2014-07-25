<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\query;

use \Exception AS Exception;
use jin\db\DbConnexion;
use jin\lang\StringTools;
use \Pdo;
use jin\query\QueryResult;
use jin\lang\TimeTools;
use jin\cache\Cache;
use jin\log\Debug;

/** Gestion d'une requête SQL
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Query {

    /** 	
     * 	@var array  Liste des arguments
     */
    private $arguments = array();

    
    /** 	
     * 	@var integer	Type SQL chaîne de caractères
     */
    public static $SQL_STRING = 1;

    
    /** 	
     * 	@var integer	Type SQL numérique
     */
    public static $SQL_NUMERIC = 2;

    
    /**
     * 	@var boolean	 Type SQL booleen
     */
    public static $SQL_BOOL = 3;

    
    /** 
     * 	@var PDO  Query préparée
     */
    private $query = NULL;

    
    /** 
     * 	@var string Requête SQL
     */
    private $sql = NULL;

    
    /** 
     * 	@var array[]	Résultats de la requête
     */
    private $resultat = NULL;

    
    /** Constructeur
     * 	@return	void
     */
    public function __construct() {
    }

    
    /** définit la requête à executer
     * 	@param	string 		$sql		Requête SQL
     * 	@return	void
     */
    public function setRequest($sql) {
	$this->sql = $sql;
	$this->query = DbConnexion::$cnxHandler->cnx->prepare($sql);
    }

    
    /** Ajoute une ligne à la requête à exécuter
     * @param string		$sql	Nouvelle ligne à executer.
     * @return	void
     */
    public function addToRequest($sql) {
	$this->sql = $sql;
	$this->query = DbConnexion::$cnxHandler->cnx->prepare($sql);
    }

    /** Execute la requête
     * 	@return	void
     */
    public function execute($cacheEnabled = false) {

	//Gestion du cache
	$time = TimeTools::getTimestampInMs();
	
	$mustRequest = true;
	$cacheKey = '';
	if ($cacheEnabled) {
	    $psql = $this->getSql();

	    $cacheKey = 'sql_' . StringTools::hashCode($psql);

	    $valeur = Cache::getFromCache($cacheKey);
	    if (!is_null($valeur)) {
		$mustRequest = false;
		$this->resultat = $valeur;
		$res = true;
	    }
	}

	if ($mustRequest) {
	    try {
		$this->query->setFetchMode(PDO::FETCH_BOTH);
		$res = $this->query->execute($this->arguments);
		try{	$this->resultat = $this->query->fetchAll(); }catch(\Exception $e2){ }
	    } catch (\Exception $e) {
		throw new \Exception($e->getMessage());
	    }

	    //Mise en cache
	    if ($cacheEnabled) {
		Cache::saveInCache($cacheKey, $this->resultat);
	    }
	}
	
	return $res;
    }
    

    
    /** Retourne la requête SQL
     * 	@return string	Requête SQL
     */
    public function getSql() {
	$psql = $this->sql;

	foreach ($this->arguments as $a) {
	    $psql = StringTools::replaceFirst($psql, '\?', $a);
	}

	return $psql;
    }
    
    
    /**
     * Retourne le ResultSet
     * @return ResultSet    résultats de la reaquête PDO
     */
    public function getRs(){
	return $this->resultat;
    }
    

    /** Retourne un objet QueryResult permettant de travailler avec les résultats de la requête
     * 	@return QueryResult Instance de jin/query/QueryResult
     */
    public function getQueryResults() {
	return new QueryResult($this->resultat);
    }

    
    /** Retourne le nombre de lignes retournées par la requête
     * 	@return	integer	Nombre de lignes
     */
    public function getResultsCount() {
	return count($this->resultat);
    }

    
    /** Permet de préparer une valeur dans une requête. (Equivalent de <cfqueryparam> en coldfusion)
     * 	@param 	mixed 	$valeur		Valeur à intégrer dans la requête
     * 	@param 	integer	$type		Type de valeur attendue (ex. Query::SQL_STRING)
     * 	@see	Query#SQL_STRING
     * 	@see	Query#SQL_NUMERIC
     * 	@see	Query#SQL_BOOL
     * 	@throws Exception
     * 	@return string	Caractère de remplacement
     */
    public function argument($valeur, $type) {
	if ($type == self::$SQL_BOOL) {
	    if (!is_bool($valeur)) {
		throw new Exception('L\'argument n\'est pas de type SQL_BOOL (valeur : '.$valeur.')');
	    }
	    if ($valeur) {
		$valeur = 'TRUE';
	    } else {
		$valeur = 'FALSE';
	    }
	} elseif ($type == self::$SQL_NUMERIC) {
	    if (!is_numeric($valeur)) {
		throw new Exception('L\'argument n\'est pas de type SQL_NUMERIC (valeur : '.$valeur.')');
	    }
	} elseif ($type == self::$SQL_STRING) {
	    if (!is_string($valeur) && !is_numeric($valeur)) {
		throw new Exception('L\'argument n\'est pas de type SQL_STRING (valeur : '.$valeur.')');
	    }
	} else {
	    throw new Exception('Le type ' . $type . ' n\'est pas reconnu');
	}

	$this->arguments[] = $valeur;

	return '?';
    }

}
