<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\query;

use \PDO;

/**
 * Surcharge de PDO pour SqLite3
 */
class SQLitePDO extends PDO {

    /**
     * Constructeur
     * @param string $filename  Fichier de la base SqLite3
     */
    function __construct($filename) {
        $filename = realpath($filename);
        parent::__construct('sqlite:' . $filename);

        $key = ftok($filename, 'a');
        $this->sem = $this->sem_get($key);
    }

    
    /**
     * Débute une transaction
     * @return type
     */
    public function beginTransaction() {
        $this->sem_acquire($this->sem);
        return parent::beginTransaction();
    }

    
    /**
     * Commite une transaction
     * @return type
     */
    public function commit() {
        $success = parent::commit();
        $this->sem_release($this->sem);
        return $success;
    }

    /**
     * Effectue le rollback d'une transaction
     * @return type
     */
    public function rollBack() {
        $success = parent::rollBack();
        $this->sem_release($this->sem);
        return $success;
    }

    /**
     * Implémentation de la méthode PHP sem_get pour compatibilité descendante.
     * @param type $key
     * @return type
     */
    private function sem_get($key) {
        return fopen(__FILE__ . '.sem.' . $key, 'w+');
    }

    /**
     * Implémentation de la méthode PHP sem_acquire pour compatibilité descendante.
     * @param type $sem_id
     * @return type
     */
    private function sem_acquire($sem_id) {
        return flock($sem_id, LOCK_EX);
    }

    /**
     * Implémentation de la méthode PHP sem_release pour compatibilité descendante.
     * @param type $sem_id
     * @return type
     */
    private function sem_release($sem_id) {
        return flock($sem_id, LOCK_UN);
    }

}
