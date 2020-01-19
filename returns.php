<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$returnsPage = new ReturnsView ( $registry->catalogue );
	echo $returnsPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>