<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

use Diatem\Jin\Output\Form\Validators\ValidatorInterface;
use Diatem\Jin\Output\Form\Validators\GlobalValidator;
use Diatem\Jin\Language\Trad;
use Diatem\Jin\Lang\StringTools;

/** Validateur : teste si le nombre de caractères du valeur entre dans une plage autorisée
 *
 * 	@auteur		Loïc Gerard
 */
class Length extends GlobalValidator implements ValidatorInterface{
    /**
     * Constructeur
     * @param type $args    Tableau d'arguments. (min : nombre max de caractères (-1 : pas de minimum) max : nombre max de caractères (-1 : pas de maximum)
     */
    public function __construct($args) {
        parent::__construct($args, array('min','max'));
    }

    /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	parent::resetErrors();

        $min = $this->getArgValue('min');
        $max = $this->getArgValue('max');

        if($min == $max){
            if(strlen($valeur) != $min){
                parent::addError($this->prepareError('length_exact'));
                return false;
            }
        }else if($min == -1){
            if(strlen($valeur) > $max){
                parent::addError($this->prepareError('length_max'));
                return false;
            }
        }else if($max == -1){
            if(strlen($valeur) < $min){
                parent::addError($this->prepareError('length_min'));
                return false;
            }
        }else{
            if(strlen($valeur) < $min || strlen($valeur) > $max){
                parent::addError($this->prepareError('length_range'));
                return false;
            }
        }

	return true;
    }


    /**
     * Formatte un texte d'erreur
     * @param string $code  Code erreur (cf. fichier langue formvalidators.ini)
     * @return string
     */
    private function prepareError($code){
        $min = $this->getArgValue('min');
        $max = $this->getArgValue('max');
        $erreur = Trad::trad($code);
        $erreur = StringTools::replaceAll($erreur, '%min%', $min);
        $erreur = StringTools::replaceAll($erreur, '%max%', $max);

        return $erreur;
    }


    /**
     * Priorité NIV1 du validateur
     * @return boolean
     */
    public function isPrior(){
	return false;
    }
}

