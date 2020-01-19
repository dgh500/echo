<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	$sitemapPage = new SitemapView ( $registry->catalogue );
	echo $sitemapPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>