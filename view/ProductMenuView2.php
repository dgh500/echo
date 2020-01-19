<?php

require_once ('../autoload.php');

//! View that generates a tree menu, based on a particular catalogue. It takes into account the settings of the catalogue, such as whether packages are enabled etc.
class ProductMenuView2 extends View {
	
	//! Obj:CatalogueModel : The catalogue to show the menu for
	var $mCatalogue;
	//! Int : An identifier used in generating the Javascript tree (Incs for each node)
	var $mIdentifier;
	//! Int : An identifier used in generating the Javascript tree (Indicates the parent node)
	var $mParentIdentifier;
	//! Int : An identifier used in generating the Javascript tree (Indicates the parent node of products that are in top level categories)
	var $mProductParentIdentifier;
	//!  Obj:ValidationHelper : Validator helper used to make sure the data is properly sanitised for javascript etc.
	var $mValidator;
	//! String : Path to the admin directory
	var $mAdminPath;
	//! Obj:CategoryController : used in manipulating the categories for display
	var $mCategoryController;
	
	//! Loads the default view of the page
	/*!
	 * @param [in] catalogue : The catalogue to build the menu from
	 * @return String - the code for the page
	 */
	function LoadDefault($catalogue) {
		$this->mCatalogue = $catalogue;
		$this->LoadMenu ();
		return $this->mPage;
	}
	
	//! Seeds the identifiers used in generating the Javascript tree - this is used for example because if packages are/aren't enabled, then the seeds must be different to account for the package options
	/*!
	 * @param [in] seed : The numeric seed
	 * @return Void
	 */
	function SeedIdentifiers($seed) {
		$this->mIdentifier = $seed;
		$this->mParentIdentifier = $seed;
		$this->mProductParentIdentifier = $seed;
	}
	
	//! Loads the Root and first "Add Category" nodes
	function InitialiseTree() {
		$this->mPage .= <<<EOT
<div id="productMenuProductTree">
<script type="text/javascript">
d = new dTree('d');
d.config.useCookies = true;
d.add(0,-1,'{$this->mCatalogue->GetDisplayName()}');
d.add(1,0,'Add Category','{$this->mAdminPath}/editArea/addCategory/{$this->mCatalogue->GetCatalogueId()}/0/root','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderAdd3.gif');
EOT;
	}
	
	//! Loads the tree menu, taking into account any catalogue settings	
	function LoadMenu() {
		$registry = Registry::getInstance ();
		$this->mValidator = new ValidationHelper ( );
		$this->mAdminPath = $registry->adminDir;
		$this->mCategoryController = new CategoryController ( );
		$showPackages = $this->mCatalogue->GetPackages (); // Bool, show packages or not?
		

		$this->InitialiseTree ();
		if ($showPackages) {
			$this->LoadPackages ();
		} else {
			// Seed Identifiers
			$this->SeedIdentifiers ( 2 );
		}
		$this->LoadMainTree ();
		$this->CloseTree ();
	} // End function
	

	//! Closes off the tree 
	function CloseTree() {
		$this->mPage .= '
			document.write(d);
			</script>
			</div>
			</div>';
	}
	
	//! Initiates the tree generation for the main (non-package) part of the tree. Loads the top-level categories, which then load their respective sub categories and products
	function LoadMainTree() {
		// Top level categories
		$allCategories = $this->mCategoryController->GetAllTopLevelCategoriesForCatalogue ( $this->mCatalogue );
		foreach ( $allCategories as $category ) {
			$this->LoadTopLevelCategory ( $category );
			$this->mIdentifier ++;
			
			// Only on click
			#$this->LoadSubCategoriesOf($category);
			#$this->LoadProductsForMain($category);
			

			$this->mParentIdentifier = $this->mIdentifier;
		} // End Categories	
	} // End LoadMainTree() function
	

	//! Loads the packages, if this is enabled for the catalogue. Adjusts the seeds accordingly.
	function LoadPackages() {
		$packagesCategory = $this->mCatalogue->GetPackagesCategory ();
		$categoryName = $this->mValidator->MakeJsSafe ( $packagesCategory->GetDisplayName () );
		$categoryName = $this->mValidator->RemoveWhitespace ( $categoryName );
		$categoryDisplayName = $categoryName;
		$categoryName = $this->mValidator->RemoveSlashes ( $categoryName );
		$this->mPage .= <<<EOT
d.add(2,0,'{$categoryDisplayName}','{$this->mAdminPath}/editArea/category/{$this->mCatalogue->GetCatalogueId()}/{$packagesCategory->GetCategoryId()}/{$categoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderRightArrow.gif','{$this->mAdminPath}/dtree/img/folderDownArrow.gif');
d.add(3,2,'Add Category','{$this->mAdminPath}/editArea/addPackageCategory/{$this->mCatalogue->GetCatalogueId()}/2/package','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderAdd3.gif');
EOT;
		#d.add(2,0,'Packages','','','','{$this->mAdminPath}/dtree/img/package.gif','{$this->mAdminPath}/dtree/img/package.gif');
		$this->SeedIdentifiers ( 4 );
		$this->LoadPackageCategories ();
	} // End Function LoadPackages()
	

