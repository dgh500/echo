<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$offersPage = new OffersOfTheWeekFullView ( $registry->catalogue );
	echo $offersPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
