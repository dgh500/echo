<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$page = new PaypalOrderReceiptView($registry->catalogue);

	if(isset($_REQUEST['token'])) {
		echo $page->LoadDefault($_REQUEST['token']);
	} elseif(isset($_REQUEST['fail'])) {
		echo $page->LoadDefault('fail');
	} else {
		die('Something has gone wrong');
	}
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
