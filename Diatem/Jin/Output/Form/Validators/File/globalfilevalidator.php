<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators\File;

use Diatem\Jin\Output\Form\Validators\GlobalValidator;

/** Classe parent de tout filevalidator
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class GlobalFileValidator extends GlobalValidator{

    /**
     * Retourne le type de validateur
     * @return string
     */
    public function getType(){
	   return 'filevalidator';
    }
}

