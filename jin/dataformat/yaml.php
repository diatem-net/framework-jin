<?php
/**
* Jin Framework
* Diatem
*/
namespace jin\dataformat;

/** Gestion de chaînes JSon
*
* 	@auteur	    Loïc Bine
* 	@check
*/
class Yaml {
  /**
  * 	Données lues/à écrire
  * @var array
  */
  private $data;

  /**
  * Chemin fichier
  *@var string
  */
  private $yamlFilePath;

  /**
  * Chaîne caractère Yaml
  *@var string
  */
  private $charChain;

  /**
  * Tableau
  *@var string
  */
  private $array;

  /**
  * Nombre d'espace d'indentation
  *@var int
  */
  private $indentSpace = 4 ;

  /**
  * Construit un objet YAML
  * @param string chemin du fichier
  * @param string tableau
  * @param string chaîne de caractères
  * @param string nombre d'espaces d'indentation
  * @throws \Exception
  */
  public function __construct($yamlFilePath = null,$array = null,$charChain = null,$indentSpace = 4){
    if($yamlFilePath != null){
      if(!file_exists($yamlFilePath)){
        throw new \Exception('Fichier '.$yamlFilePath.' inexistant !');
      }
      $this->yamlFilePath = $yamlFilePath;
      $this->data = file($this->getPath());
      $this->indentSpace = $indentSpace;
    }else if($array != null){

    }else if($charChain != null){

    }
  }

  private function getData(){
    return $this->data;
  }

  private function setData($data){
    $this->data = $data;
  }

  private function getPath(){
    return $this->yamlFilePath;
  }

  private function getIndentSpaces(){
    return $this->indentSpace;
  }

  /**
  * Récupère le nombre d'espaces indentés
  * @param string ligne à parcourir
  * @return int nombre d'espaces
  */
  private function getIndent($line){
    $count = 0;
    for ($i=0; $i < strlen($line); $i++) {
      if($line[$i] != ' '){
        break;
      }else{
        $count += 1;
      }
    }
    if($count % $this->getIndentSpaces() != 0){
      $count -= $count % $this->getIndentSpaces();
    }
    return $count;
  }

  /**
  *Récupère le premier charactère qui n'est pas un espace
  * @param string ligne à parcourir
  * @return string le caractère
  */
  private static function firstChar($line){
    $var = trim($line);
    return $var[0];
  }

  /**
  * Crée le tableau de tri
  * @return array
  */
  private function createArray(){
    //Récupère tableau des données
    $data = $this->getData();
    $res = array();
    foreach ($data as $value) {
      $line = trim($value);
      if(!empty($line)){
        //Si listes en tirets
        if(strstr($line,'- ')){
          $list = explode($line,'- ');
          if(isset($list[1])){
            array_push($res,array(
              'data' => '— '.$list[1],
              'level' => $this->getIndent($value)-$this->getIndentSpaces(),
              'left' => null,
              'right' => null));
          }else{
            array_push($res,array(
              'data' => '—'.$list[0],
              'level' => $this->getIndent($value)-$this->getIndentSpaces(),
              'left' => null,
              'right' => null));
          }
        }
        //Si liste en Crochet ou accolades
        if(preg_match("/[{[]/",$line)){
          preg_match('/((?<={).*?(?=})|(?<=\[).*?(?=\]))/', $line, $match);
          $list = explode(', ',$match[1]);
          if(preg_match("/(?<=\[).*?(?=\])/",$line)){
            array_push($res,array(
              'data' => '—',
              'level' => $this->getIndent($value)-$this->getIndentSpaces(),
              'left' => null,
              'right' => null));
            foreach ($list as $value) {
                array_push($res,array(
                  'data' => $value,
                  'level' => $this->getIndent($value)+$this->getIndentSpaces(),
                  'left' => null,
                  'right' => null));
            }
            continue;
          }else{
            foreach ($list as $value) {
              array_push($res,array(
                'data' => $value,
                'level' => $this->getIndent($value)+$this->getIndentSpaces(),
                'left' => null,
                'right' => null));
            }
          }
        }else{
      array_push($res,array(
        'data' => $line,
        'level' => $this->getIndent($value),
        'left' => null,
        'right' => null));
      }
      }
      }
      return $res;
    }


