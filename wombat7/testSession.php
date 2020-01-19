<?php

session_save_path('../sesh');
session_start();
$_SESSION['test'] = 'foo';
$_SESSION['testtwo'] = 'bar';


?>