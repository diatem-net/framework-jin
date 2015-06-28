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


    /** Composant Select
    *
    *  @auteur     Loïc Gerard
    *  @version    0.0.1
    *  @check
    */
    class Select extends FormComponent implements ComponentInterface{

    /**
    *
    * @var array
    */
    private $values;

    /**
    * Constructeur
    * @param string $name  Nom du composant
    */
    public function __construct($name) {
        parent::__construct($name, 'select');
    }


    /**
    * Rendu du composant
    * @return string
    */
    public function render(){
    //Rendu option
        $o = new AssetFile($this->componentName.'/option.tpl');
        $o_content = $o->getContent();

        $addContent = '';
        foreach($this->values as $k => $option){
            $ac = $o_content;
            $attributes = '';
            if(count($option['attributes']) > 0) {
                foreach($option['attributes'] as $att_k => $att_v) {
                    $attributes .= ' '.$att_k.'="'.$att_v.'"';
                }
            }
            $ac = StringTools::replaceAll($ac, '%value%', $option['value']);
	    $ac = StringTools::replaceAll($ac, '%attributes%', $attributes);
            $ac = StringTools::replaceAll($ac, '%label%', $k);

            $selected = false;
            if($this->getValue() == $option['value']){
                $selected = true;
            }
            if(is_null($this->getValue() && $this->getDefaultValue() == $option['value'])){
                $selected = true;
            }

            if($selected){
                $ac = StringTools::replaceAll($ac, '%selected%', 'selected="selected"');
            }else{
                $ac = StringTools::replaceAll($ac, '%selected%', '');
            }

            $addContent .= $ac;
        }

        $html = parent::render();
        $html = StringTools::replaceAll($html, '%items%', $addContent);

        return $html;
    }


    /**
    * Ajoute un choix dans la liste
    * @param string $value      Valeur du choix
    * @param string $label      Label affiché
    * @param array  $attributes Attributs supplémentaires pour l'option
    */
    public function addValue($value, $label, $attributes = array()){
        $this->values[$label] = array(
            'value'      => $value,
            'attributes' => $attributes
        );
    }

    /**
    * Définit les valeurs du Select
    * @param array $values Tableau de valeurs (clé/valeur) ex. array('label'=>'val','label','val');
    */
    public function setValues($values){
        // Pour rétrocompatibilité : impossible de passer des attributs dans le tableau
        if(count($values) > 0) {
            foreach($values as $label => $value) {
                $this->addValue($value, $label);
            }
        }
    }


    /**
    * Définit une datasource permettant de définir des valeurs au select
    * @param type $queryResult
    * @param type $colNameForLabel
    * @param type $colNameForValue
    */
    public function setDataSource($queryResult, $colNameForLabel, $colNameForValue){
        foreach($queryResult as $v){
            $this->values[$v[$colNameForLabel]] = array(
		'value' => $v[$colNameForValue],
		'attributes' => array()
	    );
        }
    }
}
