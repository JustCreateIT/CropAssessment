<?php

class PropertyBag {

	public function __construct() {
	}

    public function setProperty($name, $value){
        $this->{$name} = $value;
    }
}

