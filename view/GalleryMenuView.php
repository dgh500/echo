<?php
require_once ('../autoload.php');

//! Loads a list of galleries, used by the galleries tab
class GalleryMenuView extends AdminView {
	
	function __construct() {
		parent::__construct();	
		$this->mAdminPath = $this->mRegistry->adminDir;
	}
	
	//! Loads the default view of the page
	/*!
	 * @return String - the code for the page
	 */
	function LoadDefault() {
		$this->LoadMenu();
		return $this->mPage;
	}
	
	//! Loads the menu that contains a list of all catalogues
	function LoadMenu() {
		$galleryController = new GalleryController ( );
		$allGalleries = $galleryController->GetAllGalleries();
		$this->mPage .= <<<EOT
<div id="galleryListContainer">
	<script type="text/javascript">
	d = new dTree('d');
	d.add(0,-1,'Galleries','#');
	d.add(
		  1,
		  0,
		  'Add Gallery',
		  '{$this->mAdminPath}/galleryEditArea/addGallery/0',
		  '',
		  'galleryEditAreaContainer',
		  '{$this->mAdminPath}/dtree/img/fileAdd2.gif',
		  '{$this->mAdminPath}/dtree/img/fileAdd2.gif'
		  );

EOT;
		$identifier = 2;
		foreach ( $allGalleries as $gallery) {
			$this->mPage .= <<<EOT

	d.add(
		  {$identifier},
		  0,
		  '{$gallery->GetDisplayName()}',
		  '{$this->mAdminPath}/catalogueEditArea/editGallery/{$gallery->GetGalleryId()}',
		  '',
		  'galleryEditAreaContainer',
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


$page = new GalleryMenuView;
$page->IncludeCss('admin/dtree/dtree.css',false);
$page->IncludeJavascript('admin/dtree/dtree.js',false);
echo $page->LoadDefault();

?>