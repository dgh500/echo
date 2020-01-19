<?php
require_once ('../autoload.php');

//! View for the admin view of a catalogue
class AdminCatalogueEditView extends AdminView {
	
	//! Obj:CatalogueModel : The catalogue that is being edited by the administrator
	var $mCatalogue;
	
	function __construct() {
		$jsIncludes = array('Tabs.js','AdminCatalogueEditView.js','InputListView.js');
		$cssIncludes = array('AdminCatalogueEditView.css.php');
		parent::__construct(true,$cssIncludes,$jsIncludes);
	}
	
	//! Standard load function - call this on first load. Initialises and loads everything
	function LoadDefault($catalogueId) {
		$this->mCatalogue = new CatalogueModel ( $catalogueId );
		$this->mSystemSettings = new SystemSettingsModel($this->mCatalogue);
		$this->LoadTabs ();
		$this->LoadForm ();
		return $this->mPage;
	}
	
	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
<div id="adminCatalogueViewTabContainer">
	<ul>
		<!-- These styles are hard-coded to assume precedance over the style sheet so that 'details' is by default the focused tab -->
		<li id="adminCatalogueViewTabContainer-details"><a href="#" id="detailsLink">Details</a></li>
		<li id="adminCatalogueViewTabContainer-manufacturers"><a href="#" id="manufacturersLink">Manufacturers</a></li>
		<li id="adminCatalogueViewTabContainer-tags"><a href="#" id="tagsLink">Tags</a></li>				
		<li id="adminCatalogueViewTabContainer-estimates"><a href="#" id="estimatesLink">Estimates</a></li>
	</ul>
</div>	
EOT;
	}
	
	//! Loads the form that allows editing of a catalogue. Contains an iframe with ImageUploadHandler to upload new images for this catalogue
	// \todo{At the minute the user needs to do a hard refresh to change the image because the image has the same name - how to change this?}
	function LoadForm() {
		$this->mPage .= <<<EOT
			<form name="catalogueEditForm" id="catalogueEditForm" action="{$this->mRegistry->formHandlersDir}/CatalogueEditHandler.php" method="post">
			<div id="catalogueEditFormContainer">
EOT;
		$this->LoadDetailsSection ();
		$this->LoadManufacturersSection ();
		$this->LoadTagsSection();
		$this->LoadEstimatesSection ();
		$this->mPage .= <<<EOT
				<div id="catalogueEditFormButtons">
									<input type="submit" name="catalogueEditSave" id="catalogueEditSave" value="Save" />
								<!-- 	<input type="submit" value="Delete" name="catalogueEditDelete" id="catalogueEditDelete" /> -->
				</div>			
			</form>
			</div>
EOT;
	}
	
	function LoadDetailsSection() {		
		$oFCKeditor = new FCKeditor ( 'longDescription' );
		$oFCKeditor->BasePath = $this->mRegistry->adminDir . '/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = $this->mCatalogue->GetLongDescription ();
		$oFCKeditor->Height = 280;
		
		$image = $this->mCatalogue->GetImage();
		if (NULL === $image) {
			$filename = 'noImage.gif';
		} else {
			$filename = $image->GetFilename ();
		}
		if ($this->mSystemSettings->GetShowPackages ()) {
			$packages = 'checked';
		} else {
			$packages = '';
		}
		
		$this->mPage .= <<<EOT
				<div id="catalogueDetailsContentArea">
					<input type="hidden" name="catalogueId" id="catalogueId" value="{$this->mCatalogue->GetCatalogueId()}" />
					<label for="displayName">		Display Name:		</label>
						<input type="text" name="displayName" id="displayName" value="{$this->mCatalogue->GetDisplayName()}" /><br />
					<label for="longDescription">	Long Description:	</label><br /><br />
EOT;
		$this->mPage .= $oFCKeditor->Create ();
		$this->mPage .= <<<EOT
				</div>
EOT;
	}
	
	//! Loads manufacturers section of the form
	function LoadManufacturersSection() {
		$this->mPage .= <<<EOT
				<div id="catalogueManufacturersContentArea">
				<strong>Add Manufacturer</strong><br />
				<iframe src="{$this->mRegistry->formHandlersDir}/AddManufacturerHandler.php?catalogueId={$this->mCatalogue->GetCatalogueId()}" 
						id="addManufacturerIframe" 
						name="addManufacturerIframe" 
						scrolling="no" 
						frameborder="0" 
						border="0" />
						</iframe><br />
				<strong>Manufacturers</strong><br /><br />
EOT;
		$allManufacturers = $this->mCatalogue->GetManufacturers ();
		foreach ( $allManufacturers as $manufacturer ) {
			$manufacturerName = trim ( $manufacturer->GetDisplayName () );
			$this->mPage .= <<<EOT
				<input 	type="text" 
						name="MANUFACTURER{$manufacturer->GetManufacturerId()}" 
						id="MANUFACTURER{$manufacturer->GetManufacturerId()}" 
						value="{$manufacturerName}" 
						disabled />
						&nbsp;
				<a href="#" onClick="toggleTextInputEditable('{$manufacturer->GetManufacturerId()}','MANUFACTURER','catalogueEditForm')" id="MANUFACTURER{$manufacturer->GetManufacturerId()}Edit">Edit</a> 
				| 
				<a href="#" onClick="toggleDeleteField('{$manufacturer->GetManufacturerId()}','MANUFACTURER','catalogueEditForm');" id="MANUFACTURER{$manufacturer->GetManufacturerId()}Delete">Delete</a><br />
EOT;
		}
		$this->mPage .= <<<EOT
				</div>
EOT;
	}
	
	function LoadTagsSection() {
		$this->mPage .= <<<EOT
				<div id="catalogueTagsContentArea">
				<strong>Add Tag</strong><br />
				<iframe src="{$this->mRegistry->formHandlersDir}/AddTagHandler.php?catalogueId={$this->mCatalogue->GetCatalogueId()}" 
						id="addTagsIframe" 
						name="addTagsIframe" 
						scrolling="no" 
						frameborder="0" 
						border="0" />
						</iframe><br />
				<strong>Tags</strong><br /><br />
EOT;
		$allTags = $this->mCatalogue->GetTags();
		foreach ( $allTags as $tag ) {
			$tagName = trim ( $tag->GetDisplayName () );
			$this->mPage .= <<<EOT
				<input 	type="text" 
						name="TAG{$tag->GetTagId()}" 
						id="TAG{$tag->GetTagId()}" 
						value="{$tagName}" 
						disabled />
						&nbsp;
				<a href="#" onClick="toggleTextInputEditable('{$tag->GetTagId()}','TAG','catalogueEditForm')" id="TAG{$tag->GetTagId()}Edit">Edit</a> 
				| 
				<a href="#" onClick="toggleDeleteField('{$tag->GetTagId()}','TAG','catalogueEditForm');" id="TAG{$tag->GetTagId()}Delete">Delete</a><br />
EOT;
		}
		$this->mPage .= <<<EOT
				</div>
EOT;
	}

	function LoadEstimatesSection() {
		$registry = Registry::getInstance ();
		$this->mPage .= <<<EOT
				<div id="catalogueEstimatesContentArea">
				<strong>Add Dispatch Estimate</strong><br />
				<iframe src="{$registry->formHandlersDir}/AddEstimatesHandler.php?catalogueId={$this->mCatalogue->GetCatalogueId()}" 
						id="addEstimatesIframe" 
						name="addEstimatesIframe" 
						scrolling="no" 
						frameborder="0" 
						border="0" />
						</iframe><br />
				<strong>Estimates</strong><br /><br />
EOT;
		$dispatchDateController = new DispatchDateController ( );
		$allEstimates = $dispatchDateController->GetAllDispatchDates ();
		foreach ( $allEstimates as $dispatchDate ) {
			$estimateName = trim ( $dispatchDate->GetDisplayName () );
			$this->mPage .= <<<EOT
				<input 	type="text" 
						name="ESTIMATE{$dispatchDate->GetDispatchDateId()}" 
						id="ESTIMATE{$dispatchDate->GetDispatchDateId()}" 
						value="{$estimateName}" 
						disabled />
						&nbsp;
				<a href="#" onClick="toggleTextInputEditable('{$dispatchDate->GetDispatchDateId()}','ESTIMATE','catalogueEditForm')" id="ESTIMATE{$dispatchDate->GetDispatchDateId()}Edit">Edit</a> 
				| 
				<a href="#" onClick="toggleDeleteField('{$dispatchDate->GetDispatchDateId()}','ESTIMATE','catalogueEditForm');" id="ESTIMATE{$dispatchDate->GetDispatchDateId()}Delete">Delete</a><br />
EOT;
		}
		$this->mPage .= <<<EOT
				</div>
EOT;
	}

} // End AdminProductView class


$page = new AdminCatalogueEditView ( );
echo $page->LoadDefault ( $_GET ['id'] );
if (isset ( $_GET ['tab'] )) {
	echo '<script language="javascript" type="text/javascript">
			showTab(\'' . $_GET ['tab'] . '\');
			</script>';
}
?>