    /**
    * Determine qui est le parent de l'élément actuel du tableau et remplis son ['right']
    * @param int clé du tableau
    * @param array_pointer tableau à modifier
    * @param int compteur
    * @return array
    */
    private function fillParent($key,&$array,$i){
      $j=$key;
      $current = $key;

      $array[$current-1]['right'] = $i++;

      //Retrouve le parent
      $startLvl = $array[$current-1]['level']-$this->getIndentSpaces();
      $endLvl = 0;
      if(isset($array[$current])){
        $endLvl = $array[$current]['level'];
      }
      for ($j=$startLvl; $j >= $endLvl ; $j-=$this->getIndentSpaces()) {
        $tmp = array_slice($array,0,$current);
        $tmp = array_filter($tmp,function($element) use($j){
          return ($element['level'] == $j) && ($element['right'] == null);
        });
        if(!empty($tmp)){
          reset($tmp);
          $array[key($tmp)]['right'] = $i++;
        }
      }
      if(isset($array[$current])){
        $array[$current]['left'] = $i++;
      }
      return $i;
    }


    /**
    * Tri le tableau à l'aide d'un algorithme d'ordonnancement Gauche/Droite
    * @param array tableau à remplir
    * @return array
    */
    private function ordonnancedArray($array){
      //Ligne précédente
      $lineprec = -1;
      //Compteur
      $i=0;

      /**
      * Remplis les 'left' et 'right'
      */
      foreach ($array as $key => $value) {
        if($value['level'] > $lineprec){
          //Si on descend d'un niveau
          $array[$key]['left'] = $i;
          $i+=1;
        }elseif($value['level'] == $lineprec){
          //Si on reste au même niveau
          $array[$key-1]['right'] = $i;
          $i+=1;
          $array[$key]['left'] = $i;
          $i+=1;
        }else{
          //Si on remonte d'un niveau
          $i = $this->fillParent($key,$array,$i);
        }
        $lineprec = $value['level'];
      }
      $this->fillParent(count($array),$array,$i);
      return $array;
    }
    /**
    * Crée un tableau indenté de manière récursive à partir d'un tableau ordonnancé
    * @param array tableau ordonnancé
    * @param int niveau actuel
    * @return array
    */
    private function createIndentedArray($array,$lvl){
      $final = array();
      //Tout les éléments ne faisant pas partie du niveau recherché ne sont pas pris en compte
      foreach ($array as $key => $value) {
        if($value['level'] != $lvl){
          continue;
        }
        $lft = $value['left'];
        $rgt = $value['right'];

        //Sépare les clés des valeurs YAML
        $tabLine = explode(': ',$value['data']);
        if($tabLine[0] == "---"){
          $obj['key'] = trim($tabLine[0],' ');
        }else{
          $obj = array('key' => trim($tabLine[0],'-: '));
        }
        if(isset($tabLine[1])){
            $obj['value'] = trim($tabLine[1],'- ');
        }

        if(($rgt - $lft) != 1){
          //Si fils on rappelle la fonction pour le niveau du fils et on ajoute ['children']
          $obj['children'] = $this->createIndentedArray(array_filter($array,function($element) use($lft,$rgt){
            return ( $element['left'] > $lft && $element['right'] < $rgt);
          }),$value['level']+$this->getIndentSpaces());
        }
        $final[] = $obj;
      }
      return $final;
    }

    /**
    * Recherche récursive d'un élément dans un tableau
    * @param array tableau
    * @param mixed element à rechercher
    * @return mixed
    */
    private static function recursiveSearchChildrenFromValue($array,$elem){
      $res = null;
      foreach ($array as $key => $value) {
        if(isset($value['value']) && $value['value'] == $elem){
          $res = $value['children'];
          return $res;
        }
        if(is_array($value)){
          $res = self::recursiveSearchChildrenFromValue($value,$elem);
        }
      }
      return $res;
    }

    /**
    * Recherche récursive d'un élément dans un tableau
    * @param array tableau
    * @param mixed element à rechercher
    * @return mixed
    */
    private static function recursiveSearchKey($array,$elem){
      $res = null;
      foreach ($array as $key => $value) {
        if(isset($value['key']) && $value['key'] == $elem){
          $res = $key;
          return $res;
        }
        if(is_array($value)){
          $res = self::recursiveSearchKey($value,$elem);
        }
      }
      return $res;
    }

    /**
    * Recherche récursive d'un élément dans un tableau
    * @param array tableau
    * @param mixed element à rechercher
    * @return mixed
    */
    private static function recursiveSearchChildrenFromKey($array,$elem){
      $res = null;
      foreach ($array as $key => $value) {
        if(isset($value['key']) && isset($value['children']) && $value['key'] == $elem){
          $res = $value['children'];
          return $res;
        }
        if(is_array($value)){
          $res = self::recursiveSearchChildrenFromKey($value,$elem);
        }
      }
      return $res;
    }

