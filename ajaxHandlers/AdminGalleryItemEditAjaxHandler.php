<?php
include_once('../autoload.php');

// Delete the gallery item and return the new list
$retView 				= new AdminGalleryContentsView;
$galleryItem 			= new GalleryItemModel($_POST['newGalleryGalleryItemId']);
$validator				= new ValidationHelper;

// Action
$galleryItem->SetCaptionText($validator->MakeMysqlSafe($_POST['newGalleryItemDescription']));

// Return view
echo $retView->LoadDefault($galleryItem->GetGallery());

?>