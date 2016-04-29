<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\output\form\validators;

use jin\output\form\validators\ValidatorInterface;
use jin\output\form\validators\GlobalValidator;
use jin\language\Trad;
use jin\lang\StringTools;


/** Validateur : teste si une valeur est  supérieure ou égual à une valeur donnée
 *
 * 	@auteur		JLF
 * 	@version	0.0.1
 * 	@check		
 */
class Operator extends GlobalValidator implements ValidatorInterface{
    /**
     * Constructeur
     * @param type $args    Tableau d'arguments : operator->opérateur de test , value -> valeur à comparer )
     */
    public function __construct($args) {
	parent::__construct($args, array('operator','value'));
    }
    
    /**
     * Teste la validité
     * @param mixed $valeur Valeur à tester
     * @return boolean
     */
    public function isValid($valeur){
	parent::resetErrors();
	
        $value = $this->getArgValue('value');	
        $operator = $this->getArgValue('operator');
        $eMsg = '';
	
	if(!empty($valeur)){   
            
                    
            if($operator == '==' && !($valeur == $value)){ 
                $error = TRUE; 
                $eMsg = Trad::trad('operator_equal');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);
            }
            
            elseif($operator == '!=' && !($valeur != $value)){ 
                $error = TRUE;   
                $eMsg = Trad::trad('operator_notequal');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);
            }
            
            elseif($operator == '<' && !($valeur < $value)){ 
                $error = TRUE;   
                $eMsg = Trad::trad('operator_inferior');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);
            } 
            
            elseif($operator == '<=' && !($valeur <= $value)){ 
                $error = TRUE;   
                $eMsg = Trad::trad('operator_inferiororequal');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);  
            } 
            
            elseif($operator == '>' && !($valeur > $value)){ 
                $error = TRUE;   
                $eMsg = Trad::trad('operator_superior');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);
            }  
            
            elseif($operator == '>=' && !($valeur >= $value)){ 
                $error = TRUE;   
                $eMsg = Trad::trad('operator_superiororequal');
		$eMsg = StringTools::replaceAll($eMsg, '%value%', $value);  
            }  
            
            if($error){   
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

