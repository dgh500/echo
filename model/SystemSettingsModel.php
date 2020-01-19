<?php

//! Model that deals with what is enabled on a particular catalogue/website
class SystemSettingsModel {
	
	//! Internal database object
	var $mDatabase;
	//! The catalogue in question
	var $mCatalogue;
	//! Whether or not to show the training button on the side of the page
	var $mShowTraining;
	//! Whether or not to show the advice button on the side of the page
	var $mShowAdvice;	
	//! Whether or not to enable  packages
	var $mShowPackages;
	//! Whether or not to show the brochure button on the side of the page
	var $mShowBrochure;
	//! Whether or not to show the secure button on the side of the page
	var $mShowSecureSite;
	//! Whether or not to show the free delivery button on the side of the page	
	var $mShowFreeDelivery;
	//! Whether or not to show the recently viewed button on the side of the page	
	var $mShowRecentlyViewed;
	//! Whether or not to show the shopping bag on the side of the page	
	var $mShowShoppingBag;
	//! Whether or not to show the order hotline button on the side of the page	
	var $mShowOrderHotline;
	//! Whether or not to show the deal of the week module	
	var $mShowDealOfTheWeek;
	//! Whether or not to show the offers of the week module	
	var $mShowOffersOfTheWeek;
	//! Whether or not to show the top brands module	
	var $mShowTopBrands;
	//! Whether or not to show the price match module	
	var $mShowPriceMatch;
	//! Whether or not to show the clearance module	
	var $mShowClearance;
	//! Whether or not to show the offers of the week button
	var $mShowOffersOfTheWeekButton;
	//! Whether or not to enable multibuy module
	var $mShowMultibuy;
	//! Whether or not to enable shop by tag (goal)
	var $mShowShopByTag;
	//! Whether or not to show banner with the shop pics in it
	var $mShowShopPics;
	//! Whether or not to show the feedback image
	var $mShowFeedback;
	//! Whether or not to show the gallery (GalleryView)
	var $mShowGallery;
	
	//! Constructor, initialises the database
	/*!
	 * @param $catalogue : Obj CatalogueModel - The catalogue whose settings you want to look at
	 */
	function __construct($catalogue) {
		$registry = Registry::getInstance ();
		$this->mCatalogue = $catalogue;
		$this->mDatabase = $registry->database;
	}
	
	//! Returns the catalogue whose settings are in question
	/*! 
	 * @return Obj : CatalogueModel 
	 */
	function GetCatalogue() {
		return $this->mCatalogue;
	}
	
	//! Returns whether to show a link to dive training on the index page
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetShowTraining() {
		if (! isset ( $this->mShowTraining )) {
			$sql = 'SELECT ShowTrainingLink FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show training link.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowTraining = $resultObj->ShowTrainingLink;
		}
		return $this->mShowTraining;
	}

	//! Returns whether to show a link to the advice page
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetShowAdvice() {
		if (! isset ( $this->mShowAdvice )) {
			$sql = 'SELECT ShowAdvice FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show training link.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowAdvice = $resultObj->ShowAdvice;
		}
		return $this->mShowAdvice;
	}

