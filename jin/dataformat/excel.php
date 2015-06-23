<?php

/**
 * Jin Framework
 * Diatem
 */

namespace jin\dataformat;

use jin\query\QueryResult;
use jin\JinCore;
use jin\lang\NumberTools;
use jin\lang\StringTools;

/** Gestion de flux EXCEL
 *
 * 	@auteur	    Loïc Gerard
 * 	@check
 */
class Excel {

    /**
     * 	Données lues/à écrire
     * @var array  
     */
    private $data;

    
    /**
     * Définit si on écrit les en-têtes de colonne ou non.
     * @var boolean
     */
    private $useHeaders = true;
    
    
    /**
     * Alphabet
     * @var array
     */
    private static $alphabet = array('A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z');

    
    /**
     * Constructeur
     */
    public function __construct($excelFilePath = null, $useHeaders = true, $headersInLowCase = true) {
        if($excelFilePath){
            //Include Library
            require_once JinCore::getJinRootPath().JinCore::getRelativeExtLibs() . 'phpexcel/PHPExcel/IOFactory.php';
            
            if (!file_exists($excelFilePath)) {
                throw new \Exception('Fichier '.$excelFilePath.' inexistant !');
            }
            
            $objPHPExcel = \PHPExcel_IOFactory::load($excelFilePath);
            $sheet = $objPHPExcel->getSheet(0);
            
            $highestColumm = $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            
            if(!$useHeaders){
                $this->useHeaders = false;
            }
            
            $row = 0;
            $headers = array();
            $this->data = array();
            foreach($sheet->getRowIterator() AS $rowData){
                //Traitement des en tetes
                if($row == 0 && $useHeaders){

                    $cellIterator = $rowData->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    foreach ($cellIterator as $cell) {
                        if (!is_null($cell)) {
                            $value = $cell->getCalculatedValue();
                            if($headersInLowCase){
                                $headers[] = $cell->getCalculatedValue();
                            }else{
                                $headers[] = StringTools::toLowerCase($cell->getCalculatedValue());
                            } 
                        }
                    }
                }else if(!$useHeaders || $row > 0){
                    $cellIterator = $rowData->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    
                    $ligne = array();
                    $col = 0;
                    foreach ($cellIterator as $cell) {
                        if (!is_null($cell)) {
                            $value = $cell->getCalculatedValue();
                        } else {
                            $value = '';
                        }
                        if($this->useHeaders){
                            if($headersInLowCase){
                                $ligne[StringTools::toLowerCase($headers[$col])] = $value;
                            }else{
                                $ligne[$headers[$col]] = $value;
                            }
                        }else{
                            $ligne[] = $value;
                        }
                        $col++;
                    }
                    $this->data[] = $ligne;
                }
                $row++;
            }
            
        }
    }
    
    
    /**
     * Retourne les données au format Array of Array
     * @return array
     * @throws \Exception
     */
    public function getDatasInArray(){
        if(!$this->data){
            throw new \Exception('Aucune donnée à retourner');
        }
        return $this->data;
    }
    
    
    /**
     * Retourne les données au format QueryResult
     * @return QueryResult
     * @throws \Exception
     */
    public function getDatasInQueryResult(){
        if(!$this->data){
            throw new \Exception('Aucune donnée à retourner');
        }
        
        if($this->useHeaders){
            $outDatas = $this->data;
            $row = 0;
            foreach($outDatas AS $l){
                $col = 0;
                foreach($l AS $k => $v){
                    $outDatas[$row][$col] = $v;
                    $col++;
                }
                $row++;
            }
            return new QueryResult($outDatas);
        }else{
            return new QueryResult($this->data);
        }
    }
    
    
    /**
     * Définit les données à écrire à partir d'un tableau de tableaux associatifs.
     * Ex. array(array('id'=>1,'col'=>'valeur1'),array('id'=>2,'col'=>'valeur2'))
     * @param array $associativeArray
     * @param boolean $useHeaders Affiche les en-tête de colonne ou non
     */
    public function populateWithArray($associativeArray, $useHeaders = true) {
        $this->data = $associativeArray;
        $this->useHeaders = $useHeaders;
    }

    
    /**
     * Définit les données à écrire à partir d'un objet QueryResult
     * @param \jin\query\QueryResult $queryResult
     * @param boolean $useHeaders Affiche les en-tête de colonne ou non
     */
    public function populateWithQueryResult(QueryResult $queryResult, $useHeaders = true) {
        $this->data = $queryResult->getDatasInArray(true, true);
        $this->useHeaders = $useHeaders;
    }

    
    /**
     * Ecrit le flux EXCEL dans un fichier
     * @param string $filePath	Chemin relatif/absolu du fichier
     * @throws \Exception
     * @throws Exception
     */
    public function outputInFile($filePath) {
        if (!$this->data) {
            throw new \Exception('Aucune donnée à exporter.');
        }
        
        $objPHPExcel = $this->getExcelObjectFromData();
       
        //Save
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filePath);
    }
    
    
     /**
     * Ecrit le flux EXCEL dans la sortie navigateur
     * @param string $fileName	Nom du fichier généré
     * @throws \Exception
     */
    public function outputInBrowser($fileName) {
        if (!$this->data) {
            throw new \Exception('Aucune donnée à exporter.');
        }
        
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$fileName.'.xls"');

        $objPHPExcel = $this->getExcelObjectFromData();
        
        // Write file to the browser
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    
    /**
     * Construit un objet PHPExcel à partir des données
     * @return \PHPExcel
     */
    private function getExcelObjectFromData(){
        //Include Library
        require_once JinCore::getJinRootPath().JinCore::getRelativeExtLibs() . 'phpexcel/PHPExcel.php';
        
        //Création objet PHPExcel
        $objPHPExcel = new \PHPExcel();
        
        //Feuille 1
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        
        //Ligne actuellement éditée
        $currentLine = 1;
        
        //On créee les entetes
        if ($this->useHeaders) {
            // Les entêtes sont les clés du tableau associatif
            $entetes = array_keys($this->data[0]);

            $row = 0;
            foreach($entetes AS $e){
                $sheet->setCellValue(self::getAlphaColumnFromIndex($row).$currentLine, $e);
                $row++;
            }
            $currentLine++;
        }
        
        //On écrit les données
        foreach($this->data AS $d){
            $row = 0;
            foreach($d AS $k => $v){
                $sheet->setCellValue(self::getAlphaColumnFromIndex($row).$currentLine, $v);
                $row++;
            }
            $currentLine++;
        }
        
        return $objPHPExcel;
    }
    
    
    /**
     * Retourne le numéro de colonne au format Excel (AA, AD, AB...)
     * @param integer $i    Index de la colonne
     * @return string
     */
    private static function getAlphaColumnFromIndex($i){
        $i++;
        $ci = NumberTools::floor($i/27);
        $string = '';
        $remaining = $i - ($ci * 26);
        if($ci > 0){
            $string = self::$alphabet[$ci-1];
        }
        if($remaining > 0){
            $string = $string.self::$alphabet[$remaining-1];
        }
        
        return $string;
    }
}
