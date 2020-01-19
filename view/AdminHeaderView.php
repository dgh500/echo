<?php

//! Loads the admin header view - the logo and tabs
class AdminHeaderView extends AdminView {

	function __construct() {
		parent::__construct('',false,false,false);
	}

	//! Generic load function
	/*
	 * @return String - Code for the page
	 */
	function OpenHeader($pageId, $secure = false) {
		$registry = Registry::getInstance();
		if ($secure) {
			$dir = $registry->secureBaseDir;
		} else {
			$dir = $registry->baseDir;
		}
		$str = '
<a href="index.php">
	<img src="' . $dir . '/wombat7/images/adminLogo.gif" alt="'.$this->mCompanyName.'" id="adminHeaderLogo" />
</a>
<div id="' . $pageId . '">
	<div id="adminMenuHeader">';
		return $str;
	}

	function CloseHeader() {
		$str = '
	</div> <!-- Close adminMenuHeader -->
</div> <!-- Close pageId -->';
		return $str;
	}
}
?>
