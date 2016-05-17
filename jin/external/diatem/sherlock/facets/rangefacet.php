<?php

namespace jin\external\diatem\sherlock\facets;

use \Iterator;
use jin\log\Debug;
use jin\external\diatem\sherlock\searchconditions\ConditionOnNumericRange;
use jin\lang\ArrayTools;

class RangeFacet implements Iterator{
    private $fieldName;
    private $facetName;
    private $renderLimit;
    private $ranges = array();
    private $ESData = array();
    private $selectedValue = null;
    private $childFacets = null;

    public function __construct($fieldName, $facetName, $renderLimit = 0) {
        $this->fieldName = $fieldName;
        $this->facetName = $facetName;
        $this->renderLimit = $renderLimit;
    }

    public function getName(){
        return $this->facetName;
    }

    public function getDatasInArray(){
        return (array) $this->ESData;
    }

    public function setChildFacet(RangeFacet $facetObject){
        $this->childFacets = $facetObject;
    }

    public function getArgArrayForAggregate(){
        $outArray = array();
        $outArray[$this->facetName] = array();
        $outArray[$this->facetName]['range'] = array();
        $outArray[$this->facetName]['range']['field'] = $this->fieldName;
        $outArray[$this->facetName]['range']['size'] = $this->renderLimit;
        $outArray[$this->facetName]['range']['ranges'] = $this->ranges;
        return $outArray;
    }

    public function getArgArrayForSearchQuery(){
        if($this->selectedValue && is_array($this->selectedValue)){
            if(is_array($this->selectedValue[0])){
                $fullCond = array(
                    'bool' => array(
                        'should' => array()
                    )
                );
                foreach($this->selectedValue as $value){
                    $condition = new ConditionOnNumericRange(array($this->fieldName), $value);
                    $fullCond['bool']['should'][] = $condition->getParamArray()['bool']['should'][0];
                }
                return $fullCond;
            }else{
                $condition = new ConditionOnNumericRange(array($this->fieldName), $this->selectedValue);
                return $condition->getParamArray();
            }
        }
        return null;
    }

    public function setESReturnData($data){
        if(isset($data['buckets'])){
            $this->ESData = $data['buckets'];
            foreach ($this->ESData as $key => $d){
                $this->ESData[$key]['selected'] = $this->isValueSelected($d['key']);
            }
        }
    }

    public function addRange($from = null, $to = null){
        $range = array();
        if(!is_null($from)) {
            $range['from'] = $from;
        }
        if(!is_null($to)) {
            $range['to'] = $to;
        }
        $this->ranges[] = $range;
    }

    public function isValueSelected($value){
        if(is_array($this->selectedValue)){
            if(is_numeric(ArrayTools::find($this->selectedValue, $value))){
                return true;
            }
        }else{
            if($this->selectedValue == $value){
                return true;
            }
        }
        return false;
    }

    public function setRenderLimit($value){
        $this->renderLimit = $value;
    }

    public function setSelectedValue($value){
        $this->selectedValue = $value;
    }

    public function setSelectedValues($arrayOfValues){
        $this->selectedValue = $arrayOfValues;
    }

    public function length() {
        return iterator_count($this);
    }

    public function setSelectedFirst(){
        usort($this->ESData, function($a, $b) {
            $selectedfirst = intval($b['selected']) - intval($a['selected']);
            if($selectedfirst != 0) {
                return $selectedfirst;
            }
            return $b['doc_count'] - $a['doc_count'];
        });
    }

    //Fonctions d'itération

    /**
     * Itération : current
     * @return mixed
     */
    public function current() {
        return current($this->ESData);
    }


    /**
     * Itération : key
     * @return string
     */
    public function key() {
        return key($this->ESData);
    }


    /**
     * Itération : rewind
     * @return \jin\query\QueryResult
     */
    public function rewind() {
        reset($this->ESData);
        return $this;
    }


    /**
     * Itération : next
     */
    public function next() {
        next($this->ESData);
    }


    /**
     * Itération valid
     * @return boolean
     */
    public function valid() {
        return array_key_exists(key($this->ESData), $this->ESData);
    }
}
