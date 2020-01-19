<?php
chdir ( dirname ( __FILE__ ) );
include('../autoload.php');
$gbh = new GoogleBaseHelper();
$gbh->GenerateFeed();
$gbh->PublishFeed();

?>