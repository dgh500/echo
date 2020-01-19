<?php

class AdminLoginView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuLogin';
	
	function __construct() {
		$cssIncludes = array('AdminLoginView.css.php');
		$jsIncludes = array('AdminLoginView.js');
		parent::__construct('x',$cssIncludes,$jsIncludes,false);		
	}		
		
	function LoadDefault($secure='no') {
		$this->InitialisePage ();
		$this->LoadLoginForm ($secure);
		return $this->mPage;
	}
	
	function LoadLoginForm($secure) {
		$adminHelper = new AdminHelper ( );
		$registry = Registry::getInstance();
		if($secure == 'no') { $dir = $registry->baseDir; } else { $dir = $registry->secureBaseDir; }
		
		if ($adminHelper->LoginCheck()) {
			$errorMsg = '';
		} else {
			if(!$adminHelper->FirstLoad()) {
				$errorMsg = '<strong>Error: </strong>Wrong username/password.';
				$overStyle = 'style="display: block"';
			} else {
				$errorMsg = '';
				$overStyle = '';
			}
		}
		$this->mPage .= <<<EOT
			<form id="adminLoginForm" name="adminLoginForm" action="{$dir}/formHandlers/AdminLoginHandler.php" method="post">
			<fieldset>
				<legend>Login</legend>
				<label for="loginName">Username: </label>
					<input type="text" name="loginName" id="loginName" /><br />
				<label for="loginPassword">Password: </label>
					<input type="password" name="loginPassword" id="loginPassword" />
				<input type="hidden" name="secure" id="secure" value="{$secure}" />
				<input type="submit" value="Log In" class="submit" />
				<div id="errorBox" {$overStyle}>{$errorMsg}</div>
			</fieldset>
			</form>
EOT;
	}
	
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}

}

?>