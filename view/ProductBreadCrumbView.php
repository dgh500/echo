<?php
//! The bbreadcrumb navigation on the product page
class ProductBreadCrumbView extends View {

	function __construct() {
		parent::__construct ();
	}

	function LoadDefault($product) {
		$this->mProduct = $product;
		$allCategories = $this->mProduct->GetCategories ();
		$this->mCategory = $allCategories [0];
		if ($this->mCategory->GetParentCategory ()) {
			$parentLength = strlen ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
			$categoryLength = strlen ( $this->mCategory->GetDisplayName () );
			$totalLength = $parentLength + $categoryLength;
			if ($totalLength > 40) {
				$allowableTopLevel = 40 - $categoryLength;
				$topBreadCrumb = $this->LoadTopBreadCrumb ( $allowableTopLevel );
				$currentCrumb = $this->LoadCurrentCrumb ();
			} else {
				$topBreadCrumb = $this->LoadTopBreadCrumb ();
				$currentCrumb = $this->LoadCurrentCrumb ();
			}
			$crumbs = $topBreadCrumb . ' > ' . $currentCrumb;
		} else {
			$crumbs = $this->LoadTopLevelBreadCrumbs ();
		}
		$crumbs .= ' > ' . substr ( $product->GetDisplayName (), 0, 40 );
		return '<h2>' . $crumbs . '</h2>';
	} // End LoadDefault


	function LoadCurrentCrumb() {
		$str = '<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetParentCategory()->GetDisplayName()).'/'.$this->mCategory->GetParentCategory()->GetCategoryId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetDisplayName()).'/'.$this->mCategory->GetCategoryId().'">
		'.$this->mCategory->GetDisplayName().'
		</a>';
		return $str;
	}

	function LoadTopBreadCrumb($allowableLength = false) {
		if ($allowableLength) {
			$str = '<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetParentCategory()->GetDisplayName()).'/'.$this->mCategory->GetParentCategory()->GetCategoryId().'">
					'.substr(str_replace('&amp;','&',$this->mCategory->GetParentCategory()->GetDisplayName()),0,$allowableLength).'
					</a>';
		} else {
			$str = '<a href="'.$this->mBaseDir.'/department/'.$this->mValidationHelper->MakeLinkSafe($this->mCategory->GetParentCategory()->GetDisplayName()).'/'.$this->mCategory->GetParentCategory()->GetCategoryId().'">
						'.str_replace('&amp;','&',$this->mCategory->GetParentCategory()->GetDisplayName()).'
					</a>';
		}
		return $str;
	}

	function LoadTopLevelBreadCrumbs() {
		$breadCrumb = '
		<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/' . $this->mCategory->GetCategoryId () . '">
			' . $this->mCategory->GetDisplayName () . '
		</a>';
		return $breadCrumb;
	}

	function LoadLinkHref($product) {
		$this->mProduct = $product;
		$allCategories = $this->mProduct->GetCategories ();
		$this->mCategory = $allCategories [0];
		$hasParentCategory = $this->mCategory->GetParentCategory ();
		$href = $this->mBaseDir;
		$href .= '/department';
		if ($hasParentCategory) {
			$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
		}
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/';
		$href .= 'product/';
		$href .= $this->mValidationHelper->MakeLinkSafe ( $this->mProduct->GetDisplayName () );
		$href .= '/' . $this->mProduct->GetProductId ();
		return $href;
	}

} // End ProductBreadCrumbView


?>