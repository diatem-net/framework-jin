<?php
/**
 * Jin Framework
 * Diatem
 */

namespace jin\output\form;

use jin\lang\ArrayTools;
use jin\dataformat\Json;
use jin\lang\StringTools;

/** Gestion souple de formulaires
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check		
 */
class DForm{
    /**
     *
     * @var array   Champs contenus dans le formulaire 
     */
    private $fields = array();
    
    
    /**
     * Ajout d'un champ
     * @param mixed $field   Accepte soit une chaîne de caractères définissant l'identifiant du champ, soit un composant de type FormComponent
     * @param mixed $defaultValue   Valeur par défaut
     * @param string $dataSourceColumn	Si le DForm est lié à une dataSource, précise dans quel champ rechercher les données
     * @param string $validateurs   Structure JSON définissant les validateurs à utiliser. Ex : {"notnull":"","numrange":{"minValue":0,"maxValue":100}}
     * @return boolean	Succès ou echec
     * @throws \Exception
     */
    public function addField($field, $defaultValue = null, $dataSourceColumn = null, $validateurs = null){
	//Pour gérer les composants
	if(is_string($field)){
	    $fieldName = $field;
	}else{
	    $fieldName = $field->getName();
	}
	
	
	//Teste si le champ n'existe pas déjà
	if(ArrayTools::isKeyExists($this->fields, $fieldName)){
	    throw new \Exception('Impossible d\'ajouter le champ'.$fieldName.' : celui ci est déjà défini dans le DForm');
	    return false;
	}
	
	
	//Ajout des validateurs
	$validat = array();
	$jsonV = Json::decode($validateurs);
	if(!is_null($validateurs) && is_null($jsonV)){
	    throw new \Exception('Le format JSon fourni pour les validateurs n\'est pas conforme : '.Json::getLastErrorVerbose());
	    return false;
	}
	foreach($jsonV as $key => $value){
	    $className = 'jin\output\form\validators\\'.StringTools::firstCarToUpperCase($key);
	    $c = new $className($value);
	    $validat[] = $c;
	}
	
	//Finalisation
	$newline = array('defaultValue'=>$defaultValue, 'dataSourceColumn'=>$dataSourceColumn, 'validateurs'=>$validat, 'errors'=>array(), 'value' => '');
	if(!is_string($field)){
	    $newline['component'] = $field;
	}
	$this->fields[$fieldName] = $newline;
	
	
	return true;
    }
    
    
    /**
     * Permet de tester la validité des valeurs du formulaire
     * @return boolean	TRUE si le formulaire est valide, NULL si le formulaire n'est pas soumis
     */
    public function isValid(){
	$valide = true;
	
	//On passe dans les champs
	foreach($this->fields as $fieldName => $v){
	    if(isset($_POST[$fieldName])){
		//On réinitialise les erreurs
		$this->fields[$fieldName]['errors'] = array();
		
		//On détermine la nouvelle valeur
		$this->fields[$fieldName]['value'] = $_POST[$fieldName];
		
		//erreurs de niveau 2
		$errors = array();
		//Erreurs de niveau 1
		$priorErrors = array();
		
		//On passe par tous les validateurs pour checker la valeur
		foreach($v['validateurs'] as $v){
		    $vv = $v->isValid($_POST[$fieldName]);
		    if(!$vv){
			$valide = false;
			if($v->isPrior()){
			    $priorErrors = ArrayTools::merge($priorErrors, $v->getErrors());
			}else{
			    $errors = ArrayTools::merge($errors, $v->getErrors());
			}
		    }
		}
		
		//On prend en considération les erreurs de niveau 1 ou 2 en fonction
		if(ArrayTools::length($priorErrors) > 0){
		    $this->fields[$fieldName]['errors'] = $priorErrors;
		}else{
		    $this->fields[$fieldName]['errors'] = $errors;
		}
	    }else{
		$valide = null;
	    }
	}
	
	return $valide;
    }
    
    
    /**
     * Retourne la valeur actuelle d'un champ
     * @param string $fieldName	Identifiant du champ ou du composant
     * @return string
     */
    public function getFieldValue($fieldName){
	if(isset($_POST[$fieldName])){
	    return $this->fields[$fieldName]['value'];
	}else if(isset($this->fields[$fieldName])){
	    return $this->fields[$fieldName]['defaultValue'];
	}
	return '';
    }
    
    
    /**
     * Retourne l'erreur formattée d'un champ
     * @param string $fieldName	Identifiant du champ ou du composant
     * @param string $globalFormat  Template d'affichage autour des erreurs. %texte% est le mot clé permettant de spécifier où positionner les items générés
     * @param string $itemFormat	Template d'affichage de chaque erreur. %texte% est le mot clé permettant de spécifier ou positionner le texte de l'erreur
     * @return string
     */
    public function getFieldError($fieldName, $globalFormat = '<div class="error">%texte%</div>', $itemFormat = '<span>%texte%</span>'){
	if(isset($this->fields[$fieldName])){
	    if(ArrayTools::length($this->fields[$fieldName]['errors']) > 0){
		$toadd = '';
		foreach($this->fields[$fieldName]['errors'] as $err){
		    $toadd .= StringTools::replaceFirst($itemFormat, '%texte%', $err);
		}
		return StringTools::replaceFirst($globalFormat, '%texte%', $toadd);
	    }
	}
	return '';
    }
    
    
    /**
     * Retourne les erreurs d'un champ sous forme de tableau
     * @param string $fieldName	Identifiant du champ ou du composant
     * @return array
     */
     public function getFieldErrorInArray($fieldName){
	if(isset($this->fields[$fieldName])){
	    return $this->fields[$fieldName]['errors'];
	}
	return array();
    }
    
    
    /**
     * Force la valeur d'un champ
     * @param string $fieldName	Identifiant du champ ou du composant
     * @param mixed $value
     */
    public function forceFieldValue($fieldName, $value){
	$this->fields[$fieldName]['value'] = $value;
    }
    
    
    /**
     * Permet d'effectuer le rendu d'un champ de type component.
     * @param string $fieldName	Identifiant du champ ou du composant
     * @param string $globalFormat  Template d'affichage autour des erreurs. %texte% est le mot clé permettant de spécifier où positionner les items générés
     * @param string $itemFormat    Template d'affichage de chaque erreur. %texte% est le mot clé permettant de spécifier ou positionner le texte de l'erreur
     * @return type
     * @throws \Exception
     */
    public function renderComponentField($fieldName, $globalErrorFormat = '<div class="error">%texte%</div>', $itemErrorFormat = '<span>%texte%</span>'){
	if(!isset($this->fields[$fieldName]['component'])){
	    throw new \Exception('Le champ '.$fieldName.' n\'existe pas ou n\'est pas lié à un composant');
	    return;
	}
	
	$this->fields[$fieldName]['component']->setValue($this->fields[$fieldName]['value']);
	$this->fields[$fieldName]['component']->setError($this->getFieldError($fieldName, $globalErrorFormat, $itemErrorFormat));
	
	return $this->fields[$fieldName]['component']->render();
    }
    
    
    /**
     * Retourne les données issues du formulaire sous forme de tableau
     * @return array
     */
    public function getDataInArray(){
	$data = array();
	foreach($this->fields as $fieldName => $v){
	    $data[$fieldName] = $v['value'];
	}
	return $data;
    }
}