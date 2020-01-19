<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$brochurePage = new BrochureView ( $registry->catalogue );
	echo $brochurePage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
