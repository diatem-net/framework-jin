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

        $key = $this->ftok($filename, 'a');
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
    
    
    /**
     * Implémentation méthode ftok pour support sous Windows
     * @param string $filename
     * @param string $proj
     * @return string
     */
    private function ftok($filename = "", $proj = ""){
        if(!function_exists('ftok')){
            if(empty($filename) || !file_exists($filename)){
                return -1;
            }else{
                $filename = $filename . (string) $proj;
                for($key = array(); sizeof($key) < strlen($filename); $key[] = ord(substr($filename, sizeof($key), 1)));
                return dechex(array_sum($key));
            }
        }else{
            return ftok($filename, $proj);
        }
    }

}
