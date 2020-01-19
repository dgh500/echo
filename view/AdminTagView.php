<?php
require_once ('../autoload.php');

class AdminTagView extends AdminView {
	
	function __construct($tagId) {
		$jsIncludes = array('jqueryUi.js','jquery.hoverIntent.js','jquery.alerts.js','AdminTagView.js','InputListView.js','Tabs.js');
		$cssIncludes = array('admin.css.php','AdminTagView.css.php','jquery.alerts.css');
		parent::__construct(true,$cssIncludes,$jsIncludes);	
		$this->mTag = new TagModel($tagId);
	}
	
	function LoadDefault() {		
		$this->LoadTabs();		
		$this->LoadDetails();
		$this->LoadImage();
		$this->LoadHelp();
		$this->LoadButtons();				
		return $this->mPage;
	}
	
	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
<div id="adminTagViewTabContainer">
	<ul>
		<li id="adminTagViewTabContainer-details"><a href="#" id="detailsLink">Details</a></li>				
		<li id="adminTagViewTabContainer-image"><a href="#" id="imageLink">Image</a></li>
	</ul>
</div>	
EOT;
	}

	function LoadButtons() {
		$this->mPage .= <<<EOT
	<div id="buttonsContainer">
	<input type="submit" value="Save" name="saveTag" id="saveTag" />
	<input type="button" value="Delete" name="deleteTag" id="deleteTag" />
	<a href="#"><img src="{$this->mAdminDir}/images/helpIcon_off.jpg" id="helpToggle" /></a>
	</div>
	</form>
EOT;
	}

	//! Load the help section
	function LoadHelp() {
		$this->mPage .= <<<EOT
		<div id="helpBox">
			<div id="tagDescriptionHelp" class="helpText">
				<strong>Tag Description</strong><br />
				This gets displayed on the 'Shop By Goal' page at the top of the page.
				<img src="{$this->mAdminDir}/images/helpTagDescription.jpg" />
			</div>	
			
			<div id="tagImageHelp" class="helpText">
				<strong>Tag Image</strong><br />
				This is displayed on the 'Shop By Tag' section.<br />
				<img src="{$this->mAdminDir}/images/helpTagImage.jpg" />
			</div>
			
		</div>
EOT;
	}

	function LoadDetails() {
		$this->mPage .= <<<EOT
				<div id="tagDetailsContentArea">
				<form action="{$this->mFormHandlersDir}/TagEditHandler.php" method="post" id="tagEditForm" name="tagEditForm">
					<input type="hidden" name="tagId" id="tagId" value="{$this->mTag->GetTagId()}" />
					
					<label for="tagDisplayName">Tag Name: </label>
						<input type="text" value="{$this->mTag->GetDisplayName()}" name="tagDisplayName" id="tagDisplayName"><br />
					
					<label for="tagDescription">Description: </label><br />
						<textarea rows="4" cols="40" name="tagDescription" id="tagDescription">{$this->mTag->GetDescription()}</textarea><br />
				</div>
EOT;
	} // End LoadDetails()
	
	function LoadImage() {
		if ($this->mTag->GetImage ()) {
			$image = $this->mTag->GetImage();
			$imageString = '<img src="' . $this->mRootDir.'/'.$this->mRegistry->tagImageDir.$image->GetFilename () . '" id="tagImage" />';
		} else {
			// No Image
			$imageString = 'No Image';
		}		
		$this->mPage .= <<<EOT
			<div id="tagImageContentArea">
				<strong>Add an Image</strong><br />
				
				<iframe src="{$this->mFormHandlersDir}/ImageUploadHandler.php?tagId={$this->mTag->GetTagId()}" 
						id="uploadImageIframe" 
						name="uploadImageIframe" 
						scrolling="no" 
						frameborder="0" 
						/></iframe><br />
				<strong>Current Image</strong><br /><br />
				{$imageString}
			</div>
EOT;
	} // End LoadImage

}

if (isset ( $_GET ['id'] )) {
	$page = new AdminTagView($_GET ['id']);	
	echo $page->LoadDefault();
}

?>