<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\GlobalValidator;
use Diatem\Jin\Lang\StringTools;
use Diatem\Jin\Lang\ArrayTools;
use Diatem\Jin\Lang\ListTools;
use Diatem\Jin\Language\Trad;

/** Validateur : teste si les caractères d'une valeurs sont parmis une liste de caractères authorisés
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Charsinlist extends GlobalValidator implements ValidatorInterface{
    /**
     * Constructeur
     * @param type $args    Tableau d'arguments. chars (caractères autorisés, séparés par des virgules)
     */
    public function __construct($args) {
	parent::__construct($args, array('chars'));
    }

    /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	parent::resetErrors();

        if($valeur == '' || parent::getArgValue('chars') == ''){
            return true;
        }

        $chars = ListTools::toArray(parent::getArgValue('chars'));
        $stringChars = StringTools::explode($valeur);

        foreach($stringChars AS $char){
            if(ArrayTools::find($chars, $char) === false || ArrayTools::find($chars, $char) === null){
                $eMsg = Trad::trad('charsinlist');
                $eMsg = StringTools::replaceAll($eMsg, '%chars%', parent::getArgValue('chars'));
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


