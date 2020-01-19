<?php
require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$basketPage = new BasketView ( $registry->catalogue );
	echo $basketPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>