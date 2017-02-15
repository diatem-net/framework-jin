<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\FileSystem;

use Diatem\Jin\Jin;
use Diatem\Jin\Lang\ListTools;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\Log\Debug;

/**
 * Permet la gestion de fichiers publics sécurisés. Les fichiers sont stockés sous
 * un nom hashé associé à un fichier clé. L'appel du fichier en direct, sans le
 * passage de la clé de vérification est impossible.
 * Cette classe est destiné au stockage de fichiers à vocation de disponibilité
 * limitée aux ayant-droits ayant eu accès à l'url sécurisée.
 * Lors de son premier appel la classe génère un dossier "publicsecuredfile" à la
 * racine ainsi que les fichiers .htaccess et read.php associés.
 * Pour modifier ces fichiers, il faut surcharger l'asset publicsecuredfile.
 * Attention pour que les modifications soient prises en compte il faut supprimer
 * les fichiers .htaccess et read.php du dossier publicsecuredfile/
 */
class PublicSecuredFile {

    /* ----------------------------------------------------------------------- */
    /*PROPRIETES STATIQUES */

    /**
     * Dossier de stockage et d'accès des fichiers
     * @var string
     */
    private static $storePath = '/publicsecuredfiles/';

    /**
     * Longueur de la clé de sécurité générée
     * @var integer
     */
    private static $baseKeyLength = 16;

    /**
     * mode de calcul des clés de hashage (md4 par défaut)
     * @var string
     */
    private static $hashMethod = 'md5';

    /**
     * Méthode de cryptage des données (aes128 par défaut)
     * @var string
     */
    private static $encodeMethod = 'aes128';

    /**
     * Vecteur d'initialisation pour le cryptage des données
     * @var string
     */
    private static $initializationVector = '1234567812345678';

    /**
     * Clé privée
     * @var string
     */
    private static $privateKey = '67141ABCE7159153';

    /**
     * Nom du paramètres d'url
     * @var string
     */
    private static $urlKeyArg = 'k';


    /* ----------------------------------------------------------------------- */
    /* METHODES STATIQUES PUBLIQUES */

    /**
     * Retourne le nom du paramètre utilisé dans l'Url pour transmettre la clé
     * @return string
     */
    public static function getUrlKeyArg() {
        return self::$urlKeyArg;
    }


    /**
     * Modifie le nombre de caractères des clés de sécurité
     * @param integer $length
     */
    public static function setBaseKeyLength($length) {
        self::$baseKeyLength = $length;
    }


    /**
     * Ajoute une ressource sécurisée
     * @param string $fileToCopy    Chemin absolu ou relatif du fichier à copier
     * @param string $relativePath  Chemin relatif souhaité à l'intérieur du dossier publicsecuredfiles/ ('' par défaut)
     * @return array('read' => 'lien pour lecture', 'download' => 'lien pour téléchargement') Les liens fournis n'incluent pas l'arborescence inférieure à publicsecuredfiles/
     * @throws \Exception
     */
    public static function add($fileToCopy, $relativePath = '') {
        self::checkSecureFolder();

        //Nom du fichier
        $fileName = ListTools::last($fileToCopy, DIRECTORY_SEPARATOR);

        //Modifier la fin du nom du dossier
        if (!empty($relativePath) &&
                StringTools::right($relativePath, 1) != DIRECTORY_SEPARATOR) {
            $relativePath .= DIRECTORY_SEPARATOR;
        }

        //Clé de sécurité
        $secureKey = self::generateRandomKey();

        //Nom du fichier sécurisé
        $hashKey = hash(self::$hashMethod, $relativePath . $fileName . $secureKey);

        //Copie du fichier sécurisé
        $r = copy($fileToCopy, self::getFullStorePath() . $hashKey);
        if (!$r) {
            throw new \Exception('Impossible de copier le fichier ' . $fileToCopy);
        }

        //Création du fichier clé
        $fileVerifyContent = self::encodeValue($secureKey);
        $verifyFile = new File(self::getFullStorePath() . $hashKey . '.key', true);
        $verifyFile->write($fileVerifyContent);

        $finalPath = $relativePath . $fileName . '?' . self::$urlKeyArg . '=' . $secureKey;
        return array('read' => $finalPath, 'download' => $finalPath.'&d=1');
    }


    /**
     * Supprime une ressource à partir de son url
     * @param string $url   Url sécurisée
     * @throws \Exception
     */
    public static function deleteFromUrl($url){
        $url = StringTools::replaceAll($url, '&d=1', '');
        $parts = StringTools::explode($url, '?k=');

        if(count($parts) != 2){
            throw new \Exception('Url non valide');
        }
        $file = $parts[0];
        $cle = $parts[1];

        //Nom du fichier sécurisé
        $hashKey = hash(self::$hashMethod, $file . $cle);
        if(!is_file(self::getFullStorePath().$hashKey) ||
                !is_file(self::getFullStorePath().$hashKey.'.key')){
            throw new \Exception('Ressource inexistante');
        }

        unlink(self::getFullStorePath().$hashKey);
        unlink(self::getFullStorePath().$hashKey.'.key');
    }


    /**
     * Retourne le chemin absolu du dossier de stockage
     * @return string
     */
    public static function getFullStorePath() {
        return Jin::getAppPath() . self::$storePath;
    }


