<?php

include_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$sizeChartPage = new ContentView ( $registry->catalogue, $_GET ['content'] );
	echo $sizeChartPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
