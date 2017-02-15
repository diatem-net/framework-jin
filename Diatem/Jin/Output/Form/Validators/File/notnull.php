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

/** Validateur : teste si le fichier est renseigné
*
* 	@auteur		Loïc Gerard
* 	@version	0.0.1
* 	@check
*/
class Notnull extends GlobalFileValidator implements ValidatorInterface{
    /**
    * Constructeur
    * @param type $args    Tableau d'arguments. (Aucun argument requis))
    */
    public function __construct($args) {
        parent::__construct($args, array());
    }


    /**
    * Teste la validité
    * @param array $valeur Valeur $_FILES à tester
    * @return boolean
    */
    public function isValid($valeur){
        parent::resetErrors();

        if($valeur['name'] == ''){
            parent::addError(Trad::trad('filenotnull'));

            return false;
        }

        return true;
    }


    /**
    * Priorité NIV1 du validateur
    * @return boolean
    */
    public function isPrior(){
        return true;
    }
}

