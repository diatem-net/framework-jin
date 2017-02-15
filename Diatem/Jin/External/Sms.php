<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\External;

use Diatem\Jin\Jin;
use Diatem\Jin\Com\WebService\WSClient;

/**
 * Envoi d'un SMS via le serveur de SMS Diatem
 */
class SMS
{

    /** Méthode permettant l'envoi du sms et sa sauvegarde dans la base de donnée
     *  @param      string      $numero         Numéro de téléphone
     *  @param      string      $message        Contenu du sms
     *  @param      string      $codeSociete    Société émettrice
     *  @param      string      $serveurSMS     Serveur d'envoi de sms
     *  @throws     Exception
     *  @return     boolean                     TRUE si le sms a bien été envoyé
     */
    public static function send($numero, $message, $codeSociete, $serveurSMS)
    {
        //On appelle le webservice qui s'occupe d'envoyer le SMS
        $client = new WSClient($serveurSMS);
        $client->setWSDLCacheEnabled(false);
        $client->service('sms', array('numero' => $numero, 'message' => $message, 'code_societe' => $codeSociete));

        return true;
    }

}
