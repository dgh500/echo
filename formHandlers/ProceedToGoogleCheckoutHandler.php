<?php

include ('../autoload.php');
include('GoogleCheckoutLibrary/xml-processing/gc_xmlparser.php');
define('ENTER', "\r\n");
define('DOUBLE_ENTER', ENTER.ENTER);

//! Performs any actions needed on the basket, and takes the user to the checkout stage
class ProceedToGoogleCheckoutHandler {

	//! The request to send to google checkout
	var $mRequest;

	//! Initialises validation, session helpers and the basket
	function __construct($postArr) {
		$registry = Registry::getInstance();
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mMoneyHelper			= new MoneyHelper;
		$this->mBasket 				= new BasketModel($postArr['basket_id']);
		$this->mPostageCost			= $postArr['postagePrice'];
		$this->mPostageText			= $postArr['postageText'];
		$this->mPostageId			= $postArr['postageId'];
		$this->mRequest				= '';
		$this->mRequestUrl			= $registry->GoogleCheckoutUrl;
		$this->mMerchantId			= $registry->GoogleCheckoutMerchantId;
		$this->mMerchantKey			= $registry->GoogleCheckoutMerchantKey;
	}

	//! Creates an XML request for google checkout
	function CreateRequest() {
		$this->mRequest .= '<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">';

		// Basket
		$this->mRequest .= 	'<shopping-cart>';
		$this->mRequest .= 		'<items>';
		// Products (not Stacks)
		foreach($this->mBasket->GetSkus() as $sku) {
			// Handle VAT issues
			if($sku->GetParentProduct()->GetTaxCode()->GetRate()==0) { $taxTable = '<tax-table-selector>tax_exempt</tax-table-selector>'; $taxable = '0'; } else { $taxTable = ''; $taxable = '1'; }
			if($sku->GetParentProduct()->GetTaxCode()->GetRate()==0) {
				// Handle Multibuys
				#if($this->mBasket->ProductsInBasket($sku->GetParentProduct()) > 1) {
					$price = $this->mBasket->GetOverruledSkuPrice($sku,false,false);
					// Check for upgrade cause nowt is free!
					if($price == 0) {
						$price = $this->mBasket->GetOverruledSkuPrice($sku,false,true);
					}
				#} else {
				#	$price = $sku->GetParentProduct()->GetActualPrice();
				#}
			} else {
				// Take the VAT off
				$price = $this->mMoneyHelper->RemoveVAT($this->mBasket->GetOverruledSkuPrice($sku,false,false));
				if($price == 0) {
					$price = $this->mMoneyHelper->RemoveVAT($this->mBasket->GetOverruledSkuPrice($sku,false,true));
				}
			}

			// Insert a 3-5 day notice
			if($sku->GetParentProduct()->IsNonStockProduct()) {
				$notice = '3-5 Day Dispatch: ';
			} else {
				$notice = '';
			}

			$this->mRequest .= 			'<item>';
			$this->mRequest .= 				'<item-name>'					.htmlspecialchars($notice).htmlspecialchars($sku->GetParentProduct()->GetDisplayName()).htmlspecialchars($sku->GetSkuAttributesList(),ENT_QUOTES,"UTF-8").'</item-name>';
			$this->mRequest .= 				'<item-description>'			.htmlspecialchars($sku->GetParentProduct()->GetDescription(),ENT_QUOTES,"UTF-8").'</item-description>';
			$this->mRequest .= 				'<merchant-item-id>'			.htmlspecialchars($sku->GetSageCode()).'</merchant-item-id>';
			$this->mRequest .= 				'<unit-price currency="GBP">'	.htmlspecialchars($price).'</unit-price>';
			$this->mRequest .= 				'<quantity>1</quantity>';
			$this->mRequest .= 				$taxTable;
			$this->mRequest .= 			'</item>';
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

			$price = $this->mMoneyHelper->RemoveVAT($package->GetActualPrice());
			$this->mRequest .= 			'<item>';
			$this->mRequest .= 				'<item-name>'					.htmlspecialchars($package->GetDisplayName(),ENT_QUOTES).htmlspecialchars($skusDescription,ENT_QUOTES,"UTF-8").'</item-name>';
			$this->mRequest .= 				'<item-description>'			.htmlspecialchars($package->GetDescription(),ENT_QUOTES,"UTF-8").'</item-description>';
			$this->mRequest .= 				'<merchant-item-id>ID'			.htmlspecialchars($package->GetPackageId()).'</merchant-item-id>';
			$this->mRequest .= 				'<unit-price currency="GBP">'	.htmlspecialchars($price).'</unit-price>';
			$this->mRequest .= 				'<quantity>1</quantity>';
		//	$this->mRequest .= 				$taxTable;	// Charge VAT on all packages
			$this->mRequest .= 			'</item>';
		}

		$this->mRequest .= 		'</items>';
		$this->mRequest .=		'<merchant-private-data>';
		$this->mRequest .=			'<echo_basket_id>'		.htmlspecialchars($this->mBasket->GetBasketId()).'</echo_basket_id>';
		$this->mRequest .=			'<echo_postage_method>'	.htmlspecialchars($this->mPostageId).'</echo_postage_method>';
		$this->mRequest .=		'</merchant-private-data>';
		$this->mRequest .= 	'</shopping-cart>';

		// Shipping Info
		$mHelper = new MoneyHelper;
		$this->mRequest .= 	'<checkout-flow-support>';
		$this->mRequest .= 		'<merchant-checkout-flow-support>';
		$this->mRequest .= 			'<tax-tables>';
		$this->mRequest .= 				'<default-tax-table>';
		$this->mRequest .= 					'<tax-rules>';
		$this->mRequest .= 						'<default-tax-rule>';
		$this->mRequest .= 							'<shipping-taxed>false</shipping-taxed>';
		$this->mRequest .= 							'<rate>'.htmlspecialchars($mHelper->GetVatRateAsDecimal()).'</rate>';
		$this->mRequest .= 							'<tax-area>';
		$this->mRequest .=								'<world-area/>';
		$this->mRequest .= 							'</tax-area>';
		$this->mRequest .= 						'</default-tax-rule>';
		$this->mRequest .= 					'</tax-rules>';
		$this->mRequest .= 				'</default-tax-table>';
		$this->mRequest .= 				'<alternate-tax-tables>';
		$this->mRequest .= 					'<alternate-tax-table name="tax_exempt" standalone="false">';
		$this->mRequest .= 						'<alternate-tax-rules>';
		$this->mRequest .=							'<alternate-tax-rule>';
		$this->mRequest .= 								'<rate>0</rate>';
		$this->mRequest .= 									'<tax-area>';
		$this->mRequest .=										'<world-area/>';
		$this->mRequest .= 									'</tax-area>';
		$this->mRequest .=							'</alternate-tax-rule>';
		$this->mRequest .= 						'</alternate-tax-rules>';
		$this->mRequest .= 					'</alternate-tax-table>';
		$this->mRequest .= 				'</alternate-tax-tables>';
		$this->mRequest .= 			'</tax-tables>';
		$this->mRequest .= 			'<shipping-methods>';
		$this->mRequest .= 				'<flat-rate-shipping name="'.htmlspecialchars($this->mPostageText).'">';
		$this->mRequest .= 					'<price currency="GBP">'.htmlspecialchars($this->mPostageCost).'</price>';
		$this->mRequest .= 				'</flat-rate-shipping>';
		$this->mRequest .= 			'</shipping-methods>';
		$this->mRequest .= 		'</merchant-checkout-flow-support>';
		$this->mRequest .= 	'</checkout-flow-support>';
		$this->mRequest .= '</checkout-shopping-cart>';
		#var_dump($this->mRequest);die();
	} // End CreateRequest

