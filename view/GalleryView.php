<?php

//! Loads the 'gallery' based on the jQuery GalleryView Plugin
class GalleryView extends View {
	
	//! Sets up the JS and CSS
	/*!
	 * @param $gallery - Obj: GalleryModel - the gallery to load
	 */
	function __construct($gallery) {
		// CSS/JS settings
		$cssIncludes 	= array('GalleryView.css.php');
		$jsIncludes 	= array('jquery.preload.js','jquery.galleryview.js','jquery.easing.js','jquery.timers.js','GalleryView.js.php');
		parent::__construct(true,$cssIncludes,$jsIncludes);
		
		// Load the gallery
		$this->mGallery = $gallery;
	}
	
	//! Loads the gallery
	function LoadDefault() {
		$this->mPage .= '<ul id="gallery">';
		foreach($this->mGallery->GetGalleryItems() as $galleryItem) {
			$this->mPage .= '
							<li>
								<span class="panel-overlay">
									'.$galleryItem->GetCaptionText().'
								</span>
								<img src="'.$this->mRootDir.'/'.$this->mRegistry->originalImageDir.$galleryItem->GetImage()->GetFilename().'.jpeg" />
							</li>			
							';
		}
		$this->mPage .= '</ul>';
		return $this->mPage;
	}
	
} // End GalleryView

?>