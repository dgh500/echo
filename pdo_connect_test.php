<?php

$host 			= '10.0.43.11';
$username 		= 'echoDb';
$password 		= 'm0rpeth3cho';
$dbName 		= 'echo';

$dbo = new PDO ( 'mysql:host=' . $host . ';dbname=' . $dbName, $username, $password );
$dbo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

echo 'PDO connect success';

?>