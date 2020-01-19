<?php

//! Defines the view for the missing section of the admin area
class AdminHelpView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuHelp';
	
	function __construct() {
		parent::__construct('Admin > Help',false,false,false);
		$this->IncludeCss('AdminHelpView.css.php');
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->InitialiseDisplay ();
			$this->InitialiseContentDisplay ();
			$this->LoadPhoneDisplay ();
			$this->CloseContentDisplay ();
			$this->CloseDisplay ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}
	
	function LoadPhoneDisplay() {
		$registry = Registry::getInstance ();
		$this->mPage .= <<<EOT
			<strong>Retrieve Phone Number</strong><br />
			<form action="{$registry->formHandlersDir}/OldGetPhoneNumberHandler.php" method="post" target="_blank">
				<label for="email">Email: </label>
				<input type="text" name="email" id="email" /><br />
				<input type="submit" value="Get Numbers" />
			</form>
EOT;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminHelpViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminHelpViewContentContainer">
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
$page = new AdminHelpView ( );
echo $page->LoadDefault ();

?>