<?php

require_once ('autoload.php');

if (isset ( $_GET ['sortBy'] )) {
	$sortBy = $_GET ['sortBy'];
} else {
	$sortBy = 'DisplayName';
}
if (isset ( $_GET ['sortDirection'] )) {
	$sortDirection = $_GET ['sortDirection'];
} else {
	$sortDirection = 'DESC';
}
if (isset ( $_GET ['page'] )) {
	$page = $_GET ['page'];
} else {
	$page = 1;
}
if (isset ( $_GET ['categoryId'] )) {
	$categoryId = $_GET ['categoryId'];
} else {
	$categoryId = false;
}
if (isset ( $_GET ['showAll'] )) {
	$showAll = 1;
} else {
	$showAll = 0;
}

$tagPage = new TagView ( $_GET ['tagId'], $sortBy, $sortDirection, $page, $categoryId, $showAll );
echo $tagPage->LoadDefault ();

?>
