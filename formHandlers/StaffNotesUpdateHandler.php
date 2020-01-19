<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class StaffNotesUpdateHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
	}
	
	function Validate($postArr) {
		$this->mClean ['staffNotes'] = $this->mValidationHelper->MakeSafe ( $postArr ['staffNotes'] );
		$this->mClean ['order'] = new OrderModel ( $postArr ['orderId'] );
	}
	
	function Save() {
		$registry = Registry::getInstance ();
		$this->mClean ['order']->SetStaffNotes ( $this->mClean ['staffNotes'] );
		echo '<script language="javascript" type="text/javascript">
				self.location.href=\'' . $registry->viewDir . '/OrdersEditView.php?id=' . $this->mClean ['order']->GetOrderId () . '\'
		</script>';
	}

}

try {
	$handler = new StaffNotesUpdateHandler ( );
	$handler->Validate ( $_POST );
	$handler->Save ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>