    function GetAuthenticationHeaders() {
      $headers = array();
      $headers[] = "Authorization: Basic ".base64_encode($this->mMerchantId.':'.$this->mMerchantKey);
      $headers[] = "Content-Type: application/xml; charset=UTF-8";
      $headers[] = "Accept: application/xml; charset=UTF-8";
      return $headers;
    } // End GetAuthenticationHeaders

    function GetBodyX($heads){
      $fp = explode(DOUBLE_ENTER,$heads,2);
      return $fp[1];
    }

	function PostRequest() {
		// Get headers
		$headers_arr = $this->GetAuthenticationHeaders();

		$ch = curl_init ();
		// Set the URL
		curl_setopt ( $ch, CURLOPT_URL, $this->mRequestUrl );
		// Auth headers
		curl_setopt ( $ch, CURLOPT_HEADER, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers_arr);
		// It's a POST request
		curl_setopt ( $ch, CURLOPT_POST, true );
		// Set the fields for the POST
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $this->mRequest );
		// Return it direct, don't print it out
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		// Timeout in 60 seconds
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
		// The next two lines must be present for the kit to work with newer version of cURL
		// You should remove them if you have any problems in earlier versions of cURL
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 2 );

		// Raw response
		$response = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			// Log Error
			$errorMsg = 'ERROR -> ' . curl_errno ( $ch ) . ': ' . curl_error ( $ch ) . ' Date: ' . date ( 'r', time () );
			$fh = @fopen('ProceedToGoogleCheckoutHandler.txt','a');
			@fwrite($fh,$errorMsg."\n\n\r" );
			@fclose($fh);
			// Return indication of error
			die('An Error Has Occured - Please Call Us On 01753 572741');
		} else {
			$body = $this->GetBodyX($response);
			$xml_parser = new gc_xmlparser($body);
			$root = $xml_parser->GetRoot();
			$data = $xml_parser->GetData();
			#mail('dgh500@gmail.com','checkout ok',$this->mRequest);
			if($data[$root]['redirect-url']) {
				header('Location: ' . $data[$root]['redirect-url']['VALUE']);
			} else {
			#	mail('dgh500@gmail.com','checkout problem',$this->mRequest);
				die('An Error Has Occured - Please Call Us On 01753 572741'); //var_dump($data);
			}
		}
	} // End PostRequest

} // End ProceedToGoogleCheckoutHandler

try {
	$handler = new ProceedToGoogleCheckoutHandler($_POST);
	$handler->CreateRequest();
	$handler->PostRequest();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>