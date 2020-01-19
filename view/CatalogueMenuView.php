<?php
require_once ('../autoload.php');

//! Loads a list of catalogues, used by the catalogue tab
class CatalogueMenuView extends AdminView {

	function __construct() {
		parent::__construct();
		$this->mAdminPath = $this->mRegistry->adminDir;
	}

	//! Loads the default view of the page
	/*!
	 * @return String - the code for the page
	 */
	function LoadDefault() {
		$this->LoadMenu ();
		return $this->mPage;
	}

	//! Loads the menu that contains a list of all catalogues
	function LoadMenu() {
		$catalogueController = new CatalogueController ( );
		$allCatalogues = $catalogueController->GetAllCatalogues ();
		$this->mPage .= <<<EOT
<div id="catalogueListContainer">
	<script type="text/javascript">
	d = new dTree('d');
	d.add(0,-1,'Catalogues','#');
	d.add(
		  1,
		  0,
		  'Add Catalogue',
		  '{$this->mAdminPath}/catalogueEditArea/addCatalogue/0',
		  '',
		  'catalogueEditAreaContainer',
		  '{$this->mAdminPath}/dtree/img/fileAdd2.gif',
		  '{$this->mAdminPath}/dtree/img/fileAdd2.gif'
		  );

EOT;
		$identifier = 2;
		foreach ( $allCatalogues as $catalogue ) {
			$this->mPage .= <<<EOT

	d.add(
		  {$identifier},
		  0,
		  '{$catalogue->GetDisplayName()}',
		  '{$this->mAdminPath}/catalogueEditArea/editCatalogue/{$catalogue->GetCatalogueId()}',
		  '',
		  'catalogueEditAreaContainer',
		  '{$this->mAdminPath}/dtree/img/file3.gif'
		  );

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
$page->IncludeCss ( 'wombat7/dtree/dtree.css', false );
$page->IncludeJavascript ( 'wombat7/dtree/dtree.js', false );
echo $page->LoadDefault ();

?>






