<?php

class PresentationHelper {
	
	function ChopDown($str, $length, $dots = 0) {
		if ($dots) {
			return substr ( $str, 0, $length ) . '...';
		} else {
			return substr ( $str, 0, $length );
		}
	}
	
	function Money($num) {
		return number_format ( $num, 2 );
	}
	
	function DDMMYYYY($timestamp) {
		return date ( 'd/m/Y', $timestamp );
	}

	function SecondsToDays($seconds) {
		$days = floor($seconds/86400);
		return $days;
	} 
} // End PresentationHelper

?>