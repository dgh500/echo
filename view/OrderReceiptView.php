<?php
if (isset ( $_GET ['orderId'] )) {
	include_once ('../autoload.php');
}

//! Loads the receipt that is sent to the customer with each order
class AdminOrderRecieptView extends AdminView {

	//! The order the receipt is for
	var $mOrder;

	function __construct() {
		parent::__construct (false,false,false,true);
		$registry = Registry::getInstance ();
		$this->mLocalMode = $registry->localMode;
		$this->mMoneyHelper = new MoneyHelper;
		$this->mOrderItemController = new OrderItemController;
		if ($this->mLocalMode) {
			$this->mOrderPrefix = 'ECHO';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}
	}

	function LoadDefault($orderId) {
		$this->IncludeCss('css/OrderReceiptView.css.php',false,false,true);
		$this->IncludeCss('adminPrint.css.php',true,'print',true);
		$this->mOrder = new OrderModel ( $orderId );
		$this->OpenReceipt();
			$this->LoadHeader();
			$this->LoadBasketDetails();
		$this->CloseReceipt();
	#	$this->LoadAddress();
		return $this->mPage;
	}

	function LoadAddress() {
		// Shipping Address
		$this->mPage .= '<div style="font-family: Arial; font-size: 10pt; page-break-before: always">';
		$this->mPage .= '<strong>';
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetTitle()) 		? ucwords($this->mOrder->GetCustomer()->GetTitle()).' ' 	: '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetFirstName()) 	? ucwords($this->mOrder->GetCustomer()->GetFirstName()).' ' : '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetLastName()) 		? ucwords($this->mOrder->GetCustomer()->GetLastName()) 			: '');
		$this->mPage .= '</strong>';
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetCompany () ) ? '<br />'.ucwords($this->mOrder->GetShippingAddress()->GetCompany()).'<br />' : '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetLine1()) 	? '<br />'.ucwords($this->mOrder->GetShippingAddress()->GetLine1()).'<br />' 	: '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetLine2()) 	? ucwords($this->mOrder->GetShippingAddress()->GetLine2()).'<br />' 			: '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetLine3()) 	? ucwords($this->mOrder->GetShippingAddress()->GetLine3()).'<br />' 			: '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetCounty())	? ucwords($this->mOrder->GetShippingAddress()->GetCounty()).'<br />' 			: '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetPostcode()) 	? strtoupper($this->mOrder->GetShippingAddress()->GetPostcode()).'<br />' 	: '');
		$this->mPage .= (trim($this->mOrder->GetShippingAddress()->GetCountry()->GetDescription()) ? $this->mOrder->GetShippingAddress()->GetCountry()->GetDescription().'<br />' : '');
		$this->mPage .= '</div>';
	}

	function OpenReceipt() {
		$this->mPage.= '<div id="salesReceipt"><div>';
	}

	function CloseReceipt() {
		$this->mPage.= '</div></div><br />';
	}

	function LoadHeader() {
		$this->mPage .= '<h1 id="companyName">'.$this->mOrder->GetCatalogue()->GetDisplayName().'</h1>';
	//	$this->mPage .= '<div id="vatNo">VAT No. xxx</div>';
		$this->mPage .= '<h3 id="companyAddress">Echo Supplements - 919 Yeovil Road, Slough, Berkshire, SL1 4NH</h3>';
		$this->mPage .= '<h2 id="salesHeading">Sales Receipt - Order ECHO'.$this->mOrder->GetOrderId().' - '.date('D jS M Y',$this->mOrder->GetShippedDate()).'</h2>';
	}

	//! Loads the table displaying the basket for the order form
	function LoadBasketDetails() {
		$this->mCustomer = $this->mOrder->GetCustomer();
		$this->mAddress	= $this->mOrder->GetShippingAddress();
		// Address
		$address = '';
		(trim($this->mAddress->GetCompany()) 	? $address .= ucwords($this->mAddress->GetCompany()).', ' 	: $address .= '');
		(trim($this->mAddress->GetLine1()) 		? $address .= ucwords($this->mAddress->GetLine1()).', ' 	: $address .= '');
		(trim($this->mAddress->GetLine2()) 		? $address .= ucwords($this->mAddress->GetLine2()).', ' 	: $address .= '');
		(trim($this->mAddress->GetLine3()) 		? $address .= ucwords($this->mAddress->GetLine3()).', ' 	: $address .= '');
		(trim($this->mAddress->GetCounty()) 	? $address .= ucwords($this->mAddress->GetCounty()).', ' 	: $address .= '');
		(trim($this->mAddress->GetPostCode()) 	? $address .= ucwords($this->mAddress->GetPostCode()).', ' 	: $address .= '');
		(trim($this->mAddress->GetCountry()->GetDescription()) ? $address .= ucwords($this->mAddress->GetCountry()->GetDescription()) : $address .= '');
		// Customer Name
		$customerName = '';
		($this->mCustomer->GetTitle() 		? $customerName .= ucwords($this->mCustomer->GetTitle()).' ' 		: $customerName .= '');
		($this->mCustomer->GetFirstName() 	? $customerName .= ucwords($this->mCustomer->GetFirstName()).' ' 	: $customerName .= '');
		($this->mCustomer->GetLastName() 	? $customerName .= ucwords($this->mCustomer->GetLastName()).' ' 	: $customerName .= '');

		// Display the first row of the table with headings
		$this->mPage .= <<<EOT
			<table>
				<tr>
					<td colspan="2">
					<strong>{$customerName}</strong><br />
					{$address}
					</td>
				</tr>
    	<tr>
        	<td id="productColHeading">Product</td>
            <td id="priceColHeading">Price</td>
        </tr>
EOT;
		// Get the packages & loop over them
		foreach($this->mOrderItemController->GetPackagesForOrder($this->mOrder,true) as $packageItem) {

			// Display the row
			$this->mPage .= '<tr>';
			$this->mPage .= '<td id="productCol"><strong>'.$packageItem->GetDisplayName().'</strong><br />';

			// Get the contents of this package
			foreach($this->mOrderItemController->GetContentsOfPackageItem($packageItem,true) as $packageProductItem) {
				$this->mPage .= '- '.$packageProductItem->GetDisplayName().'<br />';
			}
			// Upgrades
			foreach($this->mOrderItemController->GetUpgradesOfPackageItem($packageItem,true) as $packageUpgradeItem) {
				$this->mPage .= '- <strong>Upgrade</strong>: '.$packageUpgradeItem->GetDisplayName().'<br />';
			}

			$this->mPage .= '</td>';
			$this->mPage .= '<td id="priceCol">&pound;'.$packageItem->GetPackagePrice().'</td>';
			$this->mPage .= '</tr>';
		} // End Looping Over Packages

		// Get the products and loop over them
		foreach($this->mOrderItemController->GetProductsForOrder($this->mOrder,true) as $productItem) {
			$this->mPage .= '<tr><td id="productCol">'.$productItem->GetDisplayName().'</td>';
			$this->mPage .= '<td id="priceCol">&pound;'.$productItem->GetPrice().'</td>';
			$this->mPage .= '</tr>';
		}

		//! Absolute total - The total displayed as Sub-Total + Postage
		$absTotal = number_format ( $this->mOrderItemController->GetTotalShippedPrice($this->mOrder) + $this->mOrder->GetTotalPostage (), 2 );

		//! VAT Total
		$vat = $this->mOrderItemController->GetTotalShippedPrice($this->mOrder) - ($this->mOrderItemController->GetTotalShippedPrice($this->mOrder) / 1.2);

		// Display the bottom (totals) row of the table
		$this->mPage .= <<<EOT
				<tr>
					<td id="totalColHeading"><strong>Sub-Total:</strong><br /><strong>VAT:</strong><br /><strong>Delivery:</strong><br /><strong>Total:</strong></td>
					<td id="totalCol">	&pound;{$this->mPresentationHelper->Money($this->mOrderItemController->GetTotalShippedPrice($this->mOrder))}<br />
										&pound;{$this->mPresentationHelper->Money($vat)}<br />
										&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPostage())}<br />
										&pound;{$absTotal}</td>
				</tr>
			</table>
	<h3 id="thankYou">Thank you for shopping at {$this->mOrder->GetCatalogue()->GetDisplayName()}!</h3>
EOT;
	if(!$this->mOrderItemController->WholeOrderShipped($this->mOrder)) {
		$this->mPage .= '
		<p id="notShipped">
		Any items not listed above <strong>have not</strong> been shipped, and will not be shipped - if these items are still required they must be re-ordered. <br /><br />
		<strong>You have not been charged for any items not shipped.</strong>
    </p>';
	}


	} // End LoadBasketDetails

} // End PublicOrderEditView


if (isset ( $_GET ['orderId'] )) {
	$page = new AdminOrderRecieptView ( );
	echo $page->LoadDefault ( $_GET ['orderId'] );
}

?>