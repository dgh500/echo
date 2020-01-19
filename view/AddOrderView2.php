<?php
require_once ('../autoload.php');

//! New, AJAX-Based Telephone Order Form
class AddOrderView2 extends AdminView {

	function __construct() {
		// Session Start
		$this->mSession 	= new SessionHelper;
		$customerController = new CustomerController;
		$addressController 	= new AddressController;

		$cssIncludes = array('jquery.tooltip.css','jquery.alerts.css.php','AddOrderView2.css.php','BasketMacFinder.css.php');
		$jsIncludes  = array('jqueryUi.js','jquery.tooltip.min.js','jquery.alerts.js','AddOrderView2.js');

		// Construct
		parent::__construct('Add Order',$cssIncludes,$jsIncludes);

		// Create a customer, addresses etc. on first load
		if(!isset($_SESSION['init'])) {
			// Create customer
			$customer = $customerController->CreateCustomer();

			// Create the addresses
			$billingAddress 	= $addressController->CreateAddress();
			$deliveryAddress 	= $addressController->CreateAddress();

			// Store in the session
			$_SESSION['init'] = true;
			$_SESSION['customerId'] = $customer->GetCustomerId();
			$_SESSION['billingId'] 	= $billingAddress->GetAddressId();
			$_SESSION['deliveryId'] = $deliveryAddress->GetAddressId();
		}

		// Catalogue Init
		(isset($_SESSION['catalogueId']) ? $this->mCatalogueId = $_SESSION['catalogueId'] : $this->mCatalogueId = 1 );

		// Create an order

	} // End __construct()

	//! Default Load Function
	function LoadDefault($tabToLoad='customer') {
			if($tabToLoad == 'basketLoad') {
				$this->mPage .= '<input type="hidden" id="basketLoad" value="1" />';
			} else {
				$this->mPage .= '<input type="hidden" id="basketLoad" value="0" />';
			}
			$this->mPage .= '
	<div id="addOrderTabContainer">
		<form>
			<input type="hidden" name="catalogueId" id="catalogueId" value="'.$this->mCatalogueId.'" />
			<input type="hidden" name="basketEmpty" id="basketEmpty" value="0" />
			</form>
		<ul>
			<li id="customerTabItem"><a href="'.$this->mBaseDir.'/view/AddOrderCustomerTabView.php" title="customerTab" id="customerTabLink"><span>Customer</span></a></li>
			<li id="basketTabItem">	 <a href="'.$this->mBaseDir.'/view/AddOrderBasketTabView.php" title="basketTab" id="basketTabLink"><span>Basket</span></a></li>
			<li id="billingTabItem"> <a href="'.$this->mBaseDir.'/view/AddOrderBillingTabView.php" title="billingTab" id="billingTabLink"><span>Billing</span></a></li>
		</ul>
		<div id="basketTab">
		</div>
		<div id="billingTab">
		</div>
	<div id="loading"><img src="'.$this->mBaseDir.'/wombat7/images/ajaxLoading.gif" /></loading>
	</div>
	<div id="completeOrder" style="display: none;">
		<input type="image" src="'.$this->mBaseDir.'/wombat7/images/completeOrderBar.gif" id="completeOrderButton" />
	</div>
	<div id="processingOrderLoading" style="display: none; text-align: center; border: 0px solid #000; margin-bottom: 40px;">
		<h3>Order Being Taken - Please Wait</h3>
		<img src="'.$this->mBaseDir.'/wombat7/images/processingOrderLoadingBar.gif" />
	</div>
	';
	return $this->mPage;
	} // End LoadDefault


} // End AddOrderView2

$page = new AddOrderView2;
if(isset($_GET['b'])) {
	echo $page->LoadDefault('basketLoad');
} else {
	echo $page->LoadDefault();
}
?>