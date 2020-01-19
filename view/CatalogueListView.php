<?php

//! View that defines the 'drop down' <select> catalogue menu as found in the products tab
class CatalogueListView extends View {
	
	//! Generic load function - does all the work because is such a simple view
	/*!
	 * @return String - The code for the view
	 */
	function LoadDefault($defaultCatalogue = false, $target = 'productMenu') {
		$catalogueController = new CatalogueController ( );
		$allCatalogues = $catalogueController->GetAllCatalogues ();
		if (! $defaultCatalogue) {
			$defaultCatalogue = $catalogueController->GetFirstCatalogue ();
		}
		switch ($target) {
			case 'productMenu' :
				$action = 'ProductMenuView.php';
				break;
			case 'manufacturerMenu' :
				$action = 'ManufacturerMenuView.php';
				break;
			case 'tagMenu':
				$action = 'TagMenuView.php';
				break;
		}
		$this->mPage .= <<<EOT
			<div id="productMenuCatalogueSelection">
				<form action="{$this->mViewDir}/{$action}" target="{$target}" method="get">
					Catalogue: 
					<select name="catalogue">
EOT;
		foreach ( $allCatalogues as $catalogue ) {
			if ($defaultCatalogue->GetCatalogueId () == $catalogue->GetCatalogueID ()) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$this->mPage .= '<option value="' . $catalogue->GetCatalogueId () . '" ' . $selected . '>' . $catalogue->GetDisplayName () . '</option>';
		}
		$this->mPage .= <<<EOT
					</select> 
					<input type="submit" value="Go" />
				</form>
			</div>
EOT;
		return $this->mPage;
	}
}

?>