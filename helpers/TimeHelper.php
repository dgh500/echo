<?php

class TimeHelper {
	
	//! Returns eg. 03/01/2009 9:30
	function TimestampToDateAndTime($stamp) {
		return date ( 'd/m/Y H:s', $stamp );
	}
	
	//! Returns eg. Mon 3rd Jan 2009 at 9.30am
	function FriendlyDateTime($stamp) {
		return date('D jS M Y \a\t G:ia',$stamp);	
	}
	
}

?>