    /**
    * Recherche récursive d'un élément dans un tableau et renvoi sa position
    * @param array tableau
    * @param mixed element à rechercher
    * @return mixed
    */
    private static function recursiveSearchValueBeginningBy($array,$elem){
      $res = null;
      foreach ($array as $key => $value) {

        if(isset($value['value']) && preg_match("/^".$elem."/",$value['value'])){
          $res = $key;
          return $res;
        }
        if(is_array($value)){
          $res = self::recursiveSearchChildrenFromKey($value,$elem);
        }
      }
      return $res;
    }

    /**
    * Recherche récursive d'un élément dans un tableau et renvoi son tableau
    * @param array tableau
    * @param mixed element à rechercher
    * @return mixed
    */
    private static function recursiveSearchArraybyValueBeginningBy($array,$elem){
      $res = null;
      foreach ($array as $key => $value) {

        if(isset($value['value']) && preg_match("/^".$elem."/",$value['value'])){
          $res = $value;
          return $res;
        }
        if(is_array($value)){
          $res = self::recursiveSearchChildrenFromKey($value,$elem);
        }
      }
      return $res;
    }

    /**
    * Renvoie un tableau prenant en compte les caractères pris en compte par le Yaml sauf le - du tableau indenté
    * @param array tableau indenté
    * @return array
    */
    private function handleYamlChars($array){
      $final = array();
      foreach ($array as $key => $value) {
        $fChar = self::firstChar($value['key']);
        if(isset($value['value']) && $value['key'] != '<<'){
          $fChar = self::firstChar($value['value']);
        }else if(isset($value['value']) && $value['key'] == '<<'){
          $fChar = '<<';
        }
        switch ($fChar) {
          case '&':
          $final[$key] = $value;
          $final[$key]['key'] = $value['key'];
          $final[$key]['value'] = $value['value'];
          $final[$key]['children'] = self::handleYamlChars($final[$key]['children']);
          break;
          // Si * on recherche l'& correspondante dans tout le tableau et on en copie les enfants
          case '*':
          $elem = '&'.trim($value['value'],'* ');
          $ref = Yaml::recursiveSearchChildrenFromValue($array,$elem);
          $final[$key]['key'] = $value['key'];
          $final[$key]['value'] = $value['value'];
          $final[$key]['children'] = self::handleYamlChars($ref);
          break;
          // Si commentaire on ignore la ligne
          case '#':
          continue;
          break;
          //Si suppression des retour à la ligne on concatène les enfants du noeud sans '\n'
          case '>':
          $final[$key] = $value;
          $concat = '';
          foreach ($final[$key]['children'] as $value) {
            $concat .= $value['key'].' ';
          }
          $concat = substr($concat,0,strlen($concat)-1);
          $final[$key]['value'] = $concat;
          unset($final[$key]['children']);
          break;
          //Si redéfinition de champ
          case '<<':
          $elem = '&'.trim($value['value'],'* ');
          $ref = Yaml::recursiveSearchChildrenFromValue($array,$elem);
          $final[$key]['key'] = trim($final[$key-1]['key'],':');
          $final[$key]['value'] = '<<'.$value['value'];
          unset($final[$key-1]);
          $final[$key]['children'] = self::handleYamlChars($ref);
          break;
          //Si conservation des retour à la ligne on concatène les enfants du noeud avec '\n'
          case '|':
          $final[$key] = $value;
          $concat = '';
          foreach ($final[$key]['children'] as $value) {
            $concat .= $value['key'].PHP_EOL;
          }
          $concat = substr($concat,0,strlen($concat)-1);
          $final[$key]['value'] = $concat;
          unset($final[$key]['children']);
          break;
          //Si valeur nulle on remplace le ~ par NULL
          case '~':
          $final[$key] = $value;
          $final[$key][$value] = NULL;
          break;
          //Sinon on ajoute la ligne
          default:
            if(!isset($final[$key]['children'])){
              //si pas d'enfants
              $final[$key] = $value;
            }else{
              //Sinon on rappelle la fonction pour les enfants
              $final[$key] = self::handleYamlChars($final[$key]['children']);
            }
            break;

        }
      }
      return $final;
    }

