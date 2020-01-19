<?php
session_start ();
require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class ContentAddHandler {

	var $mClean;

	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$registry = Registry::getInstance ();
		$this->adminDir = $registry->adminDir;
	}

	function Validate($postArr) {
		$this->mClean ['displayName'] = $this->mValidationHelper->MakeSafe ( $postArr ['displayName'] );
		$this->mClean ['longText'] = $this->mValidationHelper->RemoveWhitespace ( $postArr ['longText'] );
	}

	function AddContent() {
		$contentController = new ContentController ( );
		$content = $contentController->CreateContent ();
		$content->SetDisplayName ( $this->mClean ['displayName'] );
		$content->SetLongText ( $this->mClean ['longText'] );
		$sendTo = $this->adminDir . '/content/' . $content->GetContentId ();
		header ( 'Location: ' . $sendTo );
	}

}

try {
	$handler = new ContentAddHandler ( );
	$handler->Validate ( $_POST );
	$handler->AddContent ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>