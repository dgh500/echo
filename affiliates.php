<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$affiliatesPage = new AffiliatesView ( $registry->catalogue );
	echo $affiliatesPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>