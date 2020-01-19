<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$trackingView = new OrderTrackingView ( $registry->catalogue );
	echo $trackingView->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
