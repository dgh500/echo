<?php
include_once('../autoload.php');

//! Defines the edit a gallery form
class AdminGalleryEditView extends AdminView {
	
	function __construct($galleryId) {
		$jsIncludes = array('jqueryUi.js','jquery.alerts.js','Tabs.js','AdminGalleryEditView.js');
		$cssIncludes = array('jqueryUI.css','jquery.alerts.css.php','AdminGalleryEditView.css.php');
		parent::__construct(true,$cssIncludes,$jsIncludes);
		$this->mGallery = new GalleryModel($galleryId);
	}	
	
	function LoadDefault() {
		$this->LoadTabs();
		$this->LoadForm();
		return $this->mPage; 
	} // End LoadDefault

	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
<div id="adminGalleryViewTabContainer">
	<ul>
		<!-- These styles are hard-coded to assume precedance over the style sheet so that 'details' is by default the focused tab -->
		<li id="adminGalleryViewTabContainer-details"><a href="#" id="detailsLink">Details</a></li>
		<li id="adminGalleryViewTabContainer-items"><a href="#" id="itemsLink">Items</a></li>
	</ul>
</div>	
EOT;
	} // End LoadTabs

	//! Loads the form that allows editing of a gallery. Contains an iframe with ImageUploadHandler to upload new images for this gallery
	function LoadForm() {
		$this->mPage .= <<<EOT
			<form name="galleryEditForm" id="galleryEditForm" action="{$this->mRegistry->formHandlersDir}/GalleryEditHandler.php" method="post">
			<input type="hidden" name="galleryId" id="galleryId" value="{$this->mGallery->GetGalleryId()}" />
			<div id="galleryEditFormContainer">
EOT;
		$this->LoadDetailsSection();
		$this->LoadItemsSection();
		$this->mPage .= <<<EOT
				<div id="galleryEditFormButtons">
									<input type="submit" name="galleryEditSave" id="galleryEditSave" value="Save" />
									<input type="submit" value="Delete" name="galleryEditDelete" id="galleryEditDelete" />
				</div>			
			</form>
			</div>
EOT;
	} // End LoadForm

	//! Loads the details section
	function LoadDetailsSection() {		
		$this->mPage .= '
<div id="galleryDetailsContentArea">
	<label for="galleryDisplayName">Display Name</label><input type="text" name="galleryDisplayName" id="galleryDisplayName" value="'.$this->mGallery->GetDisplayName().'" /><br>
	<label for="galleryPanelWidth">Panel Width</label><input type="text" name="galleryPanelWidth" id="galleryPanelWidth" value="'.$this->mGallery->GetPanelWidth().'" />
	<label for="galleryPanelHeight">Panel Height</label><input type="text" name="galleryPanelHeight" id="galleryPanelHeight" value="'.$this->mGallery->GetPanelHeight().'" /><br>		
	<label for="galleryFrameWidth">Frame Width</label><input type="text" name="galleryFrameWidth" id="galleryFrameWidth" value="'.$this->mGallery->GetFrameWidth().'" />
	<label for="galleryFrameHeight">Frame Height</label><input type="text" name="galleryFrameHeight" id="galleryFrameHeight" value="'.$this->mGallery->GetFrameHeight().'" /><br>		
	<label for="galleryTransitionSpeed">Transition Speed</label><input type="text" name="galleryTransitionSpeed" id="galleryTransitionSpeed" value="'.$this->mGallery->GetTransitionSpeed().'" />
	<label for="galleryTransitionInterval">Transition Interval</label><input type="text" name="galleryTransitionInterval" id="galleryTransitionInterval" value="'.$this->mGallery->GetTransitionInterval().'" /><br>
	<label for="galleryNavTheme">Nav Theme</label><input type="text" name="galleryNavTheme" id="galleryNavTheme" value="'.$this->mGallery->GetNavTheme().'" readonly="readonly" /> 
	<a id="light" name="light" href="#">Light</a> | <a id="dark" name="dark" href="#">Dark</a>

</div>';
	}

	//! Loads the details section
	function LoadItemsSection() {		
		$this->mPage .= <<<EOT
				<div id="galleryItemsContentArea">
				<strong>Add Gallery Item</strong><br />
				<iframe src="{$this->mRegistry->formHandlersDir}/AddGalleryItemHandler.php?galleryId={$this->mGallery->GetGalleryId()}" 
						id="addGalleryItemIframe" 
						name="addGalleryItemIframe" 
						scrolling="no" 
						frameborder="0" 
						border="0" />
						</iframe>
						<br>
				<strong>Current Gallery Items</strong><br /><br />
EOT;
		$galleryContents = new AdminGalleryContentsView;
		$this->mPage .= <<<EOT
		<div id="editGalleryContainer" style="display: none">
			<form method="post" name="editGalleryItemForm" id="editGalleryItemForm" enctype="multipart/form-data">
				<input type="hidden" name="newGalleryGalleryItemId" id="newGalleryGalleryItemId" />
				<div id="editGalleryFormContainer" style="border: 1px solid #FFF; font-family: Arial, Helvetica, sans-serif; font-size: 10pt;">
					<div id="editGalleryDescriptionContainer" style="border: 1px solid #FFF; width: 400px; height: 130px; float: left;">
						<label for="newGalleryItemDescription"><strong>Description</strong>: </label>
						<textarea cols="45" rows="5" name="newGalleryItemDescription" id="newGalleryItemDescription"></textarea>
					</div>
					<br />
					<div id="editGalleryImageContainer" style="border: 1px solid #FFF; float: left;">
						<label for="newGalleryItemImage"><strong>Image</strong>: </label>
						<input type="file" name="newGalleryItemImage" id="newGalleryItemImage" />
					</div>
				</div>
				<br style="clear: both" />
			</form>
		</div>
EOT;
		$this->mPage .= '<div id="galleryItems">';
		$this->mPage .= $galleryContents->LoadDefault($this->mGallery);
		$this->mPage .= '</div>';
		$this->mPage .= <<<EOT
				</div>
EOT;
	}

} // End AdminGalleryEditView

$page = new AdminGalleryEditView($_GET['id']);
echo $page->LoadDefault();

?>