	//! Set the advice setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowAdvice($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowAdvice = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update training value for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowAdvice = $newValue;
		return true;
	}

	//! Set the training setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowTraining($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowTrainingLink = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update training value for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowTraining = $newValue;
		return true;
	}

	//! Returns whether to enable packages
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetShowPackages() {
		if (! isset ( $this->mShowPackages )) {
			$sql = 'SELECT ShowPackages FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show packages.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowPackages = $resultObj->ShowPackages;
		}
		return $this->mShowPackages;
	}
	
	//! Set the packages setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowPackages($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowPackages = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update packages for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowPackages = $newValue;
		return true;
	}
	
	function GetShowBrochure() {
		if (! isset ( $this->mShowBrochure )) {
			$sql = 'SELECT ShowBrochure FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show brochure link.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowBrochure = $resultObj->ShowBrochure;
		}
		return $this->mShowBrochure;
	}
	
	//! Set the brochure setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowBrochure($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowBrochure = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update brochure value for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowBrochure = $newValue;
		return true;
	}
	
	function GetShowSecureSite() {
		if (! isset ( $this->mShowSecureSite )) {
			$sql = 'SELECT ShowSecureSite FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show secure site link.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowSecureSite = $resultObj->ShowSecureSite;
		}
		return $this->mShowSecureSite;
	}
	
	//! Set the secure site setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowSecureSite($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowSecureSite = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update secure site for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowSecureSite = $newValue;
		return true;
	}
	
	function GetShowFreeDelivery() {
		if (! isset ( $this->mShowFreeDelivery )) {
			$sql = 'SELECT ShowFreeDelivery FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show free delivery link.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowFreeDelivery = $resultObj->ShowFreeDelivery;
		}
		return $this->mShowFreeDelivery;
	}
	
	//! Set the free delivery setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowFreeDelivery($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowFreeDelivery = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update free delivery information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowFreeDelivery = $newValue;
		return true;
	}
	
	function GetShowRecentlyViewed() {
		if (! isset ( $this->mShowRecentlyViewed )) {
			$sql = 'SELECT ShowRecentlyViewed FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show recently viewed section' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowRecentlyViewed = $resultObj->ShowRecentlyViewed;
		}
		return $this->mShowRecentlyViewed;
	}
	
	//! Set the recently viewed setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowRecentlyViewed($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowRecentlyViewed = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update recently viewed information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowRecentlyViewed = $newValue;
		return true;
	}
	
	function GetShowShoppingBag() {
		if (! isset ( $this->mShowShoppingBag )) {
			$sql = 'SELECT ShowShoppingBag FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show shopping bag section.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowShoppingBag = $resultObj->ShowShoppingBag;
		}
		return $this->mShowShoppingBag;
	}
	
	//! Set the shopping bag setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowShoppingBag($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowShoppingBag = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update shopping bag information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowShoppingBag = $newValue;
		return true;
	}
	
	function GetShowOrderHotline() {
		if (! isset ( $this->mShowOrderHotline )) {
			$sql = 'SELECT ShowOrderHotline FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show order hotline section.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowOrderHotline = $resultObj->ShowOrderHotline;
		}
		return $this->mShowOrderHotline;
	}
	
	//! Set the order hotline setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowOrderHotline($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowOrderHotline = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update order hotline information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowOrderHotline = $newValue;
		return true;
	}
	
	function GetShowDealOfTheWeek() {
		if (! isset ( $this->mShowDealOfTheWeek )) {
			$sql = 'SELECT ShowDealOfTheWeek FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show deal of the week.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowDealOfTheWeek = $resultObj->ShowDealOfTheWeek;
		}
		return $this->mShowDealOfTheWeek;
	}
	
	//! Set the deal of the week setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowDealOfTheWeek($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowDealOfTheWeek = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update deal of the week information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowDealOfTheWeek = $newValue;
		return true;
	}
	
	function GetShowOffersOfTheWeek() {
		if (! isset ( $this->mShowOffersOfTheWeek )) {
			$sql = 'SELECT ShowOffersOfTheWeek FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show deal of the week.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowOffersOfTheWeek = $resultObj->ShowOffersOfTheWeek;
		}
		return $this->mShowOffersOfTheWeek;
	}
	
	//! Set the offers of the week setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowOffersOfTheWeek($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowOffersOfTheWeek = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update offers of the week information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowOffersOfTheWeek = $newValue;
		return true;
	}
	
	function GetShowTopBrands() {
		if (! isset ( $this->mShowTopBrands )) {
			$sql = 'SELECT ShowTopBrands FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show deal of the week.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowTopBrands = $resultObj->ShowTopBrands;
		}
		return $this->mShowTopBrands;
	}
	
	//! Set the top brands setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowTopBrands($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowTopBrands = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update top brands information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowTopBrands = $newValue;
		return true;
	}
	
	function GetShowPriceMatch() {
		if (! isset ( $this->mShowPriceMatch )) {
			$sql = 'SELECT ShowPriceMatch FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show price match.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowPriceMatch = $resultObj->ShowPriceMatch;
		}
		return $this->mShowPriceMatch;
	}
	
	//! Set the price match setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowPriceMatch($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowPriceMatch = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update price match information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowPriceMatch = $newValue;
		return true;
	}
	
	function GetShowOffersOfTheWeekButton() {
		if (! isset ( $this->mShowOffersOfTheWeekButton )) {
			$sql = 'SELECT ShowOffersOfTheWeekButton FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show offers of the week button.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowOffersOfTheWeekButton = $resultObj->ShowOffersOfTheWeekButton;
		}
		return $this->mShowOffersOfTheWeekButton;
	}
	
	//! Set the offers of the week button setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowOffersOfTheWeekButton($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowOffersOfTheWeekButton = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update offers of the week information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowOffersOfTheWeekButton = $newValue;
		return true;
	}
	
	function GetShowClearance() {
		if (! isset ( $this->mShowClearance )) {
			$sql = 'SELECT ShowClearance FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show clearance.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowClearance = $resultObj->ShowClearance;
		}
		return $this->mShowClearance;
	}
	
	//! Set the show clearance setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowClearance($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowClearance = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update clearance information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowClearance = $newValue;
		return true;
	}
	
	//! Gets whether the multibuy is enabled
	/*!
	 * @return Boolean - Whether or not multibuy is enabled for this catalogue
	 */
	function GetShowMultibuy() {
		if (! isset ( $this->mShowMultibuy )) {
			$sql = 'SELECT ShowMultibuy FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show multibuy.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowMultibuy = $resultObj->ShowMultibuy;
		}
		return $this->mShowMultibuy;
	}
	
	//! Set the multibuy setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowMultibuy($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowMultibuy = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update multibuy information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowMultibuy = $newValue;
		return true;
	}

	//! Returns the shop by XXX description
	/*!
	* @return String
	*/
	function GetShopByTagDescription() {
		if (! isset ( $this->mShopByTagDescription )) {
			$sql = 'SELECT ShopByTagDescription FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: shop by tag description.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShopByTagDescription = $resultObj->ShopByTagDescription;
		}
		return $this->mShopByTagDescription;
	}

	//! Gets whether the shop by tag is enabled
	/*!
	 * @return Boolean - Whether or not shop by tag is enabled for this catalogue
	 */
	function GetShopByTag() {
		if (! isset ( $this->mShowShopByTag )) {
			$sql = 'SELECT ShowShopByTag FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show shop by tag.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowShopByTag = $resultObj->ShowShopByTag;
		}
		return $this->mShowShopByTag;
	}
	
	//! Set the shop by tags setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShopByTag($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowShopByTag = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update shop by tag information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowShopByTag = $newValue;
		return true;
	}

	//! Gets whether the shop pictures is enabled
	/*!
	 * @return Boolean - Whether or not shop pics enabled for this catalogue
	 */
	function GetShowShopPics() {
		if (! isset ( $this->mShowShopPics )) {
			$sql = 'SELECT ShowShopPics FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: show shop pics.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowShopPics = $resultObj->ShowShopPics;
		}
		return $this->mShowShopPics;
	}
	
	//! Set the show shop pics setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowShopPics($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowShopPics = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update show shop pics information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowShopPics = $newValue;
		return true;
	}

	//! Gets whether the feedback is enabled
	/*!
	 * @return Boolean - Whether or not shop pics enabled for this catalogue
	 */
	function GetShowFeedback() {
		if (! isset ( $this->mShowFeedback )) {
			$sql = 'SELECT ShowFeedback FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: feedback.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowFeedback = $resultObj->ShowFeedback;
		}
		return $this->mShowFeedback;
	}
	
	//! Set the feedback setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowFeedback($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowFeedback = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update feedback information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowFeedback = $newValue;
		return true;
	}

	//! Gets whether the gallery is enabled
	/*!
	 * @return Boolean - Whether or not gallery enabled for this catalogue
	 */
	function GetShowGallery() {
		if (! isset ( $this->mShowGallery )) {
			$sql = 'SELECT ShowGallery FROM tblSystem_Settings WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ().' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch system setting: gallery.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mShowGallery = $resultObj->ShowGallery;
		}
		return $this->mShowGallery;
	}
	
	//! Set the gallery setting
	/*!
	 * @param $newValue - Boolean 0 or 1
	 * @return True on success
	 */
	function SetShowGallery($newValue) {
		$sql = 'UPDATE tblSystem_Settings SET ShowGallery = \'' . $newValue . '\' WHERE Catalogue_ID = ' . $this->mCatalogue->GetCatalogueId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update gallery information for catalogue: ' . $this->mCatalogue->GetDisplayName () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShowGallery = $newValue;
		return true;
	}


}

?>