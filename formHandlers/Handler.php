<?php

//! Base handler class
/*	
 *	This loads up some handy directories and helpers, and provides the Validate() and Process() methods that all handlers should implement
 */
class Handler {
	
	//! Loads some helpers and variables for all children to share (NB Must call parent::__construct() if the child constructor over-rides it)
	function __construct() {
		// Always handy
		$this->mRegistry = Registry::getInstance();
		
		// Are we local or live?
		$this->mLocalMode = $this->mRegistry->localMode;
		
		// Load some directories
		$this->mBaseDir 			 = $this->mRegistry->baseDir;
		$this->mRootDir 			 = $this->mRegistry->rootDir;
		$this->mFormHandlersDir 	 = $this->mRegistry->formHandlersDir;
		$this->mViewDir 			 = $this->mRegistry->viewDir;
		$this->mManufacturerImageDir = $this->mRegistry->manufacturerImageDir;
		$this->mSecureBaseDir 		 = $this->mRegistry->secureBaseDir;
		
		// Load some helpers
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mPresentationHelper 	= new PresentationHelper();
		$this->mMoneyHelper 		= new MoneyHelper();
		$this->mTimeHelper 			= new TimeHelper();
	}
	
	//! Really an abstract function; each child is expected to use whatever parameters etc. are appropriate
	function Validate() {
		// Null
	}
	
	//! Really an abstract function; each child is expected to use whatever parameters etc. are appropriate
	function Process() {
		// Null	
	}

} // End Handler()


?>