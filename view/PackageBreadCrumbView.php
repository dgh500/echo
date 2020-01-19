<?php

//! Given a package, returns a string in a <h2> as a breadcrumb
class PackageBreadCrumbView extends View {

	function __construct() {
		parent::__construct ();
	}

	//! Generic loader
	function LoadDefault($package) {
		// Initialise
		$this->mPackage 	= $package;
		$this->mCatalogue 	= $this->mPackage->GetCatalogue ();
		$this->mTopPackagesCategory = $this->mCatalogue->GetPackagesCategory ();
		$this->mCategory 	= $this->mPackage->GetParentCategory ();

		// Construct the crumbs
		$crumbs = $this->LoadTopLevelBreadCrumbs ();
		$crumbs .= ' > ' . $this->LoadCategoryCrumb ();
		$crumbs .= ' > ' . $this->LoadPackageCrumb ();
		return '<h2>' . $crumbs . '</h2>';
	} // End LoadDefault


	function LoadPackageCrumb() {
		$breadCrumb = '
		<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mTopPackagesCategory->GetDisplayName()).'/'.$this->mTopPackagesCategory->GetCategoryId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'/package/'.$this->mValidationHelper->MakeLinkSafe($this->mPackage->GetDisplayName()).'/'.$this->mPackage->GetPackageId().'">
			' . substr ( $this->mPackage->GetDisplayName (), 0, 25 ) . '
		</a>';
		return $breadCrumb;
	}

	function LoadCategoryCrumb() {
		$breadCrumb = '
		<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mTopPackagesCategory->GetDisplayName()).'/'.$this->mTopPackagesCategory->GetCategoryId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'">
			' . substr ( $this->mCategory->GetDisplayName (), 0, 25 ) . '
		</a>';
		return $breadCrumb;
	}

	function LoadTopLevelBreadCrumbs() {
		$breadCrumb = '
		<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mTopPackagesCategory->GetDisplayName()).'/'.$this->mTopPackagesCategory->GetCategoryId().'">
			' . $this->mTopPackagesCategory->GetDisplayName () . '
		</a>';
		return $breadCrumb;
	}

	function LoadLinkHref($package) {
		// Initialise
		$this->mPackage = $package;
		$this->mCatalogue = $this->mPackage->GetCatalogue ();
		$this->mTopPackagesCategory = $this->mCatalogue->GetPackagesCategory ();
		$this->mCategory = $this->mPackage->GetParentCategory ();

		// Construct
		$href = $this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mTopPackagesCategory->GetDisplayName()).'/'.$this->mTopPackagesCategory->GetCategoryId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'/package/'.$this->mValidationHelper->MakeLinkSafe($this->mPackage->GetDisplayName()).'/'.$this->mPackage->GetPackageId();
		return $href;
	}

} // End class


?>