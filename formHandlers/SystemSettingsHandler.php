<?php
session_start ();

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class SystemSettingsHandler {

	var $mClean;

	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$registry = Registry::getInstance ();
		$this->adminDir = $registry->adminDir;
	}

	function Validate($postArr) {
		(isset ( $postArr ['showTraining'] ) ? $this->mClean ['showTraining'] = 1 : $this->mClean ['showTraining'] = 0);
		(isset ( $postArr ['showAdvice'] ) ? $this->mClean ['showAdvice'] = 1 : $this->mClean ['showAdvice'] = 0);
		(isset ( $postArr ['showPackages'] ) ? $this->mClean ['showPackages'] = 1 : $this->mClean ['showPackages'] = 0);
		(isset ( $postArr ['showBrochure'] ) ? $this->mClean ['showBrochure'] = 1 : $this->mClean ['showBrochure'] = 0);
		(isset ( $postArr ['showRecentlyViewed'] ) ? $this->mClean ['showRecentlyViewed'] = 1 : $this->mClean ['showRecentlyViewed'] = 0);
		(isset ( $postArr ['showShoppingBag'] ) ? $this->mClean ['showShoppingBag'] = 1 : $this->mClean ['showShoppingBag'] = 0);
		(isset ( $postArr ['showFreeDelivery'] ) ? $this->mClean ['showFreeDelivery'] = 1 : $this->mClean ['showFreeDelivery'] = 0);
		(isset ( $postArr ['showOrderHotline'] ) ? $this->mClean ['showOrderHotline'] = 1 : $this->mClean ['showOrderHotline'] = 0);
		(isset ( $postArr ['showSecureSite'] ) ? $this->mClean ['showSecureSite'] = 1 : $this->mClean ['showSecureSite'] = 0);
		(isset ( $postArr ['showDealOfTheWeek'] ) ? $this->mClean ['showDealOfTheWeek'] = 1 : $this->mClean ['showDealOfTheWeek'] = 0);
		(isset ( $postArr ['showOffersOfTheWeek'] ) ? $this->mClean ['showOffersOfTheWeek'] = 1 : $this->mClean ['showOffersOfTheWeek'] = 0);
		(isset ( $postArr ['showTopBrands'] ) ? $this->mClean ['showTopBrands'] = 1 : $this->mClean ['showTopBrands'] = 0);
		(isset ( $postArr ['showPriceMatch'] ) ? $this->mClean ['showPriceMatch'] = 1 : $this->mClean ['showPriceMatch'] = 0);
		(isset ( $postArr ['showOffersOfTheWeek'] ) ? $this->mClean ['showOffersOfTheWeek'] = 1 : $this->mClean ['showOffersOfTheWeek'] = 0);
		(isset ( $postArr ['showClearance'] ) ? $this->mClean ['showClearance'] = 1 : $this->mClean ['showClearance'] = 0);
		(isset ( $postArr ['showMultibuy'] ) ? $this->mClean ['showMultibuy'] = 1 : $this->mClean ['showMultibuy'] = 0);
		(isset ( $postArr ['showTags'] ) ? $this->mClean ['showTags'] = 1 : $this->mClean ['showTags'] = 0);
		(isset ( $postArr ['showShop'] ) ? $this->mClean ['showShop'] = 1 : $this->mClean ['showShop'] = 0);
		$this->mClean ['catalogueId'] = $postArr ['catalogueId'];
	}

	function UpdateSettings() {
#		var_dump($this->mClean);die();
		$catalogue = new CatalogueModel ( $this->mClean ['catalogueId'] );
		$systemSetting = new SystemSettingsModel ( $catalogue );
		$systemSetting->SetShowTraining ( $this->mClean ['showTraining'] );
		$systemSetting->SetShowAdvice ( $this->mClean ['showAdvice'] );
		$systemSetting->SetShowPackages ( $this->mClean ['showPackages'] );
		$systemSetting->SetShowBrochure ( $this->mClean ['showBrochure'] );
		$systemSetting->SetShowSecureSite ( $this->mClean ['showSecureSite'] );
		$systemSetting->SetShowFreeDelivery ( $this->mClean ['showFreeDelivery'] );
		$systemSetting->SetShowRecentlyViewed ( $this->mClean ['showRecentlyViewed'] );
		$systemSetting->SetShowShoppingBag ( $this->mClean ['showShoppingBag'] );
		$systemSetting->SetShowOrderHotline ( $this->mClean ['showOrderHotline'] );
		$systemSetting->SetShowDealOfTheWeek ( $this->mClean ['showDealOfTheWeek'] );
		$systemSetting->SetShowOffersOfTheWeek ( $this->mClean ['showOffersOfTheWeek'] );
		$systemSetting->SetShowTopBrands ( $this->mClean ['showTopBrands'] );
		$systemSetting->SetShowPriceMatch ( $this->mClean ['showPriceMatch'] );
		$systemSetting->SetShowOffersOfTheWeekButton ( $this->mClean ['showOffersOfTheWeek'] );
		$systemSetting->SetShowClearance ( $this->mClean ['showClearance'] );
		$systemSetting->SetShowMultibuy( $this->mClean ['showMultibuy'] );
		$systemSetting->SetShopByTag ( $this->mClean ['showTags'] );
		$systemSetting->SetShowShopPics ( $this->mClean ['showShop'] );

		$sendTo = $this->adminDir . '/settings';
		header ( 'Location: ' . $sendTo );
	}

}

try {
	$handler = new SystemSettingsHandler ( );
	$handler->Validate ( $_POST );
	$handler->UpdateSettings ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>