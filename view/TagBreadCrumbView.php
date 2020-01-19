<?php

class TagBreadCrumbView extends View {
	
	function __construct() {
		parent::__construct ();
	}
	
	function LoadDefault($tag) {
		$systemSettings = new SystemSettingsModel($tag->GetCatalogue());
		$tagDescription = $systemSettings->GetShopByTagDescription();
		$linkTo = $this->mBaseDir.'/tag/'.$this->mValidationHelper->MakeLinkSafe(trim($tag->GetDisplayName())).'/'.$tag->GetTagId();
		return '<h2><a href="'.$this->mBaseDir.'/tags">'.$tagDescription.'</a> > <a href="'.$linkTo.'">'.$tag->GetDisplayName().'</a></h2>';
	} // End LoadDefault


} // End class


?>