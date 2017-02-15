<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Db;

use Diatem\Jin\Jin;

/**
 * Gestion de la connexion aux bases de données
 */
class DbConnexion
{
    /**
     * @var object  Objet connexion
     */
    public static $cnxHandler = null;

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
    public static function connectWithPostgreSql($host, $user, $pass, $port, $dbname)
    {
        self::$cnxHandler = new PostgreSQL($host, $user, $pass, $port, $dbname);
        return self::$cnxHandler->connect();
    }


    /**	Initialise la connexion sur une Base de données MySql
     *
     * @param string $host  Url du serveur MySql
     * @param string $user  Utilisateur de base de données
     * @param string $pass  Password de l'utilisateur
     * @param integer $port Port utilisé
     * @param string $dbname	Nom de la base de données
     * @return boolean	Succès ou echec de connexion
     */
    public static function connectWithMySql($host, $user, $pass, $port, $dbname)
    {
        self::$cnxHandler = new MySQL($host, $user, $pass, $port, $dbname);
        return self::$cnxHandler->connect();
    }


    /**	Initialise automatiquement une connexion sur un site géré avec WordPress
     */
    public static function connectWithWordPress()
    {
        include_once Jin::getAppPath().'wp-config.php';

        self::$cnxHandler = new MySQL(DB_HOST, DB_USER, DB_PASSWORD, 5432, DB_NAME);
        return self::$cnxHandler->connect();
    }

    public static function connectWithSqLite3($fileName)
    {
        self::$cnxHandler = new SQLite3($fileName);
        return self::$cnxHandler->connect();
    }

    /**	Initialise automatiquement une connexion sur un site géré avec Prestashop
     */
    public static function connectWithPrestashop()
    {
        include_once Jin::getAppPath().'config/settings.inc.php';

        self::$cnxHandler = new MySQL(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, 5432, _DB_NAME_);
        return self::$cnxHandler->connect();
    }

    /**	Initialise une transaction
     *
     */
    public static function beginTransaction()
    {
        self::$cnxHandler->beginTransaction();
    }


    /**	Effectue le commit de la transaction
     *
     */
    public static function commitTransaction()
    {
        self::$cnxHandler->commitTransaction();
    }


    /**	Annule la transaction
     *
     */
    public static function rollBackTransaction()
    {
        self::$cnxHandler->rollBackTransaction();
    }

    /**
     * Retourne le dernier ID inséré. (Avec MySql les arguments tableName et cle ne sont pas nécessaires)
     * @param string $tableName		Nom de la table
     * @param string $cle		Nom de la clé primaire
     */
    public static function getLastInsertId($tableName = null, $cle = null)
    {
        return self::$cnxHandler->getLastInsertId($tableName, $cle);
    }
}
