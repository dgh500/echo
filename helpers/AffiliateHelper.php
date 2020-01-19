<?php

//! Helper class for managing commission for affiliates
class AffiliateHelper {
	
	//! The affiliates ID
	var $mAffiliate;
	
	//! If the affiliate ID is set then the cookie is created for it
	function SetAffiliate($affId) {
		$registry = Registry::getInstance ();
		try {
			$this->mAffiliate = new AffiliateModel ( $affId );
			$oneMonth = time () + 60 * 60 * 24 * 30;
			setcookie ( 'aid', $this->mAffiliate->GetAffiliateId (), $oneMonth, '/', $registry->cookieDomain, false, true );
		} catch ( Exception $e ) {
			#echo $e->GetMessage();
		}
	}
	
	//! Returns false on failure, the affiliates ID if it exists
	function GetAffiliate() {
		if(isset($_COOKIE['aid'])) {
			return new AffiliateModel($_COOKIE['aid']);
		} else {
			return false;
		}
	}
}

if (isset ( $_GET ['aid'] )) {
	$affHelper = new AffiliateHelper ( );
	$affHelper->SetAffiliate ( $_GET ['aid'] );
}

if (isset ( $_POST ['aid'] )) {
	$affHelper = new AffiliateHelper ( );
	$affHelper->SetAffiliate ( $_POST ['aid'] );
}

?>