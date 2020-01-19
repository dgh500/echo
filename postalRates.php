<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$postalRatesPage = new PostalRatesView ( $registry->catalogue );
	echo $postalRatesPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>