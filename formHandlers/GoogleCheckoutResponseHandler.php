<?php
include('../autoload.php');

/**
 * Copyright (C) 2007 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 /* This is the response handler code that will be invoked every time
  * a notification or request is sent by the Google Server
  *
  * To allow this code to receive responses, the url for this file
  * must be set on the seller page under Settings->Integration as the
  * "API Callback URL'
  * Order processing commands can be sent automatically by placing these
  * commands appropriately
  *
  * To use this code for merchant-calculated feedback, this url must be
  * set also as the merchant-calculations-url when the cart is posted
  * Depending on your calculations for shipping, taxes, coupons and gift
  * certificates update parts of the code as required
  *
  */

  require_once('GoogleCheckoutLibrary/googleresponse.php');
  require_once('GoogleCheckoutLibrary/googlemerchantcalculations.php');
  require_once('GoogleCheckoutLibrary/googleresult.php');
  require_once('GoogleCheckoutLibrary/googlerequest.php');

  define('RESPONSE_HANDLER_ERROR_LOG_FILE','googleerror.log');
  define('RESPONSE_HANDLER_LOG_FILE','googlemessage.log');

  $merchant_id 	= $registry->GoogleCheckoutMerchantId;  	// Your Merchant ID
  $merchant_key = $registry->GoogleCheckoutMerchantKey;  	// Your Merchant Key
  $server_type 	= $registry->GoogleCheckoutMode; 			// Live?
  $currency 	= 'GBP';  	
  
  $Gresponse = new GoogleResponse($merchant_id, $merchant_key);

  $Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type, $currency);

  // Setup the log file
  $Gresponse->SetLogFiles(RESPONSE_HANDLER_ERROR_LOG_FILE, 
                                        RESPONSE_HANDLER_LOG_FILE, L_ALL);

  // Retrieve the XML sent in the HTTP POST request to the ResponseHandler
  $xml_response = isset($HTTP_RAW_POST_DATA)?
                    $HTTP_RAW_POST_DATA:file_get_contents("php://input");
 /* $xml_response = '<new-order-notification xmlns="http://checkout.google.com/schema/2" serial-number="677763496955242-00001-7">
  <timestamp>2009-06-29T15:38:58.319Z</timestamp>
  <shopping-cart>
    <merchant-private-data>
      <deepblue_basket_id>pm2o8ec7tl82ch01bijq0mq2e6</deepblue_basket_id>
      <deepblue_postage_method>9</deepblue_postage_method>
    </merchant-private-data>
    <items>
      <item>
        <item-name>BREATHING AIR STICKER</item-name>
        <item-description>Breathing Air sticker for your tank</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">0.87</unit-price>
        <merchant-item-id>ACCSTICKER1</merchant-item-id>
      </item>
      <item>
        <item-name>BREATHING AIR STICKER</item-name>
        <item-description>Breathing Air sticker for your tank</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">0.87</unit-price>
        <merchant-item-id>ACCSTICKER1</merchant-item-id>
      </item>
      <item>
        <item-name>BREATHING AIR STICKER</item-name>
        <item-description>Breathing Air sticker for your tank</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">0.87</unit-price>
        <merchant-item-id>ACCSTICKER1</merchant-item-id>
      </item>
      <item>
        <item-name>BREATHING AIR STICKER</item-name>
        <item-description>Breathing Air sticker for your tank</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">0.87</unit-price>
        <merchant-item-id>ACCSTICKER1</merchant-item-id>
      </item>
      <item>
        <item-name>DRY SUIT POWDER</item-name>
        <item-description>Suit Eeze allows you to slide in and out of your Wet or Dry suit with ease.</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">1.3</unit-price>
        <merchant-item-id>ACCDRYTALK</merchant-item-id>
      </item>
      <item>
        <item-name>Mares Lirica Mask and Snorkel Package</item-name>
        <item-description>Innovative in form, revolutionary in vision. The rounded corners and colored inserts of the frame give Lirica a fresh, sparkling, quintessentially modern look.</item-description>
        <quantity>1</quantity>
        <unit-price currency="GBP">26.04</unit-price>
        <merchant-item-id>63</merchant-item-id>
      </item>
    </items>
  </shopping-cart>
  <order-adjustment>
    <merchant-codes />
    <shipping>
      <flat-rate-shipping-adjustment>
        <shipping-name>Saturday Delivery</shipping-name>
        <shipping-cost currency="GBP">21.9</shipping-cost>
      </flat-rate-shipping-adjustment>
    </shipping>
    <total-tax currency="GBP">4.63</total-tax>
    <adjustment-total currency="GBP">26.53</adjustment-total>
  </order-adjustment>
  <buyer-id>374424089752882</buyer-id>
  <google-order-number>677763496955242</google-order-number>
  <buyer-shipping-address>
    <company-name></company-name>
    <contact-name>Dave h</contact-name>
    <email>dave@deepbluedive.com</email>
    <phone></phone>
    <fax></fax>
    <address1>scascac</address1>
    <address2>csacas</address2>
    <country-code>GB</country-code>
    <city>scasac</city>
    <region>CSACAS</region>
    <postal-code>NE26 2JW</postal-code>
  </buyer-shipping-address>
  <buyer-billing-address>
    <company-name></company-name>
    <contact-name>Dave h</contact-name>
    <email>dave@deepbluedive.com</email>
    <phone></phone>
    <fax></fax>
    <address1>scascac</address1>
    <address2>csacas</address2>
    <country-code>GB</country-code>
    <city>scasac</city>
    <region>CSACAS</region>
    <postal-code>NE26 2JW</postal-code>
  </buyer-billing-address>
  <buyer-marketing-preferences>
    <email-allowed>true</email-allowed>
  </buyer-marketing-preferences>
  <order-total currency="GBP">57.35</order-total>
  <fulfillment-order-state>NEW</fulfillment-order-state>
  <financial-order-state>REVIEWING</financial-order-state>
</new-order-notification>';*/

  if (get_magic_quotes_gpc()) {
    $xml_response = stripslashes($xml_response);
  }
  list($root, $data) = $Gresponse->GetParsedXML($xml_response);
  $Gresponse->SetMerchantAuthentication($merchant_id, $merchant_key);

  $status = $Gresponse->HttpAuthentication();
  if(! $status) {
    die('authentication failed');
  }

  /* Commands to send the various order processing APIs
   * Send charge order : $Grequest->SendChargeOrder($data[$root]
   *    ['google-order-number']['VALUE'], <amount>);
   * Send process order : $Grequest->SendProcessOrder($data[$root]
   *    ['google-order-number']['VALUE']);
   * Send deliver order: $Grequest->SendDeliverOrder($data[$root]
   *    ['google-order-number']['VALUE'], <carrier>, <tracking-number>,
   *    <send_mail>);
   * Send archive order: $Grequest->SendArchiveOrder($data[$root]
   *    ['google-order-number']['VALUE']);
   *
   */
  switch ($root) {
    case "request-received": {
      break;
    }
    case "error": {
      break;
    }
    case "diagnosis": {
      break;
    }
    case "checkout-redirect": {
      break;
    }
	// New Order Received
    case "new-order-notification": {
	  // Acknowledge the order
      $Gresponse->SendAck(false);
	  
	  // Process the order
	  NewGoogleOrder($Gresponse,$data);
      break;
    }
	// Order Updated
    case "order-state-change-notification": {
		
	  // Acknowledge the response
      $Gresponse->SendAck(false);		
		
	  // Needed to update the order status
	  $orderStatusController = new OrderStatusController;

	  // Order ID?
	  $googleOrderId = $data[$root]['google-order-number']['VALUE'];
	  $order = new OrderModel($googleOrderId,true);
	  
	  // Get the state values
      $new_financial_state 	= $data[$root]['new-financial-order-state']['VALUE'];
      $new_fulfillment_order= $data[$root]['new-fulfillment-order-state']['VALUE'];
	  
	  // Deal with whatever has changed
      switch($new_financial_state) {
        case 'REVIEWING': {
			// Do nothing, its already awaiting auth
          break;
        }
        case 'CHARGEABLE': {
          //$Grequest->SendProcessOrder($data[$root]['google-order-number']['VALUE']);
          //$Grequest->SendChargeOrder($data[$root]['google-order-number']['VALUE'],'');
			// The order is now authorised
			$orderStatus = $orderStatusController->GetAuthorised();
			$order->SetStatus($orderStatus);
			echo '<pre>'; var_dump($data); echo '</pre>';die();
			
          break;
        }
        case 'CHARGING': {
			// Don't need to do anything except wait for the charged signal
          break;
        }
        case 'CHARGED': {
			$orderStatus = $orderStatusController->GetInTransit();
			$order->SetStatus($orderStatus);			
          break;
        }
        case 'PAYMENT_DECLINED': {
			$orderStatus = $orderStatusController->GetFailed();
			$order->SetStatus($orderStatus);			
          break;
        }
        case 'CANCELLED': {
			$orderStatus = $orderStatusController->GetCancelledByMerchant();
			$order->SetStatus($orderStatus);			
          break;
        }
        case 'CANCELLED_BY_GOOGLE': {
          //$Grequest->SendBuyerMessage($data[$root]['google-order-number']['VALUE'],
          //    "Sorry, your order is cancelled by Google", true);
			$orderStatus = $orderStatusController->GetCancelledByUser();
			$order->SetStatus($orderStatus);		  
          break;
        }
        default:
          break;
      }

      switch($new_fulfillment_order) {
        case 'NEW': {
          break;
        }
        case 'PROCESSING': {
          break;
        }
        case 'DELIVERED': {
          break;
        }
        case 'WILL_NOT_DELIVER': {
          break;
        }
        default:
          break;
      }
      break;
    }
    case "charge-amount-notification": {
      //$Grequest->SendDeliverOrder($data[$root]['google-order-number']['VALUE'],
      //    <carrier>, <tracking-number>, <send-email>);
      //$Grequest->SendArchiveOrder($data[$root]['google-order-number']['VALUE'] );
      $Gresponse->SendAck();
      break;
    }
    case "chargeback-amount-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "refund-amount-notification": {
      $Gresponse->SendAck();
      break;
    }
    case "risk-information-notification": {
      $Gresponse->SendAck();
      break;
    }
    default:
      $Gresponse->SendBadRequestStatus("Invalid or not supported Message");
      break;
  }
  
  //! Creates a new Google Checkout order
  function NewGoogleOrder($gResponse,$data) {
  
	// Controllers
	$orderController 		= new OrderController;
	$orderStatusController 	= new OrderStatusController;
	$customerController 	= new CustomerController;
	$addressController		= new AddressController;
	
	// Create a customer for the order
	$customer = $customerController->CreateCustomer();
	
	// Populate the customer details
	$customer->SetFirstName(		$data["new-order-notification"]["buyer-shipping-address"]["contact-name"]["VALUE"]);
	$customer->SetEmail(			$data["new-order-notification"]["buyer-shipping-address"]["email"]["VALUE"]);
	$customer->SetDaytimeTelephone(	$data["new-order-notification"]["buyer-shipping-address"]["phone"]["VALUE"]);
	
	// If it got here, the order is awaiting auth
	$orderStatus = $orderStatusController->GetDefault();
	
	// Get Google's order number
	$googleOrderNumber = RetreiveGoogleOrderNumber($data);
	
	// Create a address to send to/bill
	$billingAddress = $addressController->CreateAddress();
	$shippingAddress = $addressController->CreateAddress();
	
	// Populate the shipping address
	$billingCountry = new CountryModel($data["new-order-notification"]["buyer-shipping-address"]["country-code"]["VALUE"],true);
	$billingAddress->SetCompany(	$data["new-order-notification"]["buyer-shipping-address"]["company-name"]["VALUE"]);
	$billingAddress->SetLine1(		$data["new-order-notification"]["buyer-shipping-address"]["address1"]["VALUE"]);
	$billingAddress->SetLine2(		$data["new-order-notification"]["buyer-shipping-address"]["address2"]["VALUE"]);
	$billingAddress->SetLine3(		$data["new-order-notification"]["buyer-shipping-address"]["city"]["VALUE"]);
	$billingAddress->SetCounty(		$data["new-order-notification"]["buyer-shipping-address"]["region"]["VALUE"]);
	$billingAddress->SetPostcode(	$data["new-order-notification"]["buyer-shipping-address"]["postal-code"]["VALUE"]);
	$billingAddress->SetCountry(	$billingCountry);

	// Populate the billing address
	$shippingCountry = new CountryModel($data["new-order-notification"]["buyer-billing-address"]["country-code"]["VALUE"],true);
	$shippingAddress->SetCompany(	$data["new-order-notification"]["buyer-billing-address"]["company-name"]["VALUE"]);
	$shippingAddress->SetLine1(		$data["new-order-notification"]["buyer-billing-address"]["address1"]["VALUE"]);
	$shippingAddress->SetLine2(		$data["new-order-notification"]["buyer-billing-address"]["address2"]["VALUE"]);
	$shippingAddress->SetLine3(		$data["new-order-notification"]["buyer-billing-address"]["city"]["VALUE"]);
	$shippingAddress->SetCounty(	$data["new-order-notification"]["buyer-billing-address"]["region"]["VALUE"]);
	$shippingAddress->SetPostcode(	$data["new-order-notification"]["buyer-billing-address"]["postal-code"]["VALUE"]);
	$shippingAddress->SetCountry(	$shippingCountry);

	// Price without Postage
	$priceWithoutPostage = $data["new-order-notification"]["order-total"]["VALUE"] - $data["new-order-notification"]["order-adjustment"]["shipping"]["flat-rate-shipping-adjustment"]["shipping-cost"]["VALUE"];
	
	// Postage Method
	$postageMethod = new PostageMethodModel($data["new-order-notification"]["shopping-cart"]["merchant-private-data"]["deepblue_postage_method"]["VALUE"]);
	
	// Create order
	$basket = new BasketModel(RetreiveBasketId($data));
	$order = $orderController->CreateOrder($basket);
	$order->SetGoogleCheckout(true);
	$order->SetStatus($orderStatus);
	$order->SetTotalPrice($priceWithoutPostage);
	$order->SetTotalPostage($data["new-order-notification"]["order-adjustment"]["shipping"]["flat-rate-shipping-adjustment"]["shipping-cost"]["VALUE"]);
	$order->SetCustomer($customer);
	$order->SetBrochure(1);
	$order->SetShippingAddress($shippingAddress);
	$order->SetBillingAddress($billingAddress);
	$order->SetStaffName('Google Checkout');
	$order->SetCatalogue($basket->GetCatalogue());
	$order->SetPostageMethod($postageMethod);
	$order->SetCourier($postageMethod->GetCourier());
	$order->SetTransactionId(RetreiveGoogleOrderNumber($data));
	
	// Make an order items order for it :)
	$order->ConvertBasketIntoOrder();
	
	echo '<pre>'; var_dump($data); echo '</pre>';die();	
  }
	
  //! Retreives Google's unique order ID from the raw data
  function RetreiveBasketId($data) {
		return $data["new-order-notification"]["shopping-cart"]["merchant-private-data"]["deepblue_basket_id"]["VALUE"];
  }

  //! Retreives Google's unique order ID from the raw data
  function RetreiveGoogleOrderNumber($data) {
		return $data["new-order-notification"]["google-order-number"]["VALUE"];
  }
  
  /* In case the XML API contains multiple open tags
     with the same value, then invoke this function and
     perform a foreach on the resultant array.
     This takes care of cases when there is only one unique tag
     or multiple tags.
     Examples of this are "anonymous-address", "merchant-code-string"
     from the merchant-calculations-callback API
  */
  function get_arr_result($child_node) {
    $result = array();
    if(isset($child_node)) {
      if(is_associative_array($child_node)) {
        $result[] = $child_node;
      }
      else {
        foreach($child_node as $curr_node){
          $result[] = $curr_node;
        }
      }
    }
    return $result;
  }

  /* Returns true if a given variable represents an associative array */
  function is_associative_array( $var ) {
    return is_array( $var ) && !is_numeric( implode( '', array_keys( $var ) ) );
  }
?>