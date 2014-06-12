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
     * Définit ce qui sera affiché dans la balise style du composant
     * @param string $style Déclaration CSS
     */
    public function setStyleCSS($style){
	$this->stylecss = $style;
    }
    
    
    /**
     * Retourne ce qui est affiché dans la balise style du composant
     */
    public function getStyleCSS(){
	return $this->stylecss;
    }
    
    
    /**
     * Rendu par défaut du composant de type FORM (prise en compte de %label% %value% %error% %style%)
     * @return	string
     */
    protected function render(){
	$html = parent::render();
	$html = StringTools::replaceAll($html, '%label%', $this->getLabel());
	$html = StringTools::replaceAll($html, '%value%', $this->getValue());
	$html = StringTools::replaceAll($html, '%error%', $this->getError());
	$html = StringTools::replaceAll($html, '%style%', $this->getStyleCSS());
	
	return $html;
    }
}