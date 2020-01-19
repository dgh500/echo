<?php
@include_once('../autoload.php');
class BasketMacFinderView extends AdminView {

	//! Obj:CatalogueModel : Catalogue for which to display the interface
	var $mCatalogue;

	function __construct() {
		parent::__construct();
	}

	//! Generic load function
	/*!
	 * @param [in] catalogue : CatalogueModel - The catalogue used to populate the categories
	 * @return String - The code for the view
	 */
	function LoadDefault($catalogue,$secure=false) {
		$this->mCatalogue = $catalogue;
		$this->mSystemSettings = new SystemSettingsModel($this->mCatalogue);
		$this->mSecure = $secure;
		$this->InitialiseDisplay ();
		$this->LoadMiscProductForm();
		$this->LoadPackageOptionsForm();
		$this->LoadProductOptionsForm();
		$this->LoadTopLevelCategories();
		$this->LoadSubCategories();
		$this->LoadProducts();
		$this->CloseDisplay();
		return $this->mPage;
	}

	//! Initialises the mac view container
	function InitialiseDisplay() {
		$this->mPage .= '<div id="macViewContainer" class="macViewContainer">';
	}

	//! Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	//! Loads the top level categories in the correct catalogue
	function LoadTopLevelCategories() {
		$categoryController = new CategoryController ( );
		$this->mPage .= '<div id="topLevelCategoryContainer" class="topLevelCategoryContainer">';
		// ***** Packages
		if ($this->mSystemSettings->GetShowPackages()) {
			$packagesCategory = $this->mCatalogue->GetPackagesCategory();
			$this->mPage .= '<div class="macViewMenuItemPackages" id="packages" name="packages">';
			$this->mPage .= '<a href="#" id="topLevelPackage">'.ucfirst(strtolower($packagesCategory->GetDisplayName())).'</a>';
			$this->mPage .= '</div>';
		}
		// ***** Products
		$allCategories = $categoryController->GetAllTopLevelCategoriesForCatalogue ( $this->mCatalogue );
		foreach ( $allCategories as $category ) {
			$this->mPage .= '<div class="macViewMenuItem">';
			$this->mPage .= '<a href="#" id="'.$category->GetCategoryId().'">'.ucwords(strtolower($category->GetDisplayName())).'</a>';
			$this->mPage .= '</div>';
		}
		$this->mPage .= '</div>';
	}

	function LoadPackageOptionsForm() {
		$this->mPage .= <<<EOT
			<div id="packageOptionsContainer" title="Package Options" style="display: none;">
				<form id="packageOptionsForm">
					<div id="packageOptionsContent">
					</div>
				</form>
			</div>
EOT;
	}

	function LoadProductOptionsForm() {
		$this->mPage .= <<<EOT
			<div id="productOptionsContainer" title="Product Options" style="display: none">
				<form id="productOptionsForm">
					<div id="productOptionsContent">
					</div>
				</form>
			</div>
EOT;
	}

	function LoadMiscProductForm() {
		$this->mPage .= <<<EOT
		<div id="miscProduct" title="Misc Product" style="display: none">
			<form id="miscProductForm">
				<div id="miscProductContainer" name="miscProductContainer" method="get" style="font-family: Arial; margin-top: 10px;">
					<input type="text" id="miscProductName" name="miscProductName" style="width: 450px; font-family: Arial; margin-right: 10px; margin-left: 5px;" />
					&pound; <input type="text" name="miscProductPrice" id="miscProductPrice" style="width: 54px; font-family: Arial;" />
				</div>
				<div id="miscProductError"></div>
			</form>
		</div>
EOT;
	}
	//! Loads the container for the sub categories, assuming it will be filled by the Javascript part of the view
	function LoadSubCategories() {
		$this->mPage .= '<div id="subLevelCategoryContainer" class="subLevelCategoryContainer"></div>';
	}

	//! Loads the container for the products, assuming it will be filled by the Javascript part of the view
	function LoadProducts() {
		$this->mPage .= '<div id="productLevelCategoryContainer" class="productLevelCategoryContainer"></div>';
	}
}

if(isset($_GET['LOADFINDER'])) {
	$catalogue = new CatalogueModel($_GET['catalogueIdentifier']);
	$_SESSION['catalogueId'] = $_GET['catalogueIdentifier'];
	$page = new BasketMacFinderView();
	echo $page->LoadDefault($catalogue);
}

?>