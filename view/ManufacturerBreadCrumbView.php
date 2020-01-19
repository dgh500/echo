<?php

class ManufacturerBreadCrumbView extends View {
	
	function __construct() {
		parent::__construct();
	}
	
	function LoadDefault($manufacturer) {
		$linkTo = $this->mBaseDir
					.'/brand/'
					.$this->mValidationHelper->MakeLinkSafe(trim($manufacturer->GetDisplayName()))
					.'/'.$manufacturer->GetManufacturerId();
		return '<h2><a href="'.$this->mBaseDir.'/brands">Shop By Brand</a> > <a href="'.$linkTo.'">'.$manufacturer->GetDisplayName().'</a></h2>';
	} // End LoadDefault


} // End class


?>