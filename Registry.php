<?php
//! Registry pattern class: http://www.patternsforphp.com/wiki/Registry
/*!
* Uses PHP5 magic methods __set, __get etc.
* Usage - Setting up
* $registry = Registry::getInstance();  | Sets up a new registry
* $registry->database = $database;      | Adds a label called database to a variable $database that will be stored in the registry
* Usage - Retrieving
* $registry = Registry::getInstance();  | Get the instance (assumes one has already been set up otherwise the line below will throw an error)
* $database = $registry->database;      | Retrieve the database variable
*/
class Registry {
	private $store = array ();
	private static $thisInstance = null;
	
	//! Maintains only one instance of the class, using Singleton pattern 
	static public function getInstance() {
		if (self::$thisInstance == null) {
			self::$thisInstance = new Registry ( );
		}
		return self::$thisInstance;
	}
	
	//! Stores a variable in the registry
	public function __set($label, $object) {
		if (! isset ( $this->store [$label] )) {
			$this->store [$label] = $object;
		}
	}
	
	//! Deletes a variable from the registry
	public function __unset($label) {
		if (isset ( $this->store [$label] )) {
			unset ( $this->store [$label] );
		}
	}
	
	//! Removes a variable from the registry (non-destructive)
	public function __get($label) {
		if (isset ( $this->store [$label] )) {
			return $this->store [$label];
		}
		return false;
	}
	
	//! Checks whether a variable is in the registry 
	public function __isset($label) {
		if (isset ( $this->store [$label] )) {
			return true;
		}
		return false;
	}
}

?>