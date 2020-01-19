<?php
session_start ();
require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class ContentUpdateHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$registry = Registry::getInstance ();
		$this->adminDir = $registry->adminDir;
	}
	
	function Validate($postArr) {
		$this->mClean ['displayName'] 	= $this->mValidationHelper->MakeSafe($postArr['displayName']);
		$this->mClean ['longText'] 		= $this->mValidationHelper->RemoveWhitespace($postArr['longText']);	// NB. Don't make mysql safe, because its being written to a file
		$this->mClean ['description'] 	= $this->mValidationHelper->RemoveWhitespace ( $this->mValidationHelper->MakeMysqlSafe ( $postArr ['description'] ) );	
		$this->mClean ['contentId'] 	= $postArr ['contentId'];
		$this->mClean ['contentType'] 	= $postArr ['contentType'];
	}
	
	function UpdateContent() {
		$content = new ContentModel ( $this->mClean ['contentId'] );
		$contentStatus = new ContentStatusModel ( $this->mClean ['contentType'] );
		$content->SetContentType ( $contentStatus );
		$content->SetDisplayName ( $this->mClean ['displayName'] );
		$content->SetDescription ( $this->mClean ['description'] );
		$content->SetLongText ( $this->mClean ['longText'] );
		$sendTo = $this->adminDir . '/content/' . $content->GetContentId ();
		header ( 'Location: ' . $sendTo );
	}
	
	function DeleteContent() {
		$contentController = new ContentController ( );
		$content = new ContentModel ( $this->mClean ['contentId'] );
		$contentController->DeleteContent ( $content );
		$sendTo = $this->adminDir . '/content';
		header ( 'Location: ' . $sendTo );
	}

}

try {
	$handler = new ContentUpdateHandler ( );
	$handler->Validate ( $_POST );
	if (isset ( $_POST ['saveContent'] )) {
		$handler->UpdateContent ();
	} elseif (isset ( $_POST ['deleteContent'] )) {
		$handler->DeleteContent ();
	}
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>