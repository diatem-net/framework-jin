<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\output\components\form;

use jin\output\components\GlobalComponent;
use jin\lang\StringTools;

/** Classe parent de tout composant de type FORM (destinés à être utilisés avec des balises FORM)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		
 */
class FormComponent extends GlobalComponent{
    /**
     *
     * @var string Texte d'erreur affiché
     */
    private $error = '';
    
    /**
     *
     * @var string  Valeur actuelle
     */
    private $value = '';
    
    /**
     *
     * @var string  Valeur par défaut
     */
    private $defaultvalue = '';
    
    /**
     *
     * @var string  Label affiché
     */
    private $label = '';
    
    /**
     *
     * @var string Personnalisation de la balise style
     */
    private $stylecss = '';
    
    
    /**
     * Constructeur
     * @param string $name  Nom du composant
     * @param string $componentName Type de composant (ex. InputText)
     */
    protected function __construct($name, $componentName) {
	parent::__construct($name, $componentName);
	$this->label = $name;
    }
    
    
    /**
     * Définit l'erreur affichée
     * @param string $error   Texte de l'erreur
     */
    public function setError($error){
	$this->error = $error;
    }
    
    
    /**
     * Retourne l'erreur affichée
     * @return string
     */
    public function getError(){
	return $this->error;
    }
    
    
    /**
     * Définit la valeur actuelle
     * @param string $value Valeur actuelle
     */
    public function setValue($value){
	$this->value = $value;
    }
    
    
    /**
     * Retourne la valeur actuelle
     * @return string
     */
    public function getValue(){
	return $this->value;
    }
    
    
     /**
     * Définit la valeur par défaut
     * @param string $value Valeur actuelle
     */
    public function setDefaultValue($value){
	$this->defaultvalue = $value;
    }
    
    
    /**
     * Retourne la valeur  par défaut
     * @return string
     */
    public function getDefaultValue(){
	return $this->defaultvalue;
    }
    
    
    /**
     * Définit la valeur du label
     * @param string $label Valeur du label
     */
    public function setLabel($label){
	$this->label = $label;
    }
    
    
    /**
     * Retourne la valeur du label
     * @return string
     */
    public function getLabel(){
	return $this->label;
    }
    
    
    /**
     * Rendu par défaut du composant de type FORM (prise en compte de %label% %value% %error% %style%)
     * @return	string
     */
    protected function render(){
	$html = parent::render();
	$html = StringTools::replaceAll($html, '%label%', $this->getLabel());
	$html = StringTools::replaceAll($html, '%value%', $this->getValue());
	$html = StringTools::replaceAll($html, '%defaultvalue%', $this->getDefaultValue());
	$html = StringTools::replaceAll($html, '%error%', $this->getError());
	
	
	return $html;
    }
}