<?php

// Settings
require_once('../autoload.php');

// Need the dictionary
require_once('../dictionary.php');

$val = new ValidationHelper;

// What are we looking for?
$input = $_POST['q'];

// What is it's ID?
if($input != '' && isset($dictionary[htmlentities($input)])) {
	$id = $dictionary[htmlentities($input)];
} else {
#	var_dump($input);die();
	$id = false;
}
if($id) {

	// Log success
	$fh = fopen("success.php","a+");
	fwrite($fh,$input.',');
	fclose($fh);

	// Get URL
	$plHelper = new PublicLayoutHelper;
	$product  = new ProductModel($id);
	$href = $plHelper->LoadLinkHref($product);

	// Redirect direct to the supps page
	header('Location: '.$href);
} else {

	// Log Attempt
	$fh = fopen("attempts.php","a+");
	fwrite($fh,$input.',');
	fclose($fh);

	// Redirect to not found page
	header('Location: '.$registry->baseDir.'/q/'.$val->MakeLinkSafe($input));
}
?>