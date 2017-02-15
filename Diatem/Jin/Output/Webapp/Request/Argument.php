<?php

namespace Diatem\Jin\Output\WebApp\Request;

class Argument{
    private $name;
    private $type;
    private $value;

    public function __construct($name, $type, $value) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }

    public function getName(){
        return $this->name;
    }

    public function getType(){
        return $this->type;
    }

    public function getValue(){
        return $this->value;
    }

    public function setValue($value){
        $this->value = $value;
    }
}

