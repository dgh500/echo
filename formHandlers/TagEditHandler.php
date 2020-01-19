<?php
require_once ('../autoload.php');

class TagEditHandler extends Handler {
	
	var $mClean;
	var $mTag;
	
	function __construct() {
		parent::__construct();
		$this->mSessionHelper = new SessionHelper();
	}
	
	function Validate($postArr) {
		$this->mTag = new TagModel($postArr['tagId']);
		$this->mClean ['tagDisplayName'] = $this->mValidationHelper->MakeSafe($postArr['tagDisplayName']);
		$this->mClean ['tagDescription'] = $this->mValidationHelper->MakeSafe($postArr['tagDescription']);		
	}
	
	function DeleteTag() {
		$tagController = new TagController;
		$tagController->DeleteTag($this->mTag);
		echo 'Tag Deleted.';
	}
	
	function Process() {
		$this->mTag->SetDisplayName($this->mClean['tagDisplayName']);
		$this->mTag->SetDescription($this->mClean['tagDescription']);
		echo 'Tag Saved.';
	}

}

try {
	$handler = new TagEditHandler();
	if(isset($_POST['saveTag'])) {
		$handler->Validate($_POST);
		$handler->Process();
	} elseif(isset($_POST['deleteTagInput'])) {
		$handler->Validate($_POST);
		$handler->DeleteTag();
	}
} catch (Exception $e) {
	echo $e->getMessage();
}

?>