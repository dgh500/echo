<?php

require_once ('../autoload.php');

class GalleryEditHandler {
	
	var $mClean;
	
	function ValidateInput($submittedArray, $gallery) {
		$validator = new ValidationHelper ( );
		$this->mGallery = $gallery;
		foreach ( $submittedArray as $key => $value ) {
			switch ($key) {
				case 'galleryDisplayName' :
					$this->mClean [$key] = $validator->MakeMysqlSafe ( $value );
					break;
				default :
					$this->mClean [$key] = $submittedArray [$key];
					break;
			}
		}
	}
	
	function DeleteGallery($gallery) {
		$galleryController = new GalleryController;
		$galleryController->DeleteGallery($gallery);
	}
	
	// ValidateInput MUST be called before this!
	function SaveGallery() {		
		// Text 
		$this->mGallery->SetDisplayName($this->mClean['galleryDisplayName']);
		$this->mGallery->SetFrameHeight($this->mClean['galleryFrameHeight']);
		$this->mGallery->SetFrameWidth($this->mClean['galleryFrameWidth']);
		$this->mGallery->SetPanelWidth($this->mClean['galleryPanelWidth']);
		$this->mGallery->SetPanelHeight($this->mClean['galleryPanelHeight']);
		$this->mGallery->SetTransitionSpeed($this->mClean['galleryTransitionSpeed']);
		$this->mGallery->SetTransitionInterval($this->mClean['galleryTransitionInterval']);		
		$this->mGallery->SetNavTheme($this->mClean['galleryNavTheme']);		
	} // End function
} // End class


try {
	$gallery = new GalleryModel($_POST['galleryId']);
	$handler = new GalleryEditHandler();
	if(isset($_POST['galleryEditSave'])) {
		$registry = Registry::getInstance();
		$handler->ValidateInput($_POST,$gallery);
		$handler->SaveGallery();
		echo '<h1 style="font-family: Arial; font-size: 12pt;">'.$gallery->GetDisplayName().' Saved.</h1>';
		echo '<script language="javascript" type="text/javascript">
				self.location.href=\''.$registry->viewDir.'/AdminGalleryEditView.php?id='.$_POST['galleryId'].'\'
		</script>';
	} elseif(isset($_POST['galleryEditDelete'])) {
		$name = $gallery->GetDisplayName();
		$handler->DeleteGallery($gallery);
		echo '<h1 style="font-family: Arial; font-size: 12pt;">'.$name.' Deleted.</h1>';
		?>
<script language="javascript" type="text/javascript">
				self.parent.window.frames["galleryMenu"].location.reload();
			</script>
<?php
	}
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>