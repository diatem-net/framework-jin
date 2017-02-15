<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\GlobalValidator;
use Diatem\Jin\Language\Trad;

/** Validateur : teste si une valeur est un numérique entier
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Isint extends GlobalValidator implements ValidatorInterface{
    /**
     * Constructeur
     * @param type $args    Tableau d'arguments. (Aucun argument requis)
     */
    public function __construct($args) {
	parent::__construct($args, array());
    }

    /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	parent::resetErrors();
	if(!empty($valeur) && !ctype_digit($valeur)){
            parent::addError(Trad::trad('isint'));
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

