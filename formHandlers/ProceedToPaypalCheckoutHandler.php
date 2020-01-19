<?php

include ('../autoload.php');

//! Performs any actions needed on the basket, and takes the user to the checkout stage
class ProceedToPaypalCheckoutHandler {

	//! The request to send to paypal checkout
	var $mRequest;

	//! Initialises validation, session helpers and the basket
	function __construct($postArr) {
		$registry = Registry::getInstance();
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mOrderController		= new OrderController;
		$this->mMoneyHelper			= new MoneyHelper;
		$this->mBasket 				= new BasketModel($postArr['basket_id']);
		$this->mPostageTotal		= $postArr['postagePrice'];
		$this->mRequest				= '';
		$this->mRequestUrl			= $registry->PaypalCheckoutUrl;
	}

	//! Creates an XML request for  checkout
	function CreateRequest() {
		$registry = Registry::getInstance();
		// Set request-specific fields.
		$paymentAmount 	= urlencode($this->mBasket->GetTotal());
		if($paymentAmount < 45) {
			$paymentAmount = $paymentAmount + $this->mPostageTotal;
		}
		$currencyID 	= urlencode('GBP');						// or other currency code ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		$paymentType 	= urlencode('Sale');					// or 'Sale' or 'Order'

		$returnURL = urlencode($registry->baseDir."/paypalOrderReceipt.php?amount=".$paymentAmount);
		$cancelURL = urlencode($registry->baseDir."/basket");

		// Add request-specific fields to the request string.
		$this->mRequest = 'VERSION='.urlencode('56.0');
		$this->mRequest .= '&SIGNATURE='.urlencode($registry->paypalApiSignature);
		$this->mRequest .= '&USER='.urlencode($registry->paypalApiUsername);
		$this->mRequest .= '&PWD='.urlencode($registry->paypalApiPassword);
		$this->mRequest .= "&METHOD=SetExpressCheckout";
		$this->mRequest .= "&AMT=$paymentAmount&RETURNURL=$returnURL&CANCELURL=$cancelURL&PAYMENTACTION=$paymentType&CURRENCYCODE=$currencyID";

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
				#	$price = $this->mMoneyHelper->RemoveVAT($this->mBasket->GetOverruledSkuPrice($sku,false,true));
					$price = $this->mBasket->GetOverruledSkuPrice($sku,false,true);
				}
			}
			$itemTotal += $price;

			// Insert a 3-5 day notice
			if($sku->GetParentProduct()->IsNonStockProduct() || $sku->GetQty() ==0) {
				$notice = '3-5 Day Dispatch: ';
			} else {
				$notice = '';
			}

			$this->mRequest .= "&L_NAME$i=".urlencode(htmlspecialchars($notice)).urlencode(htmlspecialchars($sku->GetParentProduct()->GetDisplayName(),ENT_QUOTES,"UTF-8"));
			$this->mRequest .= "&L_NUMBER$i=".urlencode($sku->GetSkuId());
			$this->mRequest .= "&L_DESC$i=".urlencode(htmlspecialchars($sku->GetSkuAttributesList(),ENT_QUOTES,"UTF-8"));
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
		$this->mRequest .= "&SHIPPINGAMT=".$this->mPostageTotal;

		// Extras
		$this->mRequest .= "&REQCONFIRMSHIPPING=1&ALLOWNOTE=1&PAGESTYLE=EchoSupps";
	#	print_r($this->mRequest); echo '<br><br>';die();
	} // End CreateRequest

	//! Do the actual request
	function PostRequest() {
		$registry = Registry::getInstance();
		$paypalCheckout = new PaypalCheckoutHelper;
		// Execute the API operation; see the PPHttpPost function above.
		#$httpParsedResponseAr = $paypalCheckout->PPHttpPost('SetExpressCheckout', $this->mRequest);
	#	var_dump($this->mRequest); die();
		$this->mResult = $this->mOrderController->SendPaypalOrderRequest($this->mRequest,$registry->paypalPaymentProcessingUrl);
		parse_str(urldecode($this->mResult),$this->mResultArr);
	#	var_dump($this->mResultArr); die();

		if("SUCCESS" == strtoupper($this->mResultArr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->mResultArr["ACK"])) {
			// Redirect to paypal.com.
			$token = urldecode($this->mResultArr['TOKEN']);
			if($registry->PaypalCheckoutLive) {
				$payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";
			} else {
				$payPalURL = "https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=$token";
			}
			header("Location: $payPalURL");
			exit;
		} else  {
			die("We have experienced a system error - please tell us on info@echosupplements.com - thanks!");
			#print_r($this->mResultArr);die();
		}
	} // End PostRequest

} // End ProceedToPaypalCheckoutHandler

try {
	$handler = new ProceedToPaypalCheckoutHandler($_POST);
	$handler->CreateRequest();
	$handler->PostRequest();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>