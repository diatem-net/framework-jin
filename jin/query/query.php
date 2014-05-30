<?php

namespace jin\query;

use \Exception AS Exception;
use jin\db\DbConnexion;
use jin\lang\StringTools;
use \Pdo;
use jin\query\QueryResult;
use sylab\framework\query\QueryResult;
use sylab\system\frontend\cache\Cache;
use sylab\system\common\core\Buffer;
use sylab\system\common\exception\CustomException;
use sylab\system\common\analyse\GlobalPerfAnalyser;
use sylab\framework\lang\TimeTools;

/** Gestion d'une requête SQL
 *
 * 	@auteur		Loïc Gerard
 * 	@version	alpha
 * 	@check
 * 	@maj		11/04/2013	:	[Loïc gerard]	Création initiale de la classe
 * 	@maj		14/05/2013	:	[Loïc gerard]	Ajout de la méthode getSql()
 * 	@maj		17/07/2013	:	[Loïc Gerard]	Ajout de la méthode getResultsCount()
 *	@maj		28/11/2013	:	[Loïc Gerard]	Ajout de la méthode addToRequest()
 */
class Query {

    /** 	Liste des arguments
     * 	@var array
     */
    private $arguments = array();

    /** 	Type SQL chaîne de caractères
     * 	@var integer
     */
    public static $SQL_STRING = 1;

    /** 	Type SQL numérique
     * 	@var integer
     */
    public static $SQL_NUMERIC = 2;

    /** 	Type SQL booleen
     * 	@var boolean
     */
    public static $SQL_BOOL = 3;

    /** 	Query préparée
     * 	@var [PDO]
     */
    private $query = NULL;

    /** 	Requête SQL
     * 	@var string
     */
    private $sql = NULL;

    /** 	Résultats de la requête
     * 	@var array[]
     */
    private $resultat = NULL;

    /** 	Constructeur
     * 	@return	void
     */
    public function __construct() {
	
    }

    /** 	définit la requête à executer
     * 	@param	string 		sql		Requête SQL<br><i><b>NB :</b> On pourra utiliser le mot-clé <envname> pour faire référence au schéma de l'environnement courant.
     * 	@return	void
     */
    public function setRequest($sql) {
	$this->sql = StringTools::replaceAll($sql, '<envname>', 'global_' . Config::get('envName'));
	$this->query = DbConnexion::$cnxHandler->cnx->prepare($this->sql);
    }

    /** 	Ajoute une ligne à la requête à exécuter
     * @param string		sql	Nouvelle ligne à executer.<br><i><b>NB :</b> On pourra utiliser le mot-clé <envname> pour faire référence au schéma de l'environnement courant.
     * @return	void
     */
    public function addToRequest($sql) {
	$this->sql .= ' '.StringTools::replaceAll($sql, '<envname>', 'global_' . Config::get('envName'));
	$this->query = DbConnexion::$cnxHandler->cnx->prepare($this->sql);
    }

    /** 	Execute la requête
     * 	@return	void
     */
    public function execute($cacheEnabled = false) {

	//Gestion du cache
	if (GlobalPerfAnalyser::$enabled) {
	    $time = TimeTools::getTimestampInMs();
	}
	$mustRequest = true;
	$cacheKey = '';
	if ($cacheEnabled && Config::get('useCache') == 1 && Config::get('useCacheQuery') == 1) {
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
		$this->resultat = $this->query->fetchAll();
	    } catch (\Exception $e) {
		throw new CustomException($e->getMessage(), $e->getCode(), $e->getPrevious(), array(array('name' => 'Requête SQL', 'content' => $this->getSql())));
	    }

	    //Mise en cache
	    if ($cacheEnabled && Config::get('useCache') == 1 && Config::get('useCacheQuery') == 1) {
		Cache::saveInCache($cacheKey, $this->resultat);
	    }
	}
	if (GlobalPerfAnalyser::$enabled) {
	    $elapsed = TimeTools::getTimestampInMs() - $time;
	    GlobalPerfAnalyser::addQuery($this->getSql(), $elapsed, !$mustRequest);
	}

	return $res;
    }

    /** 	Retourne la requête SQL
     * 	@return string	Requête SQL
     */
    public function getSql() {
	$psql = $this->sql;

	foreach ($this->arguments as $a) {
	    $psql = StringTools::replaceFirst($psql, '\?', $a);
	}

	return $psql;
    }
    
    
    public function getRs(){
	return $this->resultat;
    }
    

    /** 	Retourne un objet QueryResult permettant de travailler avec les résultats de la requête
     * 	@return [QueryResult]	Objet \sylab\common\sgbd\QueryResult
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

    /** 	Permet de préparer une valeur dans une requête. (Equivalent de <cfqueryparam> en coldfusion)
     * 	@param 	mixed 	valeur		Valeur à intégrer dans la requête
     * 	@param 	integer	type		Type de valeur attendue (ex. Query::SQL_STRING)
     * 	@see	sylab.common.sgbd.Query#SQL_STRING
     * 	@see	sylab.common.sgbd.Query#SQL_NUMERIC
     * 	@see	sylab.common.sgbd.Query#SQL_BOOL
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
	    if (!is_string($valeur)) {
		throw new Exception('L\'argument n\'est pas de type SQL_STRING (valeur : '.$valeur.')');
	    }
	} else {
	    throw new Exception('Le type ' . $type . ' n\'est pas reconnu');
	}

	$this->arguments[] = $valeur;

	return '?';
    }

}
