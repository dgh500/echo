<?php
require_once ('../autoload.php');

class OrdersEditView extends AdminView {
	
	function LoadDefault($orderId) {
		$orderView = new OrderView();
		$order = new OrderModel ( $orderId );
		$registry = Registry::getInstance();
		$dir = $registry->secureBaseDir;
		$this->mPage .= '<div id="staffNotes">';
		$this->mPage .= '<form action="' . $dir . '/formHandlers/StaffNotesUpdateHandler.php" method="post">';
		$this->mPage .= '<input type="hidden" name="orderId" id="orderId" value="' . $orderId . '" />';
		$this->mPage .= '<strong>Staff Notes: </strong><input type="text" name="staffNotes" id="staffNotes" value="'.$order->GetStaffNotes().'" class="staffNotesInput" />';
		$this->mPage .= '<input type="submit" value="Update Staff Notes" />';
		$this->mPage .= '</form>';
		$this->mPage .= '</div>';
		$this->mPage .= $orderView->LoadDefault ( $orderId, true );
		return $this->mPage;
	}

}

$page = new OrdersEditView ( );
$page->IncludeCss ( 'base.css.php', true, false, true );
$page->IncludeCss ( 'admin.css.php', true, false, true );
$page->IncludeCss ( 'adminPrint.css.php', true, 'print', true );
$page->IncludeCss ( 'StaffNotes.css.php', true, false, true );

if (isset ( $_GET ['id'] )) {
	try {
		echo $page->LoadDefault ( $_GET ['id'] );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
}

?>