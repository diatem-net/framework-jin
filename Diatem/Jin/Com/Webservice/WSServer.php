<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Com\Webservice;

use Diatem\Jin\Jin;

/** 
 * Outil de création de WebService à partir d'une classe
 */
class WsServer
{

    /** Chemin du fichier de classe php
     * 	@var string
     */
    private $classFile = null;


    /** Url du fichier service php
     * 	@var string
     */
    private $endPoint = null;


    /** Nom de la classe utilisée comme WebService
     * 	@var string
     */
    private $className = null;


    /** Namespace du WebService
     * 	@var string
     */
    private $namespace = null;


    /** Constructeur
     * 	@param 	string 	$classFile		Fichier PHP à utiliser comme WebService
     * 	@param 	string 	$className		Nom de la classe
     * 	@param 	string 	$endPoint		Url du fichier instanciant la classe WSServer (url d'accès du WebService)
     * 	@param 	string 	$namespace		Espace de nom du WebService
     * 	@return	void
     */
    public function __construct($classFile, $className, $endPoint, $namespace)
    {
        $this->classFile = $classFile;
        $this->endPoint = $endPoint;
        $this->className = $className;
        $this->namespace = $namespace;
    }


    /** Construction du service
     * 	@return	void
     */
    public function buildService()
    {
        if (!isset($_GET['wsdl'])) {
            //MODE EXECUTION

        require_once($this->classFile);
            $wsdl = $this->generateWsdl();

            $file = 'data://text/plain;base64,' . base64_encode($wsdl);
            $server = new \SoapServer($file);

            $server->setClass($this->className);
            $funcs = get_class_methods($this->className);
            foreach ($funcs as $f) {
                $server->addFunction($f);
            }

            $server->handle();
        } else {
            //MODE WSDL
        echo $this->generateWsdl();
        }
    }


    /** Génération du WSDL à la volée
     * 	@return	void
     * 	@todo	Mise en cache du WSDL + Chemin d'accès aux classes externes
     */
    private function generateWsdl()
    {
        error_reporting(0);
        set_error_handler(null);

        require_once(Jin::getJinPath() . '_extlibs/katywsdl/classes/WsdlDefinition.php');
        require_once(Jin::getJinPath() . '_extlibs/katywsdl/classes/WsdlWriter.php');

        $def = new \WsdlDefinition();
        $def->setDefinitionName($this->className);
        $def->setClassFileName($this->classFile);
        $def->setNameSpace($this->namespace);
        $def->setEndPoint($this->endPoint);

        $wsdl = new \WsdlWriter($def);
        return $wsdl->classToWsdl();
    }
}
