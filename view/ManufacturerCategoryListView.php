<?php

class ManufacturerCategoryListView extends View {

	function __construct() {
		// CSS Includes
		#$cssIncludes = array('Manufacturer.css.php','Category.css.php');
		$cssIncludes = array();
		// Construct
		parent::__construct(true,$cssIncludes);
	}

	//! Used by usort as a callback function
	function SortByParent($a, $b) {
		if ($a->GetParentCategory ()) {
			$aName = $a->GetParentCategory ()->GetDisplayName ();
		} else {
			$aName = $a->GetDisplayName ();
		}
		if ($b->GetParentCategory ()) {
			$bName = $b->GetParentCategory ()->GetDisplayName ();
		} else {
			$bName = $b->GetDisplayName ();
		}
		#echo 'aName: '.$aName.' | bName: '.$bName.' | strcmp: '.strcmp($aName,$bName).'<br />';
		return strcmp ( $aName, $bName );
	}

	//! Generic load function
	function LoadDefault($manufacturer) {
		$this->mManufacturer = $manufacturer;
		$this->mManufacturerController = new ManufacturerController ( );
		$categories = $this->mManufacturerController->GetAllCategoriesIn ( $this->mManufacturer );
		usort ( $categories, array ($this, "SortByParent" ) );
		if (count ( $categories ) > 0) {
			$this->mPage .= '<div id="categoryListContainer">';
			$this->mPage .= '<ul id="categoryList">';
			foreach ( $categories as $category ) {
				$linkTo = $this->mBaseDir.'/';
				$linkTo .= 'brand/'.$this->mValidationHelper->MakeLinkSafe(trim($this->mManufacturer->GetDisplayName())).'/'.$this->mManufacturer->GetManufacturerId();
				$linkTo .= '/department/'.$this->mValidationHelper->MakeLinkSafe($category->GetDisplayName()).'/'.$category->GetCategoryId();
				if ($category->GetParentCategory ()) {
					$name = $category->GetParentCategory()->GetDisplayName();
				} else {
					$name = $category->GetDisplayName();
				}
				// this is fucking dumb - if its >13 chars just display the subcat name???
				$this->mPage .= '
						<li>
							<a href="' . $linkTo . '">
							' . $name . '
							</a>
						</li>';
			}
			$this->mPage .= '</ul>';
			$this->mPage .= '</div>';
		}
		return $this->mPage;
	} // End LoadDefault


} // End class

?>