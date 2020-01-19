<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$page = new PaypalCheckoutCompleteView($registry->catalogue);

	if(isset($_REQUEST['result'])) {
		echo $page->LoadDefault($_REQUEST['result']);
	} else {
		die('Something has gone wrong');
	}
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
