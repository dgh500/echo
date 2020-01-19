<?php

include_once ('autoload.php');

//! View for the add a new catalogue
class CatalogueMenuView extends View {
	
	//! Loads the default view of the page
	/*!
	 * @return String - the code for the page
	 */
	function LoadDefault() {
		$this->LoadMenu ();
		return $this->mPage;
	}
	
	function LoadMenu() {
		$registry = Registry::GetInstance ();
		$adminPath = $registry->adminDir;
		$catalogueController = new CatalogueController ( );
		$allCatalogues = $catalogueController->GetAllCatalogues ();
		$this->mPage .= <<<EOT
		<div id="catalogueListContainer">
			<script type="text/javascript">
				d = new dTree('d');
				d.add(0,-1,'Catalogues','#');
				d.add(1,0,'Add Catalogue','{$adminPath}/catalogueEditArea/addCatalogue/0','','catalogueEditAreaContainer','{$adminPath}/dtree/img/fileAdd.gif','{$adminPath}/dtree/img/fileAdd.gif');
EOT;
		
		$identifier = 2;
		foreach ( $allCatalogues as $catalogue ) {
			$this->mPage .= <<<EOT
				d.add({$identifier},0,'{$catalogue->GetDisplayName()}','{$adminPath}/catalogueEditArea/editCatalogue/{$catalogue->GetCatalogueId()}','','catalogueEditAreaContainer','{$adminPath}/dtree/img/file3.gif');
EOT;
			$identifier ++;
		}
		$this->mPage .= '
				document.write(d);
				</script>
				</div>';
	} // End function


} // End Class


$page = new CatalogueMenuView ( );
$page->IncludeCss ( 'admin/dtree/dtree.css', false );
$page->IncludeJavascript ( 'admin/dtree/dtree.js', false );
echo $page->LoadDefault ();

?>






