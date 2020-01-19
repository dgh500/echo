<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$linksPage = new LinksView ( $registry->catalogue );
	echo $linksPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>