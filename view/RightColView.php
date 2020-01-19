<?php

//! Loads the right column for a page
class RightColView extends View {
	
	//! Just some initialisation
	/*!
	 * @catalogue Obj:CatalogueModel - The catalogue to load the page for
	 * @sessionHelper Obj:SessionHelper - To manage the basket
	 */
	function __construct($catalogue, $sessionHelper, $secure = false) {
		parent::__construct ();
		$this->mSecure = $secure;
		$this->mCatalogue = $catalogue;
		$this->mSystemSettings = new SystemSettingsModel ( $this->mCatalogue );
		if ($secure) {
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( true );
		} else {
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( );
		}
		$this->mSessionHelper = $sessionHelper;
	}
	
	function LoadDefault() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightNavContainer ();
		($this->mSystemSettings->GetShowOrderHotline () ? $this->AddOrderHotline () : false);
		($this->mSystemSettings->GetShowShoppingBag () ? $this->AddShoppingBag () : false);
		if (! $this->mSecure) {
			($this->mSystemSettings->GetShowRecentlyViewed () ? $this->AddRecentlyViewed () : false);
		}
		($this->mSystemSettings->GetShowBrochure () 			? $this->AddBrochureLink() 			: false);		
		($this->mSystemSettings->GetShowFreeDelivery () 		? $this->AddFreeDeliveryLink() 		: false);
		($this->mSystemSettings->GetShowSecureSite () 			? $this->AddSecureSiteLink() 		: false);
		($this->mSystemSettings->GetShowFeedback () 			? $this->AddFeedback() 				: false);
		($this->mSystemSettings->GetShowPriceMatch () 			? $this->AddPriceMatch() 			: false);
		($this->mSystemSettings->GetShowOffersOfTheWeekButton ()? $this->AddOffersOfTheWeekButton()	: false);
		($this->mSystemSettings->GetShowClearance () 			? $this->AddClearance() 			: false);
		($this->mSystemSettings->GetShowTraining () 			? $this->AddTrainingLink() 			: false);		
		($this->mSystemSettings->GetShowAdvice () 				? $this->AddAdvice() 				: false);
		($this->mSystemSettings->GetShowShopPics () 			? $this->AddShopPics() 				: false);
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightNavContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		return $this->mPage;
	}
	
	function AddFeedback() {
		$this->mPage .= $this->mPublicLayoutHelper->Feedback();
	}
	
	function AddOrderHotline() {
		$this->mPage .= $this->mPublicLayoutHelper->OrderHotline ();
	}
	
	function AddPriceMatch() {
		$this->mPage .= $this->mPublicLayoutHelper->PriceMatch ();
	}
	
	function AddClearance() {
		$this->mPage .= $this->mPublicLayoutHelper->Clearance ();
	}
	
	function AddAdvice() {
		$this->mPage .= $this->mPublicLayoutHelper->Advice ();
	}

	function AddShopPics() {
		$this->mPage .= $this->mPublicLayoutHelper->ShopPics ();
	}
	
	function AddOffersOfTheWeekButton() {
		$this->mPage .= $this->mPublicLayoutHelper->OffersOfTheWeekButton ();
	}
	
	function AddShoppingBag() {
		$shoppingBag = new ShoppingBagView ( );
		$this->mPage .= $shoppingBag->LoadDefault ( $this->mSessionHelper->GetBasket () );
	}
	
	function AddFreeDeliveryLink() {
		$this->mPage .= $this->mPublicLayoutHelper->FreeDelivery ();
	}
	
	function AddSecureSiteLink() {
		$this->mPage .= $this->mPublicLayoutHelper->SecureSite ();
	}
	
	function AddBrochureLink() {
		$this->mPage .= $this->mPublicLayoutHelper->OrderBrochure ();
	}
	
	function AddTrainingLink() {
		$this->mPage .= $this->mPublicLayoutHelper->DiveTraining ();
	}
	
	function AddRecentlyViewed() {
		if ($this->mSessionHelper->GetRecentlyViewed ()) {
			$recentlyViewed = new RecentlyViewedView ( );
			$this->mPage .= $recentlyViewed->LoadDefault ( $this->mSessionHelper->GetRecentlyViewed () );
		}
	}

} // End RightColView


?>