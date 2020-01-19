<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$page = new BestSellerView ( $registry->catalogue );
	echo $page->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
