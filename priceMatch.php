<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$priceMatchView = new PriceMatchView ( $registry->catalogue );
	echo $priceMatchView->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
