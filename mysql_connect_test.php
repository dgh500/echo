<?php

$host 			= '10.0.43.11';
$username 		= 'echoDb';
$password 		= 'm0rpeth3cho';
$dbName 		= 'echo';

$link = mysql_connect($host,$username,$password);
mysql_select_db($dbName,$link);

echo 'mysql connect success';

?>