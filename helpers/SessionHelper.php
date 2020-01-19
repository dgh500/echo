<?php

class SessionHelper {

	//! The current session ID
	var $mSessionId;
	//! Used to control $mBasket
	var $mBasketController;
	//! The current basket for a user
	var $mBasket;
	//! Obj:ProductModel - the most recently viewed product
	var $mRecentlyViewedProduct;

	function __construct() {
	#	var_dump($_SESSION);die();
	#	$_SESSION = array();

		// Only start a session
	#	if(!isset($_SESSION)) {
	#		session_start();
	#	}
		if(isset($_GET['s'])) {
			session_id($_GET['s']);
			@session_start();
		}
		if(session_id() == '') {
#			session_save_path('/home/echosupplements/htdocs/sesh');
			session_start();
		}/* else {
			die(session_id());
		}*/
		#	die(var_dump($_SESSION));
		#die('test');
		$this->mSessionId = session_id ();
		#die($this->mSessionId);
		if(!isset($this->mBasket)) {
			$this->MakeNewBasket ();
		}
		#die('End Session Helper Construct');
		#echo '<div style="background-color: #fff; color: #000;">'.$this->mSessionId.'</div>';
	}

	function GetSessionId() {
		return $this->mSessionId;
	}

	function Reset() {
		if (isset($_COOKIE[session_name()])) {
			@setcookie(session_name(),'',time()-42000,'/');
		}
		@session_regenerate_id();
		session_destroy();
		session_unset();
		@session_start();
	}

	function GetSavedOrderId() {
		if(isset($_SESSION['Saved_Order_ID'])) {
			return $_SESSION['Saved_Order_ID'];
		 } else {
			return false;
		 }
	}

	function SetSavedOrderId($orderId) {
		$_SESSION['Saved_Order_ID'] = $orderId;
	}

	function RegenerateId() {
		$_SESSION['Saved_Order_ID'] = $this->mBasket->GetOrder()->GetOrderId();
		session_regenerate_id();
		unset($_SESSION['checkoutStatus']);
		unset($_SESSION ['checkoutStage']);
		$this->mSessionId = session_id();
		$this->MakeNewBasket();
	}

	function MakeNewBasket() {
		#die('Make New Basket Start');
		$this->mBasketController = new BasketController ( );
		#die('Basket Controller Initiated');
		$this->mBasket = $this->mBasketController->CreateBasket ( $this->mSessionId );
		#die('Basket Created.');
	}

	function GetBasket() {
		return $this->mBasket;
	}

	function SetCountry($countryId) {
		$_SESSION ['countryId'] = $countryId;
	}

	function GetCountry() {
		if (isset ( $_SESSION ['countryId'] )) {
			return $_SESSION ['countryId'];
		} else {
			return false;
		}
	}

	function SetCustomer($customer) {
		$_SESSION ['customer'] = $customer;
	}

	function GetRegistrationError() {
		if (isset ( $_SESSION ['registrationError'] )) {
			return $_SESSION ['registrationError'];
		} else {
			return false;
		}
	}

	function SetRegistrationError($error) {
		$_SESSION ['registrationError'] = $error;
	}

	function GetPostage() {
		if (isset ( $_SESSION ['basketPostage'] )) {
			return $_SESSION ['basketPostage'];
		} else {
			return false;
		}
	}

	function SetPostage($postage) {
		$_SESSION ['basketPostage'] = $postage;
	}

	function SetCheckoutStatus($status) {
		$_SESSION ['checkoutStatus'] = $status;
	}

	function GetCheckoutStatus() {
		if (isset ( $_SESSION ['checkoutStatus'] )) {
			return $_SESSION ['checkoutStatus'];
		} else {
			return false;
		}
	}

	function SetErrorMsg($orderId) {
		$_SESSION ['errorMsg'] = $orderId;
	}

	function GetErrorMsg() {
		if (isset ( $_SESSION ['errorMsg'] )) {
			return $_SESSION ['errorMsg'];
		} else {
			return false;
		}
	}

	function SetOrder($orderId) {
		$_SESSION ['order'] = $orderId;
	}

