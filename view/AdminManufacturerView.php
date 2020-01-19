<?php
require_once ('../autoload.php');

//! Loads the admin interface for editing a single manufacturer
class AdminManufacturerView extends AdminView {

	//! Load the neccessary JS and CSS
	/*!
	 * @param [in] $manufacturerId - Int - The manufacturer to load
	 */
	function __construct($manufacturerId) {
		$jsIncludes = array('jqueryUi.js','jquery.alerts.js','Tabs.js','AdminManufacturerView.js','jquery.hoverIntent.js');
		$cssIncludes = array('AdminManufacturerView.css.php','jquery.alerts.css');
		parent::__construct(true,$cssIncludes,$jsIncludes);
		$this->mManufacturer = new ManufacturerModel($manufacturerId);
	}


	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
<div id="adminManufacturerViewTabContainer">
	<ul>
		<li id="adminManufacturerViewTabContainer-details"><a href="#" id="detailsLink">Details</a></li>
		<li id="adminManufacturerViewTabContainer-image"><a href="#" id="imageLink">Image</a></li>
	</ul>
</div>
EOT;
	}

	//! Load tabs, details, image, help, buttons
	function LoadDefault() {
		$this->LoadTabs();
		$this->LoadDetails();
		$this->LoadImage();
		$this->LoadHelp();
		$this->LoadButtons();
		return $this->mPage;
	} // End LoadDefault()

	function LoadButtons() {
		$this->mPage .= <<<EOT
	<div id="buttonsContainer">
	<input type="submit" value="Save" id="saveMan" name="saveMan" />
	<input type="button" value="Delete" id="deleteMan" name="deleteMan" />
	<a href="#"><img src="{$this->mAdminDir}/images/helpIcon_off.jpg" id="helpToggle" /></a>
	</div>
	</form>
EOT;
	}

	//! Load the help section
	function LoadHelp() {
		$this->mPage .= <<<EOT
		<div id="helpBox">
			<div id="manufacturerDisplayHelp" class="helpText">
				<strong>Display on Front Page</strong><br />
				Selecting this box will make this manufacturer appear in the 'Top Brands' section on the front page of the website.
			</div>
			<div id="manufacturerDescriptionHelp" class="helpText">
				<strong>Description</strong><br />
				This gets displayed on the 'Shop By Brand' page at the top of the page.
				<img src="{$this->mAdminDir}/images/helpManufacturerDescription.jpg" />
			</div>
			<div id="manufacturerContentHelp" class="helpText">
				<strong>Content ID</strong><br />
				This is the ID for the manufacturers size chart/description page and can be found from the content tab.
			</div>
			<div id="manufacturerImageHelp" class="helpText">
				<strong>Manufacturer Image</strong><br />
				This is displayed on the 'top brands' section, and at the top of the 'shop by brand' page.<br />
				<img src="{$this->mAdminDir}/images/helpManufacturerImage.jpg" />
			</div>
		</div>
EOT;
	}

	//! Load the details section
	function LoadDetails() {
		($this->mManufacturer->GetDisplay() 	? $checked = 'checked="checked"' : $checked = '' );
		($this->mManufacturer->GetSizeChart() 	? $contentId = $this->mManufacturer->GetSizeChart()->GetContentId() : $contentId = '' );

		// See http://www.fckeditor.net for full details
		$registry = Registry::getInstance();
		$adminPath = $registry->adminDir;
		$oFCKeditor = new FCKeditor('manufacturerDescription');
		$oFCKeditor->BasePath = $adminPath . '/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = $this->mManufacturer->GetDescription(false);
		$oFCKeditor->Height = 350;

		$this->mPage .= '
	<div id="manufacturerDetailsContentArea">
		<form action="'.$this->mFormHandlersDir.'/ManufacturerEditHandler.php" method="post" id="manufacturerEditForm" name="manufacturerEditForm">

			<label for="manufacturerDisplayName">Manufacturer Name: </label>
				<input type="text" name="manufacturerDisplayName" id="manufacturerDisplayName" value="'.$this->mManufacturer->GetDisplayName().'" /><br />

			<label for="manufacturerDisplay" id="manufacturerDisplayLabel">Display on front page?</label>
				<input type="checkbox" name="manufacturerDisplay" id="manufacturerDisplay" '.$checked.' />

			<input type="hidden" name="displayManufacturerId" id="displayManufacturerId" value="'.$this->mManufacturer->GetManufacturerId().'" /><br />

			<label for="manufacturerDescription" id="manufacturerDescriptionLabel">Description: </label>';
		$this->mPage .= $oFCKeditor->Create();
		$this->mPage .= '
			<label for="manufacturerContent" id="manufacturerContentLabel">Content ID: </label>
				<input type="text" name="manufacturerContent" id="manufacturerContent" value="'.$contentId.'" /><br />
		</div>';
	} // End LoadDetails

	//! Loads the image section
	function LoadImage() {
		// If the manufacturer already has an image, display it otherwise say there isn't one
		if ($this->mManufacturer->GetImage()) {
			$image = $this->mManufacturer->GetImage();
			$imageStr = '<img src="'.$this->mRootDir.'/'.$this->mManufacturerImageDir.$image->GetFilename().'" id="manufacturerImage" />';
		} else {
			// No Image
			$imageStr = 'No Image';
		}

		// Display the form
		$this->mPage .= <<<EOT
<div id="manufacturerImageContentArea">
<strong>Add an Image (175x80px)</strong><br />

<iframe src="{$this->mFormHandlersDir}/ImageUploadHandler.php?manufacturerId={$this->mManufacturer->GetManufacturerId()}"
		id="uploadImageIframe"
		name="uploadImageIframe"
		scrolling="no"
		frameborder="0"
		/></iframe><br />
<strong>Current Image</strong><br /><br />
{$imageStr}<br /><br />
<label for="manufacturerBannerUrl">Banner URL: </label>
	<input type="text" name="manufacturerBannerUrl" id="manufacturerBannerUrl" value="{$this->mManufacturer->GetBannerUrl()}" /><br />
</div>
EOT;
	} // End LoadImage

} // End AdminManufacturerView

// Load the AdminManufacturerView only if requested
if(isset($_GET['id'])) {
	$page = new AdminManufacturerView($_GET['id']);
	echo $page->LoadDefault();
}

?>