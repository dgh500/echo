<?php

require_once ('autoload.php');

$registry = Registry::getInstance ();
$articlesPage = new ArticlesView ( $registry->catalogue );
echo $articlesPage->LoadDefault ();

?>