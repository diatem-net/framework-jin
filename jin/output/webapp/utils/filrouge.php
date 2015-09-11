<?php

namespace jin\output\webapp\utils;

use jin\output\webapp\utils\FilRougeItem;

class FilRouge{
    private static $items = array();
    
    public static function add($label, $urlCode = null, $addedArgs = array()){
        self::$items[] = new FilRougeItem($label, $urlCode, $addedArgs);
    }
    
    public static function getAllItems(){
        return self::$items;
    }
}
