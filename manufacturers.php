<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$manufacturersPage = new ManufacturersView ( $registry->catalogue );
	echo $manufacturersPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>