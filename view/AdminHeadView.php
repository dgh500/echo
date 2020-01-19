<?php

//! Loads the <head> section of the page for the admin side of the site
class AdminHeadView extends AdminView {

	function __construct() {
		parent::__construct(false,false,false,false);
	}

	//! Generic load function - include any header() directives here
	/*
	 * @return String - Code for the page
	 */
	function LoadDefault($title = '') {
		header ( 'Cache-Control: no-cache' ); // HTTP 1.1; for IE
		header ( 'Pragma: no-cache' ); // HTTP 1.0; for Netscape // and old IEs
		header ( 'Expires: Wed, 11 Feb 1998 10:40:21 GMT' );
		$this->mPage .= <<<EOT
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<title>Echo Supplements Admin {$title}</title>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			</head>
EOT;
		return $this->mPage;
	}
}
?>
