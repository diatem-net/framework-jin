<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\db;

use jin\db\PostgreSql;
use \Exception;

/** Gestion de la connexion aux bases de données
 *
 * 	@auteur		Loïc Gerard
 * 	@version	alpha
 * 	@check
 */
class DbConnexion {
    /**
     * @var object  Objet connexion
     */
    public static $cnxHandler = NULL;
    
    /**
     * @var boolean Indique si la connexion a été ouverte avec succès 
     */
    private static $cnxOpened = false;

    
    /**	Initialise la connexion sur une Base de données PostgreSQL
     * 
     * @param string $host  Url du serveur PostgreSQL
     * @param string $user  Utilisateur de base de données
     * @param string $pass  Password de l'utilisateur
     * @param integer $port Port utilisé
     * @param string $dbname	Nom de la base de données
     * @return boolean	Succès ou echec de connexion
     */
    public static function connectWithPostgreSql($host, $user, $pass, $port, $dbname) {
	self::$cnxHandler = new PostgreSql($host, $user, $pass, $port, $dbname);
	return self::$cnxHandler->connect();
    }

    
    /**	Initialise une transaction
     * 
     */
    public static function beginTransaction() {
	self::$cnxHandler->beginTransaction();
    }

    
    /**	Effectue le commit de la transaction
     * 
     */
    public static function commitTransaction() {
	self::$cnxHandler->commitTransaction();
    }

    
    /**	Annule la transaction
     * 
     */
    public static function rollBackTransaction() {
	self::$cnxHandler->rollBackTransaction();
    }

}