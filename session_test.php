<?php

session_start();
#session_write_close();
session_start();

$_SESSION['test'] = 'test';

session_start();

var_dump($_SESSION);
var_dump(session_id());


session_start();
#session_write_close();
session_start();

$_SESSION['test'] = 'test';

session_start();

var_dump($_SESSION);
var_dump(session_id());

echo 'Can Session Start';


?>