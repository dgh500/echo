<?php
class InternalOrderView extends AdminView {
	
	var $mOrder;

	function __construct() {
		parent::__construct(false,false,false,false);	
		$this->mAdminPath = $this->mRegistry->adminDir;
	}

	function LoadDefault($orderId) {
		$this->IncludeCss('PublicOrderView.css.php');
		$this->mOrderItemController = new OrderItemController;
		$this->mOrder = new OrderModel($orderId);
		$this->mTimeHelper = new TimeHelper();
		$this->mPresentationHelper = new PresentationHelper();
		$this->mPage .= '<div style="font-family: Arial, Sans-Serif; font-size: 10pt;">';
		$this->LoadDeepBlueSection();
		if(!$this->mOrder->GetStatus()->IsFailed()) {
			$this->LoadOrderDetails();
		}
		$this->LoadBasketDetails();
		$this->LoadMisc ();
		if($this->mOrder->GetStatus()->IsInTransit()) {
			$this->LoadAddresses();
		} else {
			$this->LoadContactDetails();	
		}
		$this->mPage .= '</div>';
		return $this->mPage;
	}

	function LoadOrderDetails() {

		// If the order has been shipped, make the order shipped section
		if(NULL != $this->mOrder->GetStatus()->IsInTransit()) {
			$orderShipped = '<strong class="indent">Order Shipped:</strong> '.$this->mTimeHelper->FriendlyDateTime($this->mOrder->GetShippedDate()).'<br style="clear: both;" />';
		} else {
			$orderShipped = '';
		}

		$this->mPage .= <<<EOT
			<strong class="indent">Order No:</strong> ECHO{$this->mOrder->GetOrderId()}<br style="clear: both;" />
			<strong class="indent">Order Date:</strong> {$this->mTimeHelper->FriendlyDateTime($this->mOrder->GetCreatedDate())}<br style="clear: both;" />
			{$orderShipped}
			<strong class="indent">Status: </strong>{$this->mOrder->GetStatus()->GetDescription()}<br style="clear: both;" />
			<strong class="indent">Delivery Method: </strong>{$this->mOrder->GetPostageMethod()->GetDisplayName()}<br style="clear: both;" />
			<strong class="indent">Weight: </strong>{$this->mOrder->GetBasket()->GetWeight()}g<br style="clear: both;" />
			<strong class="indent">Courier: </strong>{$this->mOrder->GetCourier()->GetDisplayName()}<br style="clear: both;" />
			<strong class="indent">Dispatch Estimate: </strong>{$this->mOrder->GetDispatchDate()->GetDisplayName()}<br style="clear: both;" />
EOT;
			$this->mPage .= (trim ( $this->mOrder->GetTrackingNumber () ) ? $this->mOrder->GetTrackingNumber () . '<br />' : '');
	} // End LoadOrderDetails
	

	function LoadDeepBlueSection() {
		$this->mPage .= <<<EOT
			<div id="publicOrderView">
			<h1>Sales Order - ECHO{$this->mOrder->GetOrderId()} - {$this->mOrder->GetStatus()->GetDescription()}</h1>
EOT;
	}

