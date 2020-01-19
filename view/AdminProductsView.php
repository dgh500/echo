<?php

class AdminProductsView extends AdminView {

	var $mPageId = 'adminMenuProducts';

	function __construct() {
		parent::__construct('Admin > Products',false,false,false);
	}

	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			#$this->LoadCatalogueSelection ();
			$this->LoadProductMenu ();
			$this->LoadEditArea ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}

	function InitialisePage() {
		$registry = Registry::getInstance ();
		$adminTabsView = new AdminTabsView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mCatalogue = $registry->catalogue;
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
		$this->mPage .= '<div style="float: left; width: 300px;">';
	}

	function LoadCatalogueSelection() {
		$catalogueSelection = new CatalogueListView ( );
		$this->mPage .= $catalogueSelection->LoadDefault ( $this->mCatalogue );
	}

	function LoadProductMenu() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '
		<div style="width: 295px; margin-top: 8px; text-align: center; border: 0px solid #000;">
			<a href="' . $registry->viewDir . '/ProductMenuView.php?method=byCategory" target="productMenu" style="font-weight: bold; text-decoration: none; color: #000;">BY CATEGORY</a> |
			<a href="' . $registry->viewDir . '/ProductMenuView.php?method=byBrand" target="productMenu" style="font-weight: bold; text-decoration: none; color: #000;">BY BRAND</a>
		</div>';
		$this->mPage .= '<iframe src="' . $registry->viewDir . '/ProductMenuView.php" name="productMenu" id="productMenu"></iframe>';
		$this->mPage .= '</div>';
	}

	function LoadEditArea() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '<div style="float: left;">';
		$this->mPage .= '<iframe src="' . $registry->adminDir . '/editArea.php" name="editAreaContainer" id="editAreaContainer" frameborder="0" border="0"></iframe>';
		$this->mPage .= '</div>';
	}

}

$page = new AdminProductsView ( );
echo $page->LoadDefault ();

?>