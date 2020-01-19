<?php

//! Defines the edit a gallery form
class AdminGalleryContentsView extends AdminView {

	function LoadDefault($gallery) {
		$this->mGallery = $gallery;
		$allGalleryItems = $this->mGallery->GetGalleryItems();
		foreach ( $allGalleryItems as $galleryItem ) {
			$this->mPage .= 
			'
		<input type="hidden" name="galleryCaption'.$galleryItem->GetGalleryItemId().'" id="galleryCaption'.$galleryItem->GetGalleryItemId().'" value="'.htmlentities($galleryItem->GetCaptionText()).'" />
		<div id="currentGalleryContainer">
			<div id="addGalleryCaptionContainer">
				'.$galleryItem->GetCaptionText().'
			</div>
			<div id="addGalleryImageContainer">
				<img src="'.$this->mRegistry->rootDir.'/'.$this->mRegistry->originalImageDir.$galleryItem->GetImage()->GetFilename().'.jpeg" />
			</div>
			<div id="addGalleryButtonContainer">
				<img src="'.$this->mRegistry->adminDir.'/images/galleryEdit.jpg" id="'.$galleryItem->GetGalleryItemId().'" class="editGalleryButton" />
				<img src="'.$this->mRegistry->adminDir.'/images/galleryDelete.jpg" id="'.$galleryItem->GetGalleryItemId().'" class="deleteGalleryButton" />	
			</div>
		</div>
			';
		}
		return $this->mPage;
	} // End LoadDefault

} // End AdminGalleryContentsView

?>