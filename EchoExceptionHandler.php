<?php

//! Handles exceptions, with allowances for debugging mode
function EchoExceptionHandler($exception) {
	$registry = Registry::getInstance();
	if(!$registry->debugMode) {
		echo '<h1>Echo Supplements Encountered an Unexpected Error</h1>';
		//! \todo LOG THE ERROR
	} else {
		$traceMsg = '';
		$traceArr = explode('#',$exception->getTraceAsString());
		foreach($traceArr as $traceLine) {
			$traceMsg .= $traceLine.'<br />';
		}
		echo '
		<div style="font-family: Arial; font-size: 12px; width: 700px; border: 3px solid #F00; padding: 10px; margin-left: auto; margin-right: auto">
			<h1>Exception Occured.</h1>
			<strong>Exception Message:</strong> <br />'.$exception->getMessage().'<br /><br />
			<strong>In File:</strong> <br />'.$exception->getFile().'<br /><br />
			<strong>On Line:</strong> <br />'.$exception->getLine().'<br /><br />
			<strong>Trace:</strong> <br />'.$traceMsg.'<br /><br />
			</div>';
	}
} // End Exception_Handler

?>