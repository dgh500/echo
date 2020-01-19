<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$termsPage = new TermsView ( $registry->catalogue );
	echo $termsPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
