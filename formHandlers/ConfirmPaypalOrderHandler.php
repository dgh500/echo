<?php

include ('../autoload.php');

//! Tells paypal to take their money!
class ConfirmPaypalOrderHandler {

	//! The request to send to google checkout
	var $mRequest;

	//! Initialises validation, session helpers and the basket
	function __construct($postArr) {
		$registry = Registry::getInstance();
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mMoneyHelper			= new MoneyHelper;
		$this->mRequest				= '';
		$this->mRequestUrl			= $registry->PaypalCheckoutUrl;
		$this->mToken				= $postArr['tokenId'];
		$this->mPayerId				= $postArr['payerId'];
		$this->mAmount				= $postArr['amount'];
		$this->mOrderController		= new OrderController;
	}

	//! Creates an VNP request for paypal checkout
	function CreateRequest() {
		$registry = Registry::getInstance();
		$sHelper = new SessionHelper;
		$this->mBasket = $sHelper->GetBasket();

		// Set request-specific fields.
		$token 			= urlencode($this->mToken);
		$payerId		= urlencode($this->mPayerId);
		$amount 		= urlencode($this->mAmount);
		$currency 		= urlencode('GBP');							// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

		// Add request-specific fields to the request string.
		$this->mRequest = 'VERSION='.urlencode('56.0');
		$this->mRequest .= '&SIGNATURE='.urlencode($registry->paypalApiSignature);
		$this->mRequest .= '&USER='.urlencode($registry->paypalApiUsername);
		$this->mRequest .= '&PWD='.urlencode($registry->paypalApiPassword);
		$this->mRequest .= "&METHOD=DoExpressCheckoutPayment";
		$this->mRequest .= "&TOKEN=$token&PAYERID=$payerId&PAYMENTACTION=Sale&AMT=$amount&CURRENCYCODE=$currency";

		// Products (not Stacks)
		$i=0;
		$itemTotal=0;
		foreach($this->mBasket->GetSkus() as $sku) {
			// Handle VAT issues
			if($sku->GetParentProduct()->GetTaxCode()->GetRate()==0) { $taxable = '0'; } else { $taxable = '1'; }
			if($sku->GetParentProduct()->GetTaxCode()->GetRate()==0) {
				// Handle Multibuys
				$price = $this->mBasket->GetOverruledSkuPrice($sku,false,false);
				// Check for upgrade cause nowt is free!
				if($price == 0) {
					$price = $this->mBasket->GetOverruledSkuPrice($sku,false,true);
				}
			} else {
				// Take the VAT off
				$price = $this->mBasket->GetOverruledSkuPrice($sku,false,false);
				if($price == 0) {
					$price = $this->mMoneyHelper->RemoveVAT($this->mBasket->GetOverruledSkuPrice($sku,false,true));
				}
			}
			$itemTotal += $price;

			// Insert a 3-5 day notice
			if($sku->GetParentProduct()->IsNonStockProduct() || $sku->GetQty() ==0) {
				$notice = '3-5 Day Dispatch: ';
			} else {
				$notice = '';
			}

			$attrList = urlencode(htmlspecialchars($sku->GetSkuAttributesList(),ENT_QUOTES,"UTF-8"));
			$this->mRequest .= "&L_NAME$i=".urlencode(htmlspecialchars($notice)).urlencode(htmlspecialchars($sku->GetParentProduct()->GetDisplayName(),ENT_QUOTES,"UTF-8")).' '.$attrList;
			$this->mRequest .= "&L_NUMBER$i=".urlencode($sku->GetSkuId());
			$this->mRequest .= "&L_DESC$i=".$attrList;
			$this->mRequest .= "&L_AMT$i=".urlencode(htmlspecialchars($price));
			$this->mRequest .= "&L_QTY$i=1";
			$i++;
		}
		// Get package SKUs in basket
		$packageSkus = $this->mBasket->GetSkus(false, false, true, true);
		// Stacks/Packages
		foreach($this->mBasket->GetPackages() as $package) {
			// Loop over the $packageSkus, if the parent product of the SKU is in the package then add the SKU attributes to the description
			$skusDescription = ' ';
			foreach($packageSkus as $packageSku) {
				if($package->IsPart($packageSku->GetParentProduct())) {
					$skusDescription .= ' '.$packageSku->GetSkuAttributesList();
				}
			}

			$price = $package->GetActualPrice();
			$itemTotal += $price;
			$this->mRequest .= "&L_NAME$i=".urlencode(htmlspecialchars($package->GetDisplayName(),ENT_QUOTES)).urlencode(htmlspecialchars($skusDescription));
			$this->mRequest .= "&L_NUMBER$i=".urlencode($package->GetPackageId());
			$this->mRequest .= "&L_DESC$i=".urlencode(htmlspecialchars($skusDescription,ENT_QUOTES,"UTF-8"));
			$this->mRequest .= "&L_AMT$i=".urlencode(htmlspecialchars($price));
			$this->mRequest .= "&L_QTY$i=1";
			$i++;
		}

		// Totals
		$this->mRequest .= "&ITEMAMT=".$itemTotal;
		$shippingAmount = $this->mAmount - $itemTotal;
		if($this->mBasket->GetTotal() >= 45) {
			$this->mRequest .= "&SHIPPINGAMT=0.00";
		} else {
			$this->mRequest .= "&SHIPPINGAMT=".$shippingAmount;
		}

		// Extras
		$this->mRequest .= "&REQCONFIRMSHIPPING=1&ALLOWNOTE=1&PAGESTYLE=EchoSupps";

#		var_dump($this->mRequest); die();

	} // End CreateRequest

	//! Do the actual request
	function PostRequest() {
		$registry = Registry::getInstance();
		$paypalCheckout = new PaypalCheckoutHelper;
		// Execute the API operation; see the PPHttpPost function above.
		$this->mResult = $this->mOrderController->SendPaypalOrderRequest($this->mRequest,$registry->paypalPaymentProcessingUrl);
		parse_str(urldecode($this->mResult),$this->mResultArr);
	#	var_dump($this->mResultArr);die();
		$registry = Registry::getInstance();
		if("SUCCESS" == strtoupper($this->mResultArr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->mResultArr["ACK"])) {
			// Update Stock
			$this->mBasket->UpdateStockLevels();
			// And Redirect
			header("Location: ".$registry->baseDir."/paypalCheckoutComplete.php?result=success");
		} else  {
	#		print_r($this->mResultArr);die();
			header("Location: ".$registry->baseDir."/paypalCheckoutComplete.php?result=fail");
		}
	} // End PostRequest
	/*
	DoExpressCheckoutPayment Completed Successfully: Array (
	[TOKEN] => EC%2d74V1275815430192U
	[TIMESTAMP] => 2010%2d05%2d29T19%3a08%3a47Z
	[CORRELATIONID] => ae0d0bb34fa66
	[ACK] => Success
	[VERSION] => 51%2e0
	[BUILD] => 1322101
	[TRANSACTIONID] => 1T146417U3663770V
	[TRANSACTIONTYPE] => expresscheckout
	[PAYMENTTYPE] => instant
	[ORDERTIME] => 2010%2d05%2d29T19%3a08%3a45Z
	[AMT] => 40%2e99
	[TAXAMT] => 0%2e00
	[CURRENCYCODE] => GBP
	[PAYMENTSTATUS] => Pending
	[PENDINGREASON] => authorization
	[REASONCODE] => None ) */

} // End ConfirmPaypalOrderHandler

try {
	$handler = new ConfirmPaypalOrderHandler($_POST);
	$handler->CreateRequest();
	$handler->PostRequest();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>