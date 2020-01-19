<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$privacyPage = new PrivacyView ( $registry->catalogue );
	echo $privacyPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>