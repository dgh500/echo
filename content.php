<?php

require_once ('autoload.php');

try {
	if (is_numeric ( $_GET ['id'] )) {
		$id = $_GET ['id'];
	} else {
		die ( 'Could not load content' );
	}
	$registry = Registry::getInstance ();
	$contentPage = new ContentView ( $registry->catalogue, $id );
	echo $contentPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
