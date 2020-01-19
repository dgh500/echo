<?php

require_once ('autoload.php');
try {
	$enlargePage = new EnlargePackageView ( );
	echo $enlargePage->LoadDefault ( $_GET ['packageId'] );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