    /**
     * Retourne le chemin relatif du dossier de stockage
     * @return string
     */
    public static function getRelativeStorePath() {
        return self::$storePath;
    }


    /* ----------------------------------------------------------------------- */
    /* METHODES STATIQUES PRIVEES */


    /**
     * Génère une clé aléatoire unique
     * @return string
     */
    private static function generateRandomKey() {
        $cars = '0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F';
        $baseKey = '';
        for ($i = 0; $i < self::$baseKeyLength; $i++) {
            $baseKey .= ListTools::ListGetAt($cars, rand(0, ListTools::len($cars) - 1));
        }
        $baseKey .= uniqid();

        return $baseKey;
    }


    /**
     * Encode une valeur
     * @param string $valueToEncode Chaîne à encoder
     * @return string
     */
    private static function encodeValue($valueToEncode) {
        return openssl_encrypt($valueToEncode, self::$encodeMethod, self::$privateKey, false, self::$initializationVector);
    }


    /**
     * Décode une valeur préalablement encodée
     * @param string $valueToDecode Chaîne à décoder
     * @return string
     */
    private static function decodeValue($valueToDecode) {
        return openssl_decrypt($valueToDecode, self::$encodeMethod, self::$privateKey, false, self::$initializationVector);
    }


    /**
     * Vérifie que le dossier sécurisé soit présent et que les fichiers .htaccess et read.php soient présents
     */
    private static function checkSecureFolder() {
        if (!is_dir(self::getFullStorePath())) {
            mkdir(self::getFullStorePath());
        }
        if (!file_exists(self::getFullStorePath() . '.htaccess')) {
            $af = new AssetFile('publicsecuredfile/htaccess.tpl');
            $afc = $af->getContent();
            $f = new File(self::getFullStorePath() . '.htaccess', true);
            $f->write($afc);
        }
        if (!file_exists(self::getFullStorePath() . 'read.php')) {
            $af = new AssetFile('publicsecuredfile/read.php.tpl');
            $afc = $af->getContent();
            $afc = StringTools::replaceAll($afc, '%JINROOT%', Jin::getJinPath());
            $f = new File(self::getFullStorePath() . 'read.php', true);
            $f->write($afc);
        }
    }


    /* ----------------------------------------------------------------------- */
    /* ATTRIBUTS PUBLICS */


    /**
    * Dernière erreur rencontrée
    * @var string
    */
    private $lastError = '';

    /**
     * Initialisée avec succès
     * @var boolean
     */
    private $initialized = false;

    /**
     * Chemin d'accès
     * @var string
     */
    private $path;

    /**
     * Nom de la clé du fichier
     * @var string
     */
    private $accessKey;


    /* ----------------------------------------------------------------------- */
    /* CONSTRUCTEUR */

    /**
     * Constructeur
     * @param string $path      Chemin relatif. (à partir du dossier image)
     * @param string $secureKey Clé d'accès
     */
    public function __construct($path, $secureKey) {
        $this->path = $path;

        $hashKey = hash(self::$hashMethod, $path . $secureKey);
        $this->accessKey = $hashKey;

        if (file_exists(self::getFullStorePath() . $hashKey) &&
                file_exists(self::getFullStorePath() . $hashKey . '.key')) {

            $f = new File(self::getFullStorePath() . $hashKey . '.key');
            if (self::decodeValue($f->getContent()) == $_REQUEST[self::getUrlKeyArg()]) {
                $this->initialized = true;
            } else {
                $this->lastError = 'Paramètre de sécurité incorrect';
            }
        } else {
            $this->lastError = 'Fichier indisponible';
        }
    }

    /* ----------------------------------------------------------------------- */
    /* METHODES PUBLIQUES */


    /**
     * Retourne la dernière erreur rencontrée (en verbose)
     * @return string
     */
    public function getLastError() {
        return $this->lastError;
    }


    /**
     * Retourne si il s'agit d'une ressource valide. (Initialisée avec succès)
     * @return boolean
     */
    public function isValid() {
        return $this->initialized;
    }

    /**
     * Effectue le rendu de la ressource directement dans la sortie navigateur
     */
    public function renderInOutput() {
        if ($this->initialized) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $infos = finfo_file($finfo, self::getFullStorePath().$this->accessKey);

            header('Content-Type: '.$infos);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(self::getFullStorePath().$this->accessKey));
            readfile(self::getFullStorePath().$this->accessKey);

        } else {
            throw new \Exception('Connexion securisée au fichier non vérifiée.');
        }
    }


    /**
     * Force le téléchargement du fichier
     * @throws \Exception
     */
    public function forceDownload(){
        if ($this->initialized) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $infos = finfo_file($finfo, self::getFullStorePath().$this->accessKey);

            header('Content-Description: File Transfer');
            header('Content-Type: '.$infos);
            header('Content-Disposition: attachment; filename='.$this->path);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(self::getFullStorePath().$this->accessKey));
            readfile(self::getFullStorePath().$this->accessKey);

        } else {
            throw new \Exception('Connexion securisée au fichier non vérifiée.');
        }
    }


    /**
     * Supprime la ressource iconographique sécurisée
     */
    public function delete() {
        unlink(self::getFullStorePath() . $this->accessKey);
        unlink(self::getFullStorePath() . $this->accessKey . '.key');
    }

}
