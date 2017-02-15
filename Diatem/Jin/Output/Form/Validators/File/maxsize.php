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
use Diatem\Jin\Lang\NumberTools;

/** Validateur : teste si le fichier a une taille (en octets) maximum
*
* 	@auteur		Loïc Gerard
* 	@version	0.0.1
* 	@check
*/
class Maxsize extends GlobalFileValidator implements ValidatorInterface{
    /**
    * Constructeur
    * @param type $args    Tableau d'arguments. maxsize (Taille maximale (en octets))
    */
    public function __construct($args) {
        parent::__construct($args, array('maxsize'));
    }

    /**
    * Teste la validité
    * @param array $valeur Valeur $_FILES à tester
    * @return boolean
    */
    public function isValid($valeur){
        parent::resetErrors();

        if(isset($valeur['size'])){
            if(parent::getArgValue('maxsize') <= 0) {
                return true;
            }
            if($valeur['size'] > parent::getArgValue('maxsize')){
                $eMsg = Trad::trad('maxsize');

                $o = parent::getArgValue('maxsize');
                $ko = parent::getArgValue('maxsize')/1024;
                $mo = parent::getArgValue('maxsize')/1024/1024;

                $msize = $o.' octets';
                if($mo > 1){
                    $msize = NumberTools::numberFormat($mo, 2).' mo';
                }else if($ko > 1){
                    $msize = NumberTools::numberFormat($ko, 2).' ko';
                }

                $eMsg = StringTools::replaceAll($eMsg, '%maxsize%', $msize);
                parent::addError($eMsg);

                return false;
            }
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