	function GetOrder() {
		if (isset ( $_SESSION ['order'] )) {
			return $_SESSION ['order'];
		} else {
			return false;
		}
	}

	function SetCheckoutStage($stage) {
		$_SESSION ['checkoutStage'] = $stage;
	}

	function UnsetCheckoutStage() {
		unset ( $_SESSION ['checkoutStage'] );
	}

	function GetCheckoutStage() {
		if (isset ( $_SESSION ['checkoutStage'] )) {
			return $_SESSION ['checkoutStage'];
		} else {
			return false;
		}
	}

	function GetLoginStage() {
		if (isset ( $_SESSION ['loginStage'] )) {
			return $_SESSION ['loginStage'];
		} else {
			return false;
		}
	}

	function SetLoginStage($stage) {
		$_SESSION ['loginStage'] = $stage;
	}

	function GetAffiliate() {
		if (isset ( $_SESSION ['affiliate'] )) {
			return $_SESSION ['affiliate'];
		} else {
			return false;
		}
	}

	function SetAffiliate($affiliateId) {
		$_SESSION ['affiliate'] = $affiliateId;
	}

	function GetAffiliateStage() {
		if (isset ( $_SESSION ['affiliateStage'] )) {
			return $_SESSION ['affiliateStage'];
		} else {
			return false;
		}
	}

	function SetAffiliateStage($stage) {
		$_SESSION ['affiliateStage'] = $stage;
	}

	function GetAffiliateRegistrationError() {
		if (isset ( $_SESSION ['affRegistrationError'] )) {
			return $_SESSION ['affRegistrationError'];
		} else {
			return false;
		}
	}

	function SetAffiliateRegistrationError($error) {
		$_SESSION ['affRegistrationError'] = $error;
	}

	function SetTrackingId($id) {
		$_SESSION ['trackingId'] = $id;
	}

	function GetTrackingId() {
		if (isset ( $_SESSION ['trackingId'] )) {
			return $_SESSION ['trackingId'];
		} else {
			return false;
		}
	}

	function SetTrackingStatus($status) {
		$_SESSION ['trackingStatus'] = $status;
	}

	function GetTrackingStatus() {
		if (isset ( $_SESSION ['trackingStatus'] )) {
			return $_SESSION ['trackingStatus'];
		} else {
			return false;
		}
	}

	function GetCustomer() {
		if (isset ( $_SESSION ['customer'] )) {
			return $_SESSION ['customer'];
		} else {
			return false;
		}
	}

	function SetPostageMethod($methodId) {
		$_SESSION ['postageMethodId'] = $methodId;
	}

	function GetPostageMethod() {
		if (isset ( $_SESSION ['postageMethodId'] )) {
			return $_SESSION ['postageMethodId'];
		} else {
			return false;
		}
	}

	function GetRecentlyViewed() {
		// When a product page loads, it sets this variable. Doing this (via $this->SetRecentlyViewedProduct()) will set $this->mRecentlyViewedProduct
		if (! isset ( $_SESSION ['recentlyViewedProduct'] ) && ! isset ( $_SESSION ['recentlyViewedPackage'] )) {
			return false;
		} else if ($_SESSION ['recentlyViewedProduct']) {
			try {
				return new ProductModel ( $_SESSION ['recentlyViewedProductId'] );
			} catch ( Exception $e ) {
				// null
			}
		} else if ($_SESSION ['recentlyViewedPackage']) {
			return new PackageModel ( $_SESSION ['recentlyViewedPackageId'] );
		}
	}

	function SetRecentlyViewedProduct($productId, $package = false) {
		if ($package) {
			$_SESSION ['recentlyViewedPackage'] = 1;
			$_SESSION ['recentlyViewedPackageId'] = $productId;
			$_SESSION ['recentlyViewedProduct'] = 0;
		} else {
			$_SESSION ['recentlyViewedProduct'] = 1;
			$_SESSION ['recentlyViewedProductId'] = $productId;
			$_SESSION ['recentlyViewedPackage'] = 0;
		}
	}

}

?>