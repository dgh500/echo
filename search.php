<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$searchPage = new SearchView ( $registry->catalogue );
	echo $searchPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>