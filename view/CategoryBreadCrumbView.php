<?php

//! Defines the breadcrumb navigation when on a category page
class CategoryBreadCrumbView extends View {

	// Constructor
	function __construct() {
		parent::__construct();
	} // End __construct

	//! Generic load function
	function LoadDefault($category) {
		$this->mCategory = $category;
		// If it has a parent then it isn't top level
		if ($this->mCategory->GetParentCategory ()) {
			$parentLength = strlen ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
			$categoryLength = strlen ( $this->mCategory->GetDisplayName () );
			$totalLength = $parentLength + $categoryLength;
			if ($totalLength > 26) {
				$allowableTopLevel = 26 - $categoryLength;
				$topBreadCrumb = $this->LoadTopBreadCrumb ( $allowableTopLevel );
				$currentCrumb = $this->LoadCurrentCrumb ();
			} else {
				$topBreadCrumb = $this->LoadTopBreadCrumb ();
				$currentCrumb = $this->LoadCurrentCrumb ();
			}
			$crumbs = $topBreadCrumb . ' > ' . $currentCrumb;
		} else {
			// If it IS top level, then load a top level breadcrumb
			$crumbs = $this->LoadTopLevelBreadCrumbs ();
		}
		return '<h2>' . $crumbs . '</h2>';
	} // End LoadDefault

	//! Loads the link for the current category (as opposed to it's parent)
	function LoadCurrentCrumb() {
		// Format BASEDIR/department/PARENT_CATEGORY_NAME/PARENT_CATEGORY_ID/CURRENT_CATEGORY_NAME/CURRENT_CATEGORY_ID
		$str = '<a href="'.$this->mBaseDir . '/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetParentCategory()->GetDisplayName()).'/'.$this->mCategory->GetParentCategory()->GetCategoryId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'">
					'.$this->mCategory->GetDisplayName().'</a>';
		return $str;
	} // End LoadCurrentCrumb

	//! Loads the link for the parent category
	/*!
	 * @param $allowableLength - Int (Optional) - Restrict the length of the display name
	 */
	function LoadTopBreadCrumb($allowableLength = false) {
		// Cut the display name of the parent category down if needed
		if ($allowableLength) {
			$displayName = substr(str_replace('&amp;','&',$this->mCategory->GetParentCategory()->GetDisplayName()),0,$allowableLength);
		} else {
			$displayName = str_replace('&amp;','&',$this->mCategory->GetParentCategory()->GetDisplayName());
		}
		// Format BASEDIR/department/CURRENT_CATEGORY_NAME/CURRENT_CATEGORY_ID
		$str = '<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetParentCategory()->GetDisplayName()).'/'.$this->mCategory->GetParentCategory()->GetCategoryId().'">
					'.$displayName.'
				</a>';
		return $str;
	} // End LoadTopBreadCrumb

	//! Loads a link to a top-level category
	function LoadTopLevelBreadCrumbs() {
		$breadCrumb = '
		<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'">
			' . $this->mCategory->GetDisplayName () . '
		</a>';
		return $breadCrumb;
	} // End LoadTopLevelBreadCrumbs

} // End CategoryBreadCrumbView


?>