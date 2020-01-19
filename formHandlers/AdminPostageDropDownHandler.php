<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AdminPostageDropDownHandler {
	
	var $mClean;
	
	function __construct($redirectTo=false) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
		$registry = Registry::getInstance ();
		if($redirectTo) {
			$this->mRedirectTo = $redirectTo;
		} else {
			$this->mRedirectTo = $registry->viewDir.'/AddOrderView2.php#basketTab';	
		}	
	}
	
	function Validate($postArr) {
		$this->mClean ['postageMethodDropDownMenu'] = $_POST ['postageMethodDropDownMenu'];
	}
	
	//! Updates the postage of the current basket
	function UpdateBasketPostage() {
		$postageMethod = new PostageMethodModel ( $this->mClean ['postageMethodDropDownMenu'] );
		if ($this->mBasket->GetDefaultPostageMethod ()->GetPostageMethodId () != $postageMethod->GetPostageMethodId ()) {
			$this->mBasket->SetPostageUpgrade ( $postageMethod->GetUpgradeCost () );
		} else {
			$this->mBasket->SetPostageUpgrade ( 0 );
		}
		$this->mSessionHelper->SetPostageMethod ( $this->mClean ['postageMethodDropDownMenu'] );
		header ( 'Location: ' . $this->mRedirectTo );
	}

}

try {
	$handler = new AdminPostageDropDownHandler();
	$handler->Validate ( $_POST );
	$handler->UpdateBasketPostage ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>