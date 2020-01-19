<?php

require_once ('autoload.php');
try {
	$registry = Registry::getInstance ();
	$tagsPage = new TagsView ( $registry->catalogue );
	echo $tagsPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>