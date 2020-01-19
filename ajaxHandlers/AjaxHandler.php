<?php

class AjaxHandler {
	
	var $mReturn;
	
	function Initialise() {
		$this->SetDefaultHeaders ();
	}
	
	function ReturnResponse() {
		echo $this->mReturn;
	}
	
	function SetDefaultHeaders() {
		header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
		header ( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
		header ( "Pragma: no-cache" );
	}
	
	function SetDataType($type = 'xml') {
		switch ($type) {
			case 'xml' :
				header ( "content-type: text/xml" );
				break;
		}
	}

}

?>