	//! Loads the packages categories (Eg. "BCD & Reg Packages") and then loads any products within them
	function LoadPackageCategories() {
		$allPackageCategories = $this->mCategoryController->GetAllTopLevelPackageCategoriesForCatalogue ( $this->mCatalogue );
		foreach ( $allPackageCategories as $packageCategory ) {
			$categoryName = $this->mValidator->MakeJsSafe ( $packageCategory->GetDisplayName () );
			$categoryName = $this->mValidator->RemoveWhitespace ( $categoryName );
			$categoryDisplayName = $categoryName;
			$categoryName = $this->mValidator->RemoveSlashes ( $categoryName );
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},2,'{$categoryDisplayName}','{$this->mAdminPath}/editArea/category/{$this->mCatalogue->GetCatalogueId()}/{$packageCategory->GetCategoryId()}/{$categoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderRightArrow.gif','{$this->mAdminPath}/dtree/img/folderDownArrow.gif');
EOT;
			$tempId = $this->mIdentifier;
			$this->mIdentifier ++;
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$tempId},'Add Package','{$this->mAdminPath}/editArea/addPackage/{$this->mCatalogue->GetCatalogueId()}/{$packageCategory->GetCategoryId()}/{$categoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/packageAdd.gif');		
EOT;
			$this->mIdentifier ++;
			$this->LoadPackagesIn ( $packageCategory );
			$this->mParentIdentifier = $this->mIdentifier;
		} // End outer foreach (Package Catgeories)	
	} // End LoadPackageCategories() function
	

	//! Called by LoadPackageCategories, this loads the packages to be found in $packageCategory
	/*!
	 * @param [in] packageCategory : The package category from which to load the packages
	 * @return Void
	 */
	function LoadPackagesIn($packageCategory) {
		$allPackages = $this->mCategoryController->GetAllPackagesIn ( $packageCategory );
		foreach ( $allPackages as $package ) {
			$packageName = $this->mValidator->MakeJsSafe ( $package->GetDisplayName () );
			$packageName = $this->mValidator->RemoveWhitespace ( $packageName );
			$packageDisplayName = $packageName;
			$packageName = $this->mValidator->RemoveSlashes ( $packageName );
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mParentIdentifier},'{$packageName}','{$this->mAdminPath}/editArea/package/{$this->mCatalogue->GetCatalogueId()}/{$package->GetPackageId()}/foo','','editAreaContainer','{$this->mAdminPath}/dtree/img/package.gif');					
EOT;
			$this->mIdentifier ++;
		} // End inner foreach (Packages)	
	} // End LoadPackagesIn() function
	

	//! Loads a single top level category
	/*!
	 * @param [in] category : The category to load
	 * @return Void
	 */
	function LoadTopLevelCategory($category) {
		$categoryName = $this->mValidator->MakeJsSafe ( $category->GetDisplayName () );
		$categoryName = $this->mValidator->RemoveWhitespace ( $categoryName );
		$categoryDisplayName = $categoryName;
		$categoryName = $this->mValidator->RemoveSlashes ( $categoryName );
		$this->mPage .= <<<EOT
d.add({$this->mIdentifier},0,'{$categoryDisplayName}','{$this->mAdminPath}/editArea/category/{$this->mCatalogue->GetCatalogueId()}/{$category->GetCategoryId()}/o','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderRightArrow.gif','{$this->mAdminPath}/dtree/img/folderDownArrow.gif',false,'{$category->GetCategoryId()}');
EOT;
		$this->mIdentifier ++;
		$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mParentIdentifier},'Add Category','{$this->mAdminPath}/editArea/addCategory/{$this->mCatalogue->GetCatalogueId()}/{$category->GetCategoryId()}/{$categoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderAdd3.gif');
EOT;
		$this->mIdentifier ++;
		$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mParentIdentifier},'Add Product','{$this->mAdminPath}/editArea/addProduct/{$this->mCatalogue->GetCatalogueId()}/{$category->GetCategoryId()}/{$categoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/fileAdd2.gif');
