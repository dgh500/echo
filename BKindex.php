<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	if (! $registry->disabled) {
		$indexPage = new IndexView ( $registry->catalogue );
	} else {
		$indexPage = new DisabledView ( $registry->catalogue );
	}
	echo $indexPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
