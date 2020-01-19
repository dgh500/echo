<?php

require_once('dictionary.php');

$q = strtolower($_REQUEST["q"]);
if (!$q) return;
foreach ($dictionary as $key=>$value) {
	if (strpos(strtolower($key), $q) !== false) {
		echo "$key\n";
	}
}

?>