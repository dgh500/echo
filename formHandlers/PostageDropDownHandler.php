<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class PostageDropDownHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Validate($postArr) {
		$this->mClean ['postageMethodDropDownMenu'] = $_POST ['postageMethodDropDownMenu'];
	}
	
	//! Updates the postage of the current basket
	function UpdateBasketPostage() {
		$registry = Registry::getInstance ();
		$postageMethod = new PostageMethodModel ( $this->mClean ['postageMethodDropDownMenu'] );
		if ($this->mBasket->GetDefaultPostageMethod ()->GetPostageMethodId () != $postageMethod->GetPostageMethodId ()) {
			$this->mBasket->SetPostageUpgrade ( $postageMethod->GetUpgradeCost () );
		} else {
			$this->mBasket->SetPostageUpgrade ( 0 );
		}
		$this->mSessionHelper->SetPostageMethod ( $this->mClean ['postageMethodDropDownMenu'] );
		$redirectTo = $registry->baseDir . '/basket.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new PostageDropDownHandler ( );
	$handler->Validate ( $_POST );
	$handler->UpdateBasketPostage ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>