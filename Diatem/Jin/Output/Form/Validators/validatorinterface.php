<?php

/**
 * Jin Framework
 * Diatem
 */

namespace Diatem\Jin\Output\Form\Validators;

/** Interface pour les Validateurs
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
Interface ValidatorInterface{
    public function __construct($args);
    public function isValid($valeur);
    public function isPrior();
    public function getType();
}
