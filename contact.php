<?php

require_once ('autoload.php');

$registry = Registry::getInstance ();
$contactPage = new ContactView ( $registry->catalogue );
echo $contactPage->LoadDefault ();

?>