EOT;
	} // End LoadTopLevelCategory() function
	

	//! Loads the sub categories of a sub category
	/*!
	 * @param [in] category : The parent category
	 * @return Void
	 */
	function LoadSubCategoriesOf($category) {
		// Subcategories
		$allSubCategories = $this->mCategoryController->GetAllSubCategoriesOf ( $category );
		foreach ( $allSubCategories as $subCategory ) {
			$subCategoryName = $this->mValidator->MakeJsSafe ( $subCategory->GetDisplayName () );
			$subCategoryName = $this->mValidator->RemoveWhitespace ( $subCategoryName );
			$subCategoryDisplayName = $subCategoryName;
			$subCategoryName = $this->mValidator->RemoveSlashes ( $subCategoryName );
			$currentIdentifier = $this->mIdentifier;
			$this->mProductParentIdentifier = $this->mIdentifier;
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mParentIdentifier},'{$subCategoryDisplayName}','{$this->mAdminPath}/editArea/category/{$this->mCatalogue->GetCatalogueId()}/{$subCategory->GetCategoryId()}/a','','editAreaContainer','{$this->mAdminPath}/dtree/img/folderRightArrow.gif','{$this->mAdminPath}/dtree/img/folderDownArrow.gif');
EOT;
			$this->mIdentifier ++;
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$currentIdentifier},'Add Product','{$this->mAdminPath}/editArea/addProduct/{$this->mCatalogue->GetCatalogueId()}/{$subCategory->GetCategoryId()}/{$subCategoryName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/fileAdd2.gif');
EOT;
			$this->mIdentifier ++;
			$this->LoadProductsFor ( $subCategory );
		} // End Subcategories	
	} // End LoadSubCategoriesOf() function
	

	//! Loads all the products in a single sub-category
	/*!
	 * @param [in] category : The category to load products from
	 * @return Void
	 */
	function LoadProductsFor($category) {
		// Products
		$allProducts = $this->mCategoryController->GetAllProductsIn ( $category );
		foreach ( $allProducts as $product ) {
			$productName = $this->mValidator->MakeJsSafe ( $product->GetDisplayName () );
			$productName = $this->mValidator->RemoveWhitespace ( $productName );
			$productDisplayName = $productName;
			$productName = $this->mValidator->MakeLinkSafe ( $productName );
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mProductParentIdentifier},'{$productDisplayName}','{$this->mAdminPath}/editArea/product/{$this->mCatalogue->GetCatalogueId()}/{$product->GetProductId()}/{$productName}','','editAreaContainer','{$this->mAdminPath}/dtree/img/file3.gif');
EOT;
			$this->mIdentifier ++;
		} // End Products
	} // End LoadProductsFor() function
	

	//! Loads all the products in a top level category
	/*!
	 * @param [in] category : The category to load the products from
	 * @return Void
	 */
	function LoadProductsForMain($category) {
		// Products that arent in subcategories
		$allTopLevelProducts = $this->mCategoryController->GetAllProductsIn ( $category );
		foreach ( $allTopLevelProducts as $product ) {
			$productName = $this->mValidator->MakeJsSafe ( $product->GetDisplayName () );
			$productName = $this->mValidator->RemoveWhitespace ( $productName );
			$this->mPage .= <<<EOT
d.add({$this->mIdentifier},{$this->mParentIdentifier},'{$productName}','{$this->mAdminPath}/editArea/product/{$this->mCatalogue->GetCatalogueId()}/{$product->GetProductId()}/foo','','editAreaContainer');
EOT;
			$this->mIdentifier ++;
		} // End Products that arent in subcategories		
	} // End LoadProductsForMain() function


} // End ProductMenuView Class


// Loads the chosen catalogue, loads the first one in the database if none is supplied
if (! isset ( $_GET ['catalogue'] )) {
	$catalogueController = new CatalogueController ( );
	$currentCatalogue = $catalogueController->GetFirstCatalogue ()->GetCatalogueId ();
} else {
	$currentCatalogue = $_GET ['catalogue'];
}
$catalogue = new CatalogueModel ( $currentCatalogue );

$page = new ProductMenuView2 ( );
$page->IncludeCss ( 'admin/dtree/dtree.css', false );
$page->IncludeJavascript ( 'admin/dtree/dtree.js', false );
$page->IncludeJavascript ( 'ResponseHandler.js' );
$page->IncludeJavascript ( 'RequestHandler.js' );
$page->IncludeJavascript ( 'ProductMenu2.js' );
echo '<div id="newTree">x</div>';
echo $page->LoadDefault ( $catalogue );

?>