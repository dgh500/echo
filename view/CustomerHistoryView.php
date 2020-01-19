<?php

include_once('../autoload.php');

//! Defines the view for the customer history of the admin area
class CustomerHistoryView extends AdminView {

	function __construct($customerId) {
		parent::__construct('Customer History',false,false,false);
		$this->IncludeCss('AdminIndexView.css.php');
		$this->mCustomer = new CustomerModel($customerId);
		$this->mCustomerController = new CustomerController;
	}

	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->LoadHistory();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}

	function LoadHistory() {
		// Work things out
		$orders = $this->mCustomerController->GetOrders($this->mCustomer);
		$orderCount = $this->mCustomer->GetOrderCount();
		// Display Customer Details
		$this->mPage .= '<h2>'.$this->mCustomer->GetTitle().' '.$this->mCustomer->GetFirstName().' '.$this->mCustomer->GetLastName().'</h2>';
		$this->mPage .= '<b>Previous Shipped Orders: </b>'.$orderCount;
		$this->mPage .= '<h2>Previous Orders (Most Recent First)</h2>';
		foreach($orders as $order) {
			if($order->GetStaffName()) {
				$orderMethod = 'PHONE';
			} else {
				$orderMethod = 'WEB';
			}
			$this->mPage .= '<b>ECHO'.$order->GetOrderId().'</b>';
			$this->mPage .= '<br /><i>Ordered on:</i> '.date('D jS F Y',$order->GetCreatedDate());
			$this->mPage .= '<br /><i>Billing Address:</i> '.$order->GetBillingAddress()->GetLine1().'...'.$order->GetBillingAddress()->GetPostcode();
			$this->mPage .= '<br /><i>Delivery Address:</i> '.$order->GetShippingAddress()->GetLine1().'...'.$order->GetShippingAddress()->GetPostcode();
			$this->mPage .= '<br /><i>Ordered By:</i> '.$orderMethod;
			$this->mPage .= '<br /><i>Ordered Value:</i> &pound;'.$order->GetTotalPrice();
			$this->mPage .= '<hr />';
		}
	}

} // End CustomerHistory View

$page = new CustomerHistoryView ($_GET['id'] );
echo $page->LoadDefault ();

?>