<?php
include_once('../autoload.php');

// Delete the gallery item and return the new list
$retView 				= new AdminGalleryContentsView;
$galleryItemController 	= new GalleryItemController;
$galleryItem 			= new GalleryItemModel($_POST['galleryItemId']);

// Action
$galleryItemController->DeleteGalleryItem($galleryItem);
$gallery = new GalleryModel($_POST['galleryId']);

// Return view
echo $retView->LoadDefault($gallery);

?>