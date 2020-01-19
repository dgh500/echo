<?php

class Controller {
	
	var $mRegistry;
	var $mDatabase;
	
	function __construct() {
		$this->mRegistry = Registry::getInstance();
		$this->mDatabase = $this->mRegistry->database;
	}
		
}

?>