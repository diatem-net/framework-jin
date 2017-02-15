<?php
/**
* Jin Framework
* Diatem
*/

namespace Diatem\Jin\Output\Form\Validators\File;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\File\GlobalFileValidator;
use Diatem\Jin\Language\Trad;
use Diatem\Jin\Lang\ListTools;
use Diatem\Jin\Lang\StringTools;

/** Validateur : teste si le fichier est de types précisés
*
*  @auteur     Loïc Gerard
*  @version    0.0.1
*  @check
*/
class Allowedextensions extends GlobalFileValidator implements ValidatorInterface{
    /**
    * Constructeur
    * @param type $args    Tableau d'arguments. extensionList (Liste d'extensions supportées. ex: jpg,csv)
    */
    public function __construct($args) {
        parent::__construct($args, array('extensionList'));
    }

    /**
    * Teste la validité
    * @param array $valeur Valeur $_FILES à tester
    * @return boolean
    */
    public function isValid($valeur){
        parent::resetErrors();

        if(parent::getArgValue('extensionList') == '' || count(parent::getArgValue('extensionList')) == 0) {
            return true;
        }
        $currentExt = ListTools::last($valeur['name'], '.');
        if($currentExt && !ListTools::containsNoCase(parent::getArgValue('extensionList'), $currentExt)){
            $eMsg = Trad::trad('allowedextensions');
            parent::addError(StringTools::replaceAll($eMsg, '%extensionList%', parent::getArgValue('extensionList')));
            return false;
        }

        return true;
    }


    /**
    * Priorité NIV1 du validateur
    * @return boolean
    */
    public function isPrior(){
        return false;
    }
}