	//! Loads the contact details needed (eg. email, telephone no, name etc.) needed for non-in-transit orders
	function LoadContactDetails() {		
		// Phone numbers and email
		$this->mPage .= '<strong>Contact Details</strong><br />';
			$this->mPage .= '<label><strong>Customer Name: </strong></label>';
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetTitle()) 	? ucwords($this->mOrder->GetCustomer()->GetTitle()).' '		: '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetFirstName())	? ucwords($this->mOrder->GetCustomer()->GetFirstName()).' ' : '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetLastName()) 	? ucwords($this->mOrder->GetCustomer()->GetLastName()) 		: '');
		$this->mPage .= '<br />';
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetDaytimeTelephone()) ? '<label><strong>Daytime Tel: </strong></label>'.$this->mOrder->GetCustomer()->GetDaytimeTelephone().'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetMobileTelephone()) ? '<label><strong>Mobile Tel: </strong></label>'.$this->mOrder->GetCustomer ()->GetMobileTelephone () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetEmail()) ? '<label><strong>Email: </strong></label>'.$this->mOrder->GetCustomer()->GetEmail().'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress()->GetCountry()->GetDescription()) ? '<label><strong>Country: </strong></label>'.$this->mOrder->GetShippingAddress()->GetCountry()->GetDescription().'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine1 () ) ? '<strong>Address:</strong> '.$this->mOrder->GetShippingAddress ()->GetLine1 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetPostcode () ) ? '<strong>Postcode:</strong> '.$this->mOrder->GetShippingAddress ()->GetPostcode () . '<br />' : '');
		$this->mPage .= '<br />';
	}
	
	//! Loads Staff Name, Brochure and Notes section
	function LoadMisc() {
		$this->mPage .= (trim ( $this->mOrder->GetStaffName () ) ? '<strong>Staff</strong>: ' . $this->mOrder->GetStaffName () . '' : '<strong>Staff</strong>: Web Order');
		$this->mPage .= ($this->mOrder->GetBrochure () ? ' | <strong>Brochure</strong>: Yes' : ' | <strong>Brochure</strong>: No');
		$this->mPage .= (trim ( $this->mOrder->GetNotes () ) ? ' | <strong>Notes</strong>: ' . $this->mValidationHelper->MakeSafe ( $this->mOrder->GetNotes () ) : ' | <strong>Notes</strong>: None');
		$this->mPage .= ($this->mOrder->GetCustomer()->GetFirstOrder() ? ' | <strong>First Order</strong>: Yes' : ' | <strong>First Order</strong>: No');		
		$this->mPage .= '<br /><br />';
	} // End LoadMisc


	function LoadAddresses() {
		$this->mPage .= <<<EOT
			<strong>Billing Details</strong><br />
EOT;
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine1 () ) ? $this->mOrder->GetBillingAddress ()->GetLine1 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine2 () ) ? $this->mOrder->GetBillingAddress ()->GetLine2 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine3 () ) ? $this->mOrder->GetBillingAddress ()->GetLine3 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetCounty () ) ? $this->mOrder->GetBillingAddress ()->GetCounty () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetPostcode () ) ? $this->mOrder->GetBillingAddress ()->GetPostcode () . '<br />' : '');
		
		$this->mPage .= '<br /><strong>Shipping Details</strong><br />';
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetDaytimeTelephone()) ? '<strong>Daytime Tel</strong>: '.$this->mOrder->GetCustomer()->GetDaytimeTelephone().'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetMobileTelephone()) ? '<strong>Mobile Tel</strong>: '.$this->mOrder->GetCustomer ()->GetMobileTelephone () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetEmail()) ? '<strong>Email: </strong>'.$this->mOrder->GetCustomer ()->GetEmail () . '<br />' : '');
		$this->mPage .= '<br />';
		
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetTitle () ) ? $this->mOrder->GetCustomer ()->GetTitle () . ' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetFirstName () ) ? $this->mOrder->GetCustomer ()->GetFirstName () . ' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetLastName () ) ? $this->mOrder->GetCustomer ()->GetLastName () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine1 () ) ? $this->mOrder->GetShippingAddress ()->GetLine1 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine2 () ) ? $this->mOrder->GetShippingAddress ()->GetLine2 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine3 () ) ? $this->mOrder->GetShippingAddress ()->GetLine3 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCounty () ) ? $this->mOrder->GetShippingAddress ()->GetCounty () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetPostcode () ) ? $this->mOrder->GetShippingAddress ()->GetPostcode () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () ) ? $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () . '<br />' : '');
		$this->mPage .= '<br />ECHO' . $this->mOrder->GetOrderId ();
		$this->mPage .= '</div>';
	} // End LoadAddresses

	//! Loads the table displaying the basket for the order form
	function LoadBasketDetails() {
		// Display the first row of the table with headings
		$this->mPage .= <<<EOT
			<table>
				<tr>
					<th class="left">Product</th><th>Price</th><th>Shipped</th>
				</tr>
EOT;
		// Get the packages & loop over them
		foreach($this->mOrderItemController->GetPackagesForOrder($this->mOrder) as $packageItem) {
			
			// Display the row
			$this->mPage .= '<tr>';
			$this->mPage .= '<td id="productColumn"><strong>'.$packageItem->GetDisplayName().'</strong><br />';
			
			// Get the contents of this package
			foreach($this->mOrderItemController->GetContentsOfPackageItem($packageItem) as $packageProductItem) {
				$this->mPage .= '- '.$packageProductItem->GetDisplayName().'<br />';
			}
			// Upgrades
			foreach($this->mOrderItemController->GetUpgradesOfPackageItem($packageItem) as $packageUpgradeItem) {
				$this->mPage .= '- <strong>Upgrade</strong>: '.$packageUpgradeItem->GetDisplayName().'<br />';
			}
			
			$this->mPage .= '</td>';
			$this->mPage .= '<td id="unitPriceColumn">&pound;'.$packageItem->GetPackagePrice().'</td>';
			$this->mPage .= '<td id="shippedColumn">'.$this->LoadShippingSection($packageItem).'</td>';
			
			$this->mPage .= '</tr>';
		} // End Looping Over Packages
		
		// Get the products and loop over them
		foreach($this->mOrderItemController->GetProductsForOrder($this->mOrder) as $productItem) {
			$this->mPage .= '<tr><td>'.$productItem->GetDisplayName().'</td>';
			$this->mPage .= '<td id="unitPriceColumn">&pound;'.$productItem->GetPrice().'</td>';
			$this->mPage .= '<td id="shippedColumn">'.$this->LoadShippingSection($productItem).'</td>';
			$this->mPage .= '</tr>';
		}
		
		// Absolute total - The total displayed as Sub-Total + Postage
		$absTotal = number_format ( $this->mOrder->GetTotalPrice () + $this->mOrder->GetTotalPostage (), 2 );
		
		// If the order is in transit, we can display the total actually taken
		if($this->mOrder->GetStatus()->IsInTransit()) {
			$totalTakenHeading 	= '<br /><strong>Total Actually Taken:</strong>';	
			$totalTaken			= '<br /><strong>&pound;'.$this->mPresentationHelper->Money($this->mOrder->GetActualTaken()).'</strong>';
		} else {
			$totalTakenHeading  = '';
			$totalTaken			= '';
		}
		
		// Display the bottom (totals) row of the table
		$this->mPage .= <<<EOT
				<tr>
					<td class="right"><strong>Sub-total:</strong><br /><strong>Delivery:</strong><br /><strong>Total:</strong>{$totalTakenHeading}</td>
					<td class="right">	&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPrice())}<br />
										&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPostage())}<br />
										&pound;{$absTotal}
										{$totalTaken}
										</td>
					<td></td>
				</tr>
			</table>
EOT;
	} // End LoadBasketDetails


	function LoadShippingSection($orderItem) {
		// Make shipped section - Whether to say Yes/No for each product item 
		// If cancelled then always 'No'. If a product item isnt shipped, then if the order is in transit it says 'No' otherwise it displays a checkbox (assumed Authorised)
		if ($this->mOrder->GetStatus()->IsCancelled() || $this->mOrder->GetStatus()->IsFailed()) {
			$shippedSection = 'No';
		} else {
			if (!$orderItem->GetShipped()) {
				$shippedSection = 'No';
			} else {
				$shippedSection = 'Yes';
			}
		}
		return $shippedSection;
	} // End LoadShippingSection

} // End PublicOrderEditView

?>