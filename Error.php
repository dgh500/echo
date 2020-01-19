<?php

//! A single error (message)
class Error {
	
	//! String : The error message to be displayed
	// This is hard coded as (X)HTML at the minute, possible extension to make it xml based
	var $mErrorMsg;
	//! Int : The line the error occurred on - use __LINE__
	var $mLineNumber;
	//! String : The file (and path to file) of the originating file - use __FILE__
	var $mFile;
	
	//! Constructor, initialises the erorr message
	/*! 
	* @param initialErrorMsg : String : Initial error message, displayed as a heading - use as a human-readable error message
	*/
	function Error($initialErrorMsg = '') {
		$this->mErrorMsg = '<div class="error_container" style="border: 1px dotted #000000;
	margin: 0px;
	margin-bottom: 10px;
	width: 50%;
	padding: 0px;
	padding-left: 10px;
	padding-bottom: 10px;
	font-family: Arial;
	font-size: 10pt;
	position: relative;">';
		$this->mErrorMsg .= '<h1 class="error_h1" style="font-size: 12pt;
	color: #FF0000;
	text-decoration: underline;">' . $initialErrorMsg . '</h1>';
		$this->mErrorMsg .= '</div>';
	}
	
	//! Add the line number of the error
	/*! 
	* @param lineNo : Int : Line number; use __LINE__ 
	*/
	function AddLineNumber($lineNo) {
		$this->AddToError ( '<strong>Line Number</strong>: ' . $lineNo . '<br />' );
	}
	
	//! Add the file name of the error
	/*! 
	* @param file : Int : File name; use __FILE__ 
	*/
	function AddFile($file) {
		$this->AddToError ( '<strong>File</strong>: ' . $file . '<br />' );
	}
	
	//! Add more information to the error
	/*!
	* @param additionalErrorInformation : String : The additional error information
	* @return Void
	*/
	function AddToError($additionalErrorInformation) {
		// This is a bit obtuse but just strips the </div> from the end of the error message, adds the additional
		// information then appends the </div> back to the end again
		$length = strlen ( $this->mErrorMsg );
		$this->mErrorMsg = substr ( $this->mErrorMsg, 0, ($length - 6) );
		$this->mErrorMsg .= $additionalErrorInformation;
		$this->mErrorMsg .= '</div>';
	}
	
	//! Just a shorthand function for errors that have a PDO error and line/file information
	/*!
	* @param $pdoError 	: A PDO::errorInfo array (where 2 is the message)
	* @param $line		: The line number of the error; use __LINE__
	* @param $file		: The file name of the error; use __FILE__
	* @return Void
	*/
	function PdoErrorHelper($pdoError, $line, $file) {
		//$this->AddToError ( '<strong>PDO Error:</strong><br />' . $pdoError [2] . '<br />' );
		#$this->AddFile ( $file );
		#$this->AddLineNumber ( $line );
	}
	
	//! Returns the full error message
	/*!
	* @return String
	*/
	function GetErrorMsg() {
		return $this->mErrorMsg;
	}

}

?>