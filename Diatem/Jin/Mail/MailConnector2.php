<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Mail;

use Diatem\Jin\Jin;

/**
 * Gestion d'une webmail v2 (avec https://github.com/SSilence/php-imap-client)
 */
class MailConnector2 {

	/** Boîte de réception
	 *
	 * 	@var object
	 *
	 */
	private $boite;

	/** Serveur mail, port, protocole, dossier (exemple : '{imap.gmail.com:993/imap/ssl}INBOX')
	 *
	 * 	@var string
	 *
	 */
	private $host;

	/** Nom d'utilisateur
	 *
	 * 	@var string
	 *
	 */
	private $user;

	/** Mot de passe du compte
	 *
	 * 	@var string
	 *
	 */
	private $pass;

	/**
	 * Encryption type
	 * @var string
	 */
	private $encryption;

	/** Constructeur
	 * 	@param		string	 	$host		Serveur mail, port, protocole, dossier
	 * 	@param 		string		$user		Nom d'utilisateur
	 * 	@param 		string		$pass		Mot de passe du compte
	 *  @params		string		$encryption Encryption type
	 * 	@throws		Exception
	 * 	@return		void
	 */
	public function __construct($host, $user, $pass, $encryption = 'tls') {
		//Vérifie que l'extension imap soit bien installée sur le serveur
		if (!extension_loaded('imap')) {
			throw new \Exception('Extension Imap nécessaire');
		}

		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->encryption = $encryption;

		require_once Jin::getJinPath().Jin::getRelativePathExtLibs().'imap-client/Imap.php';
	}

	/** Connexion à la boîte mail
	 * 	@return		void
	 */
	public function connect() {
		$this->boite = new \Imap($this->host, $this->user, $this->pass, $this->encryption);
	}

	/**
	 * Retourne un tableau des noms des dossiers
	 * @return array
	 */
	public function getFolders(){
		return $this->boite->getFolders();
	}


	/**
	 * Selectionne un dossier
	 * @param string $folderName	Nom du dossier
	 */
	public function selectFolder($folderName){
		$this->boite->selectFolder($folderName);
	}


	/**
	 *
	 * @return integerRetourne le nombre de messages non lus
	 * @return integer
	 */
	public function countUnreadMessages(){
		return $this->boite->countUnreadMessages();
	}


	/**
	 * Retourne le nombre total de messages
	 * @return integer
	 */
	public function countTotalMessages(){
		return $this->boite->countMessages();
	}

	/**
	 * Retourne si la connexion est bien initiée
	 * @return boolean
	 */
	public function isConnected(){
		return $this->boite->isConnected();
	}

	/** Récupère tous les emails
	 * 	@return		array				Tableau d'emails(pk,vu,sujet,expediteur,date,message,listPJ)
	 */
	public function getEmails($saveImageFilesInFolder = null) {
		return $this->boite->getMessages(true, $saveImageFilesInFolder);
	}

	/**
	 * Supprime un mail
	 * @param integer $id
	 */
	public function deleteMail($id){
		$this->boite->deleteMessage($id);
	}

	public function getAttachment($id, $num = 0){
		return $this->boite->getAttachment($id, $num);
	}


}
