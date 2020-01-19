<?php

require_once ('../autoload.php');

class ManufacturerEditHandler extends Handler {

	var $mClean;

	function __construct() {
		parent::__construct();
		$this->mSessionHelper = new SessionHelper();
	}

	function Validate($postArr) {
		if (isset ( $postArr ['manufacturerDisplay'] )) {
			$this->mClean['show'] = true;
		} else {
			$this->mClean['show'] = false;
		}
		$this->mClean['manufacturer'] = new ManufacturerModel($postArr['displayManufacturerId']);
		$this->mClean['manufacturerContent'] = $this->mValidationHelper->MakeSafe($postArr['manufacturerContent']);
		$this->mClean['manufacturerDescription'] = $postArr['manufacturerDescription'];
		$this->mClean['manufacturerDisplayName'] = $this->mValidationHelper->MakeSafe($postArr['manufacturerDisplayName']);
		$this->mClean['manufacturerBannerUrl'] = $this->mValidationHelper->MakeSafe($postArr['manufacturerBannerUrl']);
	}

	function Save() {
		try {
			if(!empty($this->mClean['manufacturerContent'])) {
				$content = new ContentModel($this->mClean['manufacturerContent']);
				$this->mClean['manufacturer']->SetSizeChart($content);
			} else {
				$this->mClean['manufacturer']->ResetSizeChart();
			}
		} catch(Exception $e) {
			echo 'Could not change content ID, incorrect ID.';
		}
		$this->mClean['manufacturer']->SetDisplayName($this->mClean['manufacturerDisplayName']);
		$this->mClean['manufacturer']->SetBannerUrl($this->mClean['manufacturerBannerUrl']);
		$this->mClean['manufacturer']->SetDescription($this->mClean['manufacturerDescription']);
		if ($this->mClean['show']) {
			$this->mClean['manufacturer']->SetDisplay(1);
			$newValue = 'True';
		} else {
			$this->mClean['manufacturer']->SetDisplay(0);
			$newValue = 'False';
		}
		echo 'Manufacturer Saved.';
	}

	function Delete() {
		$manufacturerController = new ManufacturerController;
		$manufacturerController->DeleteManufacturer($this->mClean['manufacturer']);
		echo 'Manufacturer Deleted';
	}

}

try {
	$handler = new ManufacturerEditHandler ( );
	$handler->Validate ( $_POST );
	if(isset($_POST['saveMan'])) {
		$handler->Save();
	} elseif(isset($_POST['deleteManInput'])) {
		$handler->Delete();
	}
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>