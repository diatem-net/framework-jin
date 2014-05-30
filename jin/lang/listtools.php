<?php

namespace jin\lang;

use jin\lang\StringTools;
use jin\lang\ArrayTools;


class ListTools{
    public static function changeDelims($list, $oldDelimiter, $newDelimiter){
	return StringTools::replaceAll($list, $oldDelimiter, $newDelimiter);
    }
    
    public static function append($list, $value, $delimiter=','){
	if($list == ''){
	    $list = $value;
	}else{
	    $list .= $delimiter.$value;
	}
	return $list;
    }
    
    public static function find($list, $value, $delimiter = ','){
	return ArrayTools::find(self::toArray($list, $delimiter), $value);
    }
    
    public static function findNoCase($list, $value, $delimiter = ','){
	return ArrayTools::findNoCase(self::toArray($list, $delimiter), $value);
    }
    
     public static function contains($list, $value, $delimiter = ','){
	if(ArrayTools::find(self::toArray($list, $delimiter), $value)){
	    return true;
	}
	return false;
    }
    
    public static function containsNoCase($list, $value, $delimiter = ','){
	if(ArrayTools::findNoCase(self::toArray($list, $delimiter), $value)){
	    return true;
	}
	return false;
    }
    
    public static function deleteAt($list, $index, $delimiter = ','){
	return ArrayTools::deleteAt(self::toArray($list, $delimiter), $index);
    }
    
    public static function first($list, $delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	return $arr[0];
    }
    
    public static function last($list, $delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	return $arr[count($arr)-1];
    }
    
    public static function ListGetAt($list, $index, $delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	return $arr[$index];
    }
    
    public static function ListInsertAt($list, $index, $value, $delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	$arr = ArrayTools::insertAt($arr, $index, $value);
	return ArrayTools::toList($arr, $delimiter);
    }

    
    public static function len($delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	return count($arr);
    }
    
    public static function prepend($list, $value, $delimiter = ','){
	$list = $value.$delimiter.$list;
	return $list;
    }
    
    public static function setAt($list, $index, $value, $delimiter = ','){
	$arr = self::toArray($list, $delimiter);
	$arr[$index] = $value;
	return ArrayTools::toList($arr, $delimiter);
    }
    
    public static function toArray($list, $delimiter = ','){
	return StringTools::explode($list, $delimiter);
    }
    
    public static function valueList(){
	
    }
    
    
}