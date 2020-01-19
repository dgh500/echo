<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$clearancePage = new ClearanceView ( $registry->catalogue );
	echo $clearancePage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
