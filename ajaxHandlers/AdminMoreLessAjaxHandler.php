<?php
session_start();

// Takes the action showMore and showLess and changes $_SESSION['showAllTabs'] to true/false depending on the result

if($_POST['action'] == 'showMore') {
	$_SESSION['showAllTabs'] = true;
} else {
	$_SESSION['showAllTabs'] = false;	
}


?>