<?php

class TimerHelper {
	
	function GetTime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec*1000);
	}
	
	function Difference($startTime,$endTime) {
		return sprintf("%01.7f",(($endTime-$startTime)/1000));	
	}

}

?>