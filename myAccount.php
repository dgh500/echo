<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$accountPage = new AccountView ( $registry->catalogue );
	echo $accountPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}
?>
