<?php

//! Defines the view for the settings section of the admin area
class AdminSettingsView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuSettings';
	
	function __construct() {
		parent::__construct('Admin > Settings',false,false,false);
		$this->IncludeCss('AdminSettingsView.css');
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->InitialiseDisplay ();
			$this->InitialiseContentDisplay ();
			$this->LoadSettingsDisplay ();
			$this->CloseContentDisplay ();
			$this->CloseDisplay ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminSettingsViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminSettingsViewContentContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeadView = new AdminHeadView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeadView->LoadDefault ( ' > Settings' );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}
	
	function LoadSettingsDisplay() {
		$systemSettingsController = new SystemSettingsController ( );
		$allSettings = $systemSettingsController->GetAll ();
		foreach ( $allSettings as $systemSetting ) {
			$this->mPage .= $this->LoadFormForCatalogue ( $systemSetting );
		}
	}
	
	function LoadFormForCatalogue($systemSetting) {
		($systemSetting->GetShowTraining () ? $showTraining = 'checked="checked"' : $showTraining = '');
		($systemSetting->GetShowAdvice () ? $showAdvice = 'checked="checked"' : $showAdvice = '');				
		($systemSetting->GetShowPackages () ? $showPackages = 'checked="checked"' : $showPackages = '');		
		($systemSetting->GetShowBrochure () ? $showBrochure = 'checked="checked"' : $showBrochure = '');
		($systemSetting->GetShowRecentlyViewed () ? $showRecentlyViewed = 'checked="checked"' : $showRecentlyViewed = '');
		($systemSetting->GetShowShoppingBag () ? $showShoppingBag = 'checked="checked"' : $showShoppingBag = '');
		($systemSetting->GetShowFreeDelivery () ? $showFreeDelivery = 'checked="checked"' : $showFreeDelivery = '');
		($systemSetting->GetShowOrderHotline () ? $showOrderHotline = 'checked="checked"' : $showOrderHotline = '');
		($systemSetting->GetShowSecureSite () ? $showSecureSite = 'checked="checked"' : $showSecureSite = '');
		($systemSetting->GetShowDealOfTheWeek () ? $showDealOfTheWeek = 'checked="checked"' : $showDealOfTheWeek = '');
		($systemSetting->GetShowOffersOfTheWeek () ? $showOffersOfTheWeek = 'checked="checked"' : $showOffersOfTheWeek = '');
		($systemSetting->GetShowTopBrands () ? $showTopBrands = 'checked="checked"' : $showTopBrands = '');
		($systemSetting->GetShowPriceMatch () ? $showPriceMatch = 'checked="checked"' : $showPriceMatch = '');
		($systemSetting->GetShowOffersOfTheWeekButton () ? $showOffersOfTheWeek = 'checked="checked"' : $showOffersOfTheWeek = '');
		($systemSetting->GetShowClearance () ? $showClearance = 'checked="checked"' : $showClearance = '');
		($systemSetting->GetShowMultibuy () ? $showMultibuy = 'checked="checked"' : $showMultibuy = '');
		($systemSetting->GetShopByTag () ? $showTags = 'checked="checked"' : $showTags = '');
		($systemSetting->GetShowShopPics () ? $showShop = 'checked="checked"' : $showShop = '');
		
		$this->mPage .= <<<EOT
			<h1>{$systemSetting->GetCatalogue()->GetDisplayName()}</h1>
			<form action="{$this->mBaseDir}/formHandlers/SystemSettingsHandler.php" method="post">
				<input type="hidden" name="catalogueId" id="catalogueId" value="{$systemSetting->GetCatalogue()->GetCatalogueId()}" />
				<input type="checkbox" name="showTraining" id="showTraining" "{$showTraining}" />
					<label for="showTraining">Show Training?</label><br />
				<input type="checkbox" name="showAdvice" id="showAdvice" "{$showAdvice}" />
					<label for="showAdvice">Show Advice?</label><br />										
				<input type="checkbox" name="showPackages" id="showPackages" "{$showPackages}" />
					<label for="showPackages">Show Packages?</label><br />					
				<input type="checkbox" name="showBrochure" id="showBrochure" "{$showBrochure}" />
					<label for="showBrochure">Show Brochure?</label><br />
				<input type="checkbox" name="showRecentlyViewed" id="showRecentlyViewed" "{$showRecentlyViewed}" />
					<label for="showRecentlyViewed">Show Recently Viewed?</label><br />
				<input type="checkbox" name="showShoppingBag" id="showShoppingBag" "{$showShoppingBag}" />
					<label for="showShoppingBag">Show Shopping Bag?</label><br />
				<input type="checkbox" name="showFreeDelivery" id="showFreeDelivery" "{$showFreeDelivery}" />
					<label for="showFreeDelivery">Show Free Delivery?</label><br />
				<input type="checkbox" name="showOrderHotline" id="showOrderHotline" "{$showOrderHotline}" />
					<label for="showOrderHotline">Show Order Hotline?</label><br />
				<input type="checkbox" name="showSecureSite" id="showSecureSite" "{$showSecureSite}" />
					<label for="showSecureSite">Show Secure Site?</label><br />
				<input type="checkbox" name="showDealOfTheWeek" id="showDealOfTheWeek" "{$showDealOfTheWeek}" />
					<label for="showDealOfTheWeek">Show Deal Of The Week?</label><br />
				<input type="checkbox" name="showOffersOfTheWeek" id="showOffersOfTheWeek" "{$showOffersOfTheWeek}" />
					<label for="showOffersOfTheWeek">Show Offers Of The Week?</label><br />
				<input type="checkbox" name="showTopBrands" id="showTopBrands" "{$showTopBrands}" />
					<label for="showTopBrands">Show Top Brands?</label><br />
				<input type="checkbox" name="showPriceMatch" id="showPriceMatch" "{$showPriceMatch}" />
					<label for="showPriceMatch">Show Price Match?</label><br />
				<input type="checkbox" name="showOffersOfTheWeek" id="showOffersOfTheWeek" "{$showOffersOfTheWeek}" />
					<label for="showOffersOfTheWeek">Show Offer of the Week?</label><br />
				<input type="checkbox" name="showClearance" id="showClearance" "{$showClearance}" />
					<label for="showClearance">Show Clearance?</label><br />
				<input type="checkbox" name="showMultibuy" id="showMultibuy" "{$showMultibuy}" />
					<label for="showMultibuy">Show Multibuy?</label><br />
				<input type="checkbox" name="showTags" id="showTags" "{$showTags}" />
					<label for="showTags">Show Shop By Tag?</label><br />
				<input type="checkbox" name="showShop" id="showShop" "{$showShop}" />
					<label for="showShop">Show Shop Pics?</label><br />
					<input type="submit" value="Save Settings" />
			</form>
EOT;
	}

}
$page = new AdminSettingsView ( );
$page->IncludeCss ( 'admin.css.php' );
$page->IncludeCss ( 'adminForms.css.php' );

echo $page->LoadDefault ();

?>