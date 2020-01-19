<?php

//! Defines the view for the content section of the admin area
class AdminContentView extends AdminView {

	function __construct() {
		parent::__construct('Admin > Content',false,false,false);
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('Tabs.js');
		$this->IncludeJs('AdminContentView.js');
		$this->IncludeCss('AdminContentView.css.php');
		$this->mContentController = new ContentController();
	}

	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuContent';

	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault($contentId = false, $add = false) {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->mContentId = $contentId;
			$this->mAdd = $add;
			$this->mContentController = new ContentController ( );
			$this->InitialisePage ();
			$this->InitialiseDisplay ();		// Open DIV
			$this->InitialiseContentDisplay ();	// Open DIV
			$this->LoadContentDisplay();
			$this->CloseContentDisplay();		// CloseDiv
			$this->CloseDisplay ();				// Close DIV
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}

	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
		<div id="adminContentViewTabContainer">
			<ul>
				<li id="adminContentViewTabContainer-description">
					<a href="#" id="descriptionLink">Description</a>
				</li>
				<li id="adminContentViewTabContainer-image">
					<a href="#" id="imageLink">Images</a>
				</li>
			</ul>
		</div>
EOT;
	}

	// Initialise the display - MUST be matched by $this->CloseDisplay()
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminContentViewContainer"><br />';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	function LoadAddContent() {
		// See http://www.fckeditor.net for full details
		$oFCKeditor = new FCKeditor ( 'longText' );
		$oFCKeditor->BasePath = $this->mBaseDir . '/wombat7/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = '';
		$oFCKeditor->Height = 350;

		$this->mPage .= <<<EOT
			<h1 style="margin: 0px;margin-bottom: 10px;">Add Content</h1>
			<form action="{$this->mBaseDir}/formHandlers/ContentAddHandler.php" method="post" />
			<label for="displayName">Display Name: </label>
				<input type="text" name="displayName" id="displayName" /><br />
			<label for="longDescription">Content:</label><br />
EOT;
		$this->mPage .= $oFCKeditor->Create ();
		$this->mPage .= <<<EOT
		<input type="submit" value="Save Content" />
		</form>
