<?php

require_once ('autoload.php');

$registry = Registry::getInstance ();
$page = new SearchView ( $registry->catalogue,$_REQUEST['q'] );
echo $page->LoadDefault ();

?>
