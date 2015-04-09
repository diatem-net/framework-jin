<?php
/**
* Jin Framework
* Diatem
*/

namespace jin\log;

/**
 * Outils de notification à travers une session
 *
 * @auteur     Samuel Marchal
 * @version    0.0.1
 * @check      08/04/2015
*/
class Notifier {

    /**
     * Clé pour le stockage des notifications en session
     * @var string
     */
    const COOKIE_NAME = 'jin_notifications';

    /**
     * Statut standard pour les notifications
     * @var string
     */
    const STATUS_NOTICE = 'notice';

    /**
     * Statut d'erreur pour les notifications
     * @var string
     */
    const STATUS_ERROR = 'error';

    /**
     * Statut d'avertissement pour les notifications
     * @var string
     */
    const STATUS_WARNING = 'warning';

    /**
     * Statut de succès pour les notifications
     * @var string
     */
    const STATUS_SUCCESS = 'success';

    /**
     * Vérifie que le stockage en session existe
    */
    private static function checkStorage() {
        if( !isset($_COOKIE) || !isset($_COOKIE[self::COOKIE_NAME]) ) {
            self::clear();
        }
    }

    /**
     * Ajoute une(des) notification(s) dans la pile
     * @param  mixed   $notif    Notification(s) à stocker
     * @param  string  $status   Statut de la notification (optionel, uniquement si on ajoute une seule notification)
    */
    public static function push($notif, $status = self::STATUS_NOTICE) {
        self::checkStorage();
        if( is_string($notif) ) {
            $notif = array(array(
                'status'  => $status,
                'message' => $notif
            ));
        }
        $notifs = unserialize($_COOKIE[self::COOKIE_NAME]);
        foreach ($notif as $object) {
            if( !isset($object['message']) || !is_string($object['message']) ) {
                continue;
            }
            if( !isset($object['status']) || !in_array($object['status'], array(self::STATUS_NOTICE, self::STATUS_ERROR, self::STATUS_WARNING, self::STATUS_SUCCESS)) ) {
                $object['status'] = self::STATUS_NOTICE;
            }
            array_push($notifs, (object)$object);
        }
        setcookie(self::COOKIE_NAME, serialize($notifs), time()+180, "/");
    }

    /**
     * Récupère la dernière notification de la pile
     * @return object
    */
    public static function pull() {
        self::checkStorage();
        $notifs = unserialize($_COOKIE[self::COOKIE_NAME]);
        $firstnotif = array_shift($notifs);
        setcookie(self::COOKIE_NAME, serialize($notifs), time()+180, "/");
        return $firstnotif;
    }

    /**
     * Récupère toutes les notifications de la pile
     * @return array
    */
    public static function pullAll() {
        self::checkStorage();
        $notifs = unserialize($_COOKIE[self::COOKIE_NAME]);
        self::clear();
        return $notifs;
    }

    /**
     * Supprime toutes les notifications de la pile
     * @return array
    */
    public static function clear() {
        setcookie(self::COOKIE_NAME, serialize(array()), time()+180, "/");
    }


}
