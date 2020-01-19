<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class RemovePackageHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Validate($postArr) {
		$this->mClean ['packageToRemove'] = $postArr ['packageToRemove'];
	}
	
	function RemovePackage() {
		$registry = Registry::getInstance ();
		$package = new PackageModel ( $this->mClean ['packageToRemove'] );
		$this->mBasket->RemovePackageFromBasket ( $package );
		$redirectTo = $registry->baseDir . '/basket.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new RemovePackageHandler ( );
	$handler->Validate ( $_POST );
	$handler->RemovePackage ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>