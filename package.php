<?php

require_once ('autoload.php');

try {
	$packagePage = new PackageView ( $_GET ['packageId'] );
	echo $packagePage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