    /**
    * Renvoie une tableau Yaml prenant en compte le prochain - du tableau Yaml
    * @param array tableau Yaml
    * @return array
    */
    private static function handleYamlNextList($array){
        $res = 0;
        $res = Yaml::recursiveSearchKey($array,'—');
        if(isset($array[$res]['children'])){
          if(!isset($array[$res-1]['children'])){
            $array[$res-1]['children'] = array();
          }
          array_push($array[$res-1]['children'],$array[$res]['children']);
          unset($array[$res]);
        $array = array_values($array);
        return $array;
      }else{
        return $array;
      }
    }

    /**
    * Renvoie une tableau Yaml prenant en compte les listes du tableau Yaml
    * @param array tableau Yaml
    * @return array
    */
    private function handleYamlLists($array){
      foreach ($array as $key => $value) {
        $array = Yaml::handleYamlNextList($array);
      }
      return $array;
    }

    /**
    * Renvoie une un élément typé
    * @param string element à typer
    * @return array
    */
    private function typeCheck($elem){
      $types = array('!!boolean' => 'boolean', '!!int' => 'integer','!!float' => 'float','!!str' => 'string');
      foreach ($types as $type => $cast) {
        if(isset($elem) && preg_match("/^".$type."/",$elem)){
          $elem = preg_replace("/^".$type."/","",$elem);
          settype($res,$cast);
          return $elem;
        }
      }
      return $elem;
    }

    /**
    * Recherche récursive d'un élément commençant par une chaîne dans un tableau
    * @param array tableau
    * @param string element à rechercher
    * @return mixed
    */
    private function handleYamlScalarType($array){
      $res = null;
      foreach ($array as $key => $value) {
        if(isset($value['value'])){
          $array[$key]['value'] = $this->typeCheck($value['value']);
        }
        if(isset($value['children']) && is_array($value['children'])){
          $array[$key]['children'] = self::handleYamlScalarType($value['children']);
        }
        if(!isset($value['key'])){
          $array[$key]= self::handleYamlScalarType($value);
        }
      }
      return $array;
    }
    /**
    * Recherche récursive d'un << dans le tableau et modifie la clé
    * @param array tableau
    * @return mixed
    */
    private function handleYamlNextRedifinedKeys($array){
      $keyR = Yaml::recursiveSearchValueBeginningBy($array,'<<');
      $valueR = Yaml::recursiveSearchArraybyValueBeginningBy($array,'<<');
      $i = 0;
      //Parcours des enfants
      if(isset($valueR['children'])){
      foreach ($valueR['children'] as $key => $value) {
        //Si on trouve dans le tableau une clé correspondante au élément suivant le tableau
        if($value['key'] == $array[$keyR+$i]['key']){
           $valueR['children'][$key]['value'] = $array[$keyR+$i]['value'] ;
           unset($array[$keyR+$i]);
        }
        $i++;
      }
      $array[$keyR]['value'] = trim($array[$keyR]['value'],'<<');
      $array[$keyR]['children'] = $valueR['children'];
      $array = array_values($array);
      }
      return $array;
    }

    /**
    * Recherche récursive de tout les << dans le tableau et modifie la clé
    * @param array tableau
    * @return mixed
    */
    private function handleYamlRedifinedKeys($array){
      foreach ($array as $key => $value) {
        $array = Yaml::handleYamlNextRedifinedKeys($array);
      }
      return $array;
    }

    /**
    * Renvoi un tableau des tableaux(documents) Yaml
    * @param array tableau
    * @return mixed
    */
    private function handleYamlDocs($array){
      $res = array();
      foreach ($array as $key => $value) {
        if($value['key'] == '---'){
          $newArr = array();
          $arr = $array;
          array_splice($arr,0,1);
            foreach ($arr as $arrKey => $arrValue) {
              if($arrValue['key'] == '---' || $arrValue['key'] == '...'){

                break;
              }
              array_push($newArr,$arrValue);
            }
            array_push($res,$newArr);
        }
      }
      return $res;
    }

    /**
    * Convertit le Yaml en Tableau
    * @return array
    */
    public function getArray(){
      $tabB = $this->createArray();
      $tab = $this->ordonnancedArray($tabB);
      $tab2 = $this->createIndentedArray($tab,0);
      $tab3 = $this->handleYamlChars($tab2);
      $tab4 = $this->handleYamlLists($tab3);
      $tab5 = $this->handleYamlScalarType($tab4);
      $tab6 = $this->handleYamlRedifinedKeys($tab5);
      $tab7 = $this->handleYamlDocs($tab6);
      return $tab7;
    }

}
