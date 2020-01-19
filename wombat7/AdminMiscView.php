<?php

include_once ('autoload.php');

class AdminMiscView extends View {
	
	var $mPageId = 'adminMenuMisc';
	
	function LoadDefault() {
		$this->InitialisePage ();
		$this->InitialiseDisplay ();
		$this->InitialiseContentDisplay ();
		$this->mPage .= 'as';
		$this->CloseContentDisplay ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminMiscViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminMiscViewContentContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}
	
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeadView = new AdminHeadView ( );
		$this->mPage .= $adminHeadView->LoadDefault ();
		$this->mPage .= <<<EOT
			<a href="index.php"><img src="http://localhost/deepblue08/admin/images/deepbluedive_logo.gif" height="60" width="283" alt="Deep Blue Dive Centre" id="adminHeaderLogo" /></a>			
			<div id="{$this->mPageId}">
				<div id="adminMenuHeader">
EOT;
		$this->mPage .= $adminTabsView->LoadDefault ();
		
		$this->mPage .= <<<EOT
				</div>
			</div>
EOT;
	}

}
$page = new AdminMiscView ( );
$page->IncludeCss ( 'admin.css.php' );
$page->IncludeCss ( 'adminForms.css.php' );

echo $page->LoadDefault ();

?>