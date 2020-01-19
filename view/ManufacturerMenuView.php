<?php

require_once ('../autoload.php');

class ManufacturerMenuView extends View {

	//! Obj:CatalogueModel : The catalogue to show the menu for
	var $mCatalogue;
	//! Int : An identifier used in generating the Javascript tree (Incs for each node)
	var $mIdentifier;
	//!  Obj:ValidationHelper : Validator helper used to make sure the data is properly sanitised for javascript etc.
	var $mValidator;
	//! String : Path to the admin directory
	var $mAdminPath;

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
	function LoadMainTree() {
		$this->mPage .= <<<EOT
<div id="productMenuProductTree">
<script type="text/javascript">
d = new dTree('d');
d.config.useCookies = true;
d.add(0,-1,'{$this->mCatalogue->GetDisplayName()}');
EOT;
		$manufacturerController = new ManufacturerController ( );
		$allManufacturers = $manufacturerController->GetAllManufacturersFor ( $this->mCatalogue, true );
		$i = 1;
		foreach ( $allManufacturers as $manufacturer ) {
			$this->mPage .= <<<EOT
				d.add('{$i}',0,'{$manufacturer->GetDisplayName()}','{$this->mAdminPath}/editArea.php?what=manufacturer&currentCatalogue={$this->mCatalogue->GetCatalogueId()}&id={$manufacturer->GetManufacturerId()}&name=s','','editAreaContainer');
EOT;
			$i ++;
		}
	}

	//! Closes off the tree
	function CloseTree() {
		$this->mPage .= '
			document.write(d);
			</script>
			</div>';
	}

	//! Loads the tree menu, taking into account any catalogue settings
	function LoadMenu() {
		$registry = Registry::getInstance ();
		$this->mValidator = new ValidationHelper ( );
		$this->mAdminPath = $registry->adminDir;
		$this->LoadMainTree ();
		$this->CloseTree ();
	} // End function


} // End ProductMenuView Class


// Loads the chosen catalogue, loads the first one in the database if none is supplied
if (! isset ( $_GET ['catalogue'] )) {
	$registry = Registry::getInstance ();
	$catalogue = $registry->catalogue;
} else {
	$currentCatalogue = $_GET ['catalogue'];
	$catalogue = new CatalogueModel ( $currentCatalogue );
}

$page = new ManufacturerMenuView ( );
$page->IncludeCss ( 'wombat7/dtree/dtree.css', false );
$page->IncludeJavascript ( 'wombat7/dtree/dtree.js', false );
echo $page->LoadDefault ( $catalogue );

?>