EOT;
	}

	function LoadContentDisplay() {
		// Load Navigation
		$this->LoadContentListNav();
		// Open edit area
		$this->mPage .= '<div id="contentEditAreaContainer">';
		// If there is a content ID (ie. content has been selected) then load it, otherwise if 'add' has been chosen, load the add content screen, otherwise do nothing (wait for input)
		if ($this->mContentId) {
			$this->LoadContentDetails ();
		} elseif ($this->mAdd) {
			$this->LoadAddContent ();
		}
		// Close edit area
		$this->mPage .= '</div>';
	}

	//! Loads the navigation on the left hand side
	function LoadContentListNav() {
		$this->mPage .= '
		<div id="contentNavContainer">
			<ul id="contentNavList">
			<li><a href="' . $this->mBaseDir . '/wombat7/content.php?add=1" id="add">Add Content</a></li>';
		$contentStatusController = new ContentStatusController ( );
		$contentController = new ContentController ( );
		$allContentTypes = $contentStatusController->GetAll ();
		foreach ( $allContentTypes as $contentType ) {
			$this->mPage .= '<li><a href="#" onclick="toggleVisible(' . $contentType->GetContentStatusId () . ')"><strong>' . $contentType->GetDisplayName () . '</strong></a></li>';
			$this->mPage .= '<div id="contentStatus' . $contentType->GetContentStatusId () . '" style="display: none;">';
			$contentForStatus = $contentController->GetContentForStatus ( $contentType );
			foreach ( $contentForStatus as $content ) {
				$this->mPage .= '<li><a href="' . $this->mBaseDir . '/wombat7/content.php?contentId=' . $content->GetContentId () . '"> - ' . $content->GetDisplayName () . '</a></li>';
			}
			$this->mPage .= '</div>';
		}
		$this->mPage .= '</ul>
		</div>';
	}

	//! Loads the form for display name, link, status and long text
	function LoadDescriptionTab() {
		// Open description tab container
		$this->mPage .= '<div id="contentDescriptionContentArea">';

		// Setup the FCK editor to have id 'longText', with custom toolbars and to be 300px high
		// See http://www.fckeditor.net for full details
		$oFCKeditor = new FCKeditor('longText');
		$oFCKeditor->BasePath = $this->mBaseDir . '/wombat7/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = $this->mContent->GetLongText ();
		$oFCKeditor->Height = 350;

		// Trim the content display name & description
		$contentName = trim($this->mContent->GetDisplayName());
		$contentDescription = trim($this->mContent->GetDescription());

		// Load the link value for the content (Structure BASE_DIR/CONTENT_ID/CONTENT_DISPLAY_NAME)
		$href = $this->mBaseDir.'/content/'.$this->mContent->GetContentId().'/'.$this->mValidationHelper->MakeLinkSafe($this->mContent->GetDisplayName());

		// Open the form
		$this->mPage .= <<<EOT
			<form action="{$this->mBaseDir}/formHandlers/ContentUpdateHandler.php" method="post" />
			<input type="hidden" name="contentId" id="contentId" value="{$this->mContent->GetContentId()}" />
EOT;

		// Display a dropdown list of all of the possible statuses
		$this->LoadAllStatusesDropdown();

		// Load the display name, link and short description
		$this->mPage .= <<<EOT
			<label for="displayName" style="display: block; float: left; width: 120px;">Display Name: </label>
				<input type="text" name="displayName" id="displayName" value="{$contentName}" style="width: 300px; float: left; display: block" /><br />
			<label for="linkUrl" style="display: block; float: left; width: 120px;">Link: </label>
				<input type="text" name="linkUrl" id="linkUrl" value="{$href}" style="width: 500px; float: left; display: block" /><br />
			<label for="description" style="display: block; float: left; width: 120px;">Description: </label>
				<textarea name="description" id="description" cols="60" rows="3" style="float: left; display: block">{$contentDescription}</textarea><br />
			<label for="longDescription">Content:</label><br />
EOT;
		// Load FCKeditor
		$this->mPage .= $oFCKeditor->Create();

		// Close description tab container
		$this->mPage .= '</div>';
	} // End LoadDescriptionTab

	//! Loads the tab for adding thumbnail and full imaga
	function LoadImagesTab() {
		// Open images tab container
		$this->mPage .= '<div id="contentImageContentArea">';

		// Display the thumbnail image form
		$this->mPage .= <<<EOT
				<strong>Add Thumbnail Image (75x75px)</strong><br />

				<iframe src="{$this->mFormHandlersDir}/ImageUploadHandler.php?contentId={$this->mContentId}&imageType=thumbnail"
						id="uploadImageIframe"
						name="uploadImageIframe"
						scrolling="no"
						frameborder="0"
						/></iframe><br />
				<strong>Current Thumbnail</strong><br /><br />
EOT;
		if ($this->mContent->GetThumbImage ()) {
			$image = $this->mContent->GetThumbImage();
			$this->mPage .= '<img src="' . $this->mRootDir.'/'.$this->mRegistry->contentImageDir.$image->GetFilename () . '" />';
		} else {
			// No Image
			$this->mPage .= 'No Image';
		}

		// Display the header image form
		$this->mPage .= <<<EOT
				<br /><br style="clear: both;" /><hr /><br />
				<strong>Add Header Image (540x80px)</strong><br />

				<iframe src="{$this->mFormHandlersDir}/ImageUploadHandler.php?contentId={$this->mContentId}&imageType=header"
						id="uploadImageIframe"
						name="uploadImageIframe"
						scrolling="no"
						frameborder="0"
						/></iframe><br />
				<strong>Current Header</strong><br /><br />
EOT;
		if ($this->mContent->GetHeaderImage ()) {
			$image = $this->mContent->GetHeaderImage();
			$this->mPage .= '<img src="' . $this->mRootDir.'/'.$this->mRegistry->contentImageDir.$image->GetFilename () . '" />';
		} else {
			// No Image
			$this->mPage .= 'No Image';
		}

	// Close images tab container
	$this->mPage .= '</div>';
	} // End LoadImagesTab

	//! Loads a dropdown (select) menu of all of the possible statuses content can have
	function LoadAllStatusesDropdown() {
		// Get all possible statuses
		$allStatuses = $this->mContentController->GetAllContentStatus();

		// Start the <select>
		$this->mPage .= '<label for="contentType" style="display: block; float: left; width: 120px;">Type: </label>';
		$this->mPage .= '<select name="contentType" id="contentType" style="width: 150px; float: left; display: block">';
		// Loop over all the possible statuses
		foreach($allStatuses as $status) {
			// If the content has a content type already, then display it as selected
			if(!is_null($this->mContent->GetContentType())) {
				if($status->GetContentStatusId() == $this->mContent->GetContentType()->GetContentStatusId()) {
					$this->mPage .= '<option value="'.$status->GetContentStatusId().'" selected="selected">'.$status->GetDisplayName().'</option>';
				} else {
					$this->mPage .= '<option value="'.$status->GetContentStatusId().'">'.$status->GetDisplayName().'</option>';
				}
			// Otherwise just display them as a list (and the default will get saved when the user presses save
			} else {
				$this->mPage .= '<option value="'.$status->GetContentStatusId().'">'.$status->GetDisplayName().'</option>';
			}
		} // End foreach

		// Close the <select>
		$this->mPage .= '</select><br /><br />';
	} // End LoadAllStatusesDropdown()

	//! Loads the edit page for a given piece of content
	function LoadContentDetails() {

		// Which content are we loading
		$this->mContent = new ContentModel($this->mContentId);

		// Load the tabs for the content
		$this->LoadTabs();

		// Load the description tab
		$this->LoadDescriptionTab();

		// Load images tab
		$this->LoadImagesTab();

		// Load buttons (Save/Delete)
		$this->LoadButtons();
		$this->mPage .= '</form>';
	} // End LoadContentDetails()

	function LoadButtons() {
		$this->mPage .= <<<EOT
		<div style="border: 0px solid #ccc; width: 710px; text-align: right; padding-top: 10px;">
			<input type="submit" value="Save Content" id="saveContent" name="saveContent" /> <input type="submit" value="Delete Content" id="deleteContent" name="deleteContent" />
		</div>
EOT;
	}

	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminContentViewContentContainer">
EOT;
	}

	// Closes the content display
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}

	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}

}
$page = new AdminContentView ( );
if (isset ( $_GET ['contentId'] )) {
	echo $page->LoadDefault ( $_GET ['contentId'] );
} elseif (isset ( $_GET ['add'] )) { #
	echo $page->LoadDefault ( false, true );
} else { #
	echo $page->LoadDefault ();
}
?>