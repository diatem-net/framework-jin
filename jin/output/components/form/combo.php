<?php
/**
 * Jin Framework
 * Diatem
 */
namespace jin\output\components\form;

use jin\output\components\form\FormComponent;
use jin\output\components\ComponentInterface;
use jin\filesystem\AssetFile;
use jin\lang\StringTools;
use jin\log\Debug;
use jin\lang\ArrayTools;

/** Composant Combo (cases à cocher)
 *
 * 	@auteur		Loïc Gerard
 * 	@version	0.0.1
 * 	@check
 */
class Combo extends FormComponent implements ComponentInterface{
    /**
     *
     * @var array   Valeurs des cases. (Tableau associatif de la forme array(array('label'=>'','value'=>''),...) )
     */
    private $values = array();

    /**
     *
     * @var array  Valeurs a présélectionner. (Tableau de la forme array('value',...))
     */  
    private $checkedValues = array();

    /**
     * Constructeur
     * @param string $name  Nom du composant
     */
    public function __construct($name) {
	parent::__construct($name, 'combo');
    }


    /**
     * Rendu du composant
     * @return type
     */
    public function render(){
	//Rendu de chaque ligne
	$ci = new AssetFile($this->componentName.'/comboitem.tpl');
	$ci_content = $ci->getContent();

	$addContent = '';
	foreach($this->values as $v){
	    $ac = $ci_content;
	    $ac = StringTools::replaceAll($ac, '%name%', $this->getName().'[]');
	    $ac = StringTools::replaceAll($ac, '%item_label%', $v['label']);
	    $ac = StringTools::replaceAll($ac, '%item_value%', $v['value']);
        $ac = StringTools::replaceAll($ac, '%uid%', uniqid());
	    $selected = '';

	    $val= $this->getDefaultValue();
	    if(!is_null($this->getValue())){
		$val = $this->getValue();
	    }
	    if(is_array($val)){
		if(is_numeric(ArrayTools::find($val, $v['value']))){
		    $selected = 'checked="checked" ';
		}
	    }else{
		if($val == $v['value']){
		    $selected = 'checked="checked" ';
		}
	    }
            
            if(is_numeric(ArrayTools::find($this->checkedValues, $v['value']))){                
                    $selected = 'checked="checked" ';
            }         
            
            
            
	    $ac = StringTools::replaceAll($ac, '%item_selected%', $selected);
	    $addContent .= $ac;
            
	}
	$addContent = parent::replaceMagicFields($addContent);

	$html = parent::render();

	$html = StringTools::replaceAll($html, '%items%', $addContent);

	return $html;
    }


    /**
     * Ajoute une case à cocher
     * @param string $value Valeur du choix
     * @param string $label [optionel] Label affiché (Par défaut la valeur)
     */
    public function addValue($value, $label = null){
	if(!$label){
	    $label = $value;
	}
	$this->values[] = array('value' => $value, 'label' => $label);
    }
      
    /**
     * Définit les value à mettre à checked
     * @param array $valuesToChecked  Tableau de values array(1,'toto'...)   
     */  
    public function forceCheckedValues($valuesToChecked){
        $this->checkedValues = $valuesToChecked;
    }

    /**
     * Définit l'ensemble des données des cases à cocher
     * @param array $values  Tableau associatif de la forme array(array('label'=>'','value'=>''),...)
     * @throws \Exception
     */
    public function setValues($values){
	$vc = count($values);
	for($i = 0; $i < $vc; $i++){
	    if(!isset($values[$i]['value']) || !isset($values[$i]['label'])){
		throw new \Exception('Tableau de données non correct. Les clés value et label doivent être définis pour chaque ligne du tableau de données');
	    }
	}
	$this->values = $values;
    }
}
