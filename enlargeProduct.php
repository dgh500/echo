<?php

require_once ('autoload.php');
try {
	$enlargePage = new EnlargeProductView ( );
	echo $enlargePage->LoadDefault ( $_GET ['productId'] );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
