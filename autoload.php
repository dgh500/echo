<?php

// Attempt to reduce caching problems
/*header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");*/
// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
#ini_set('memory_limit', '12M');

date_default_timezone_set('Europe/London');

// Global-type classes
include_once('Registry.php');
include_once('Error.php');

// Register objects
$registry = Registry::getInstance ();

// Debug Mode - Sets whether proceses log or not
$registry->debugMode = true;

// Local Mode - Sets directory and database config settings
if(strpos($_SERVER['SERVER_NAME'],'localhost') !== FALSE) {
	$registry->localMode = true;
} else {
	$registry->localMode = false;
}

// Disabled - Used when upgrading to display an error message
$registry->disabled = false;

// Exception Handler
require_once('EchoExceptionHandler.php');
set_exception_handler('EchoExceptionHandler');

// Whether or not google checkout is live or sandboxed
$registry->GoogleCheckoutLive = true;
// Whether Google Checkout is enabled
$registry->GoogleCheckoutEnabled = true;

// Whether or not Paypal Checkout is enabled
$registry->PaypalCheckoutEnabled = true;
// Whether paypal checkout is live or sandboxed
$registry->PaypalCheckoutLive = true;

// Payment Gateway to Use - paypal or sagepay
$registry->paymentGateway = 'sagepay';

// Load up the correct info
if ($registry->localMode) {
	$registry->rootDir 			= 'http://localhost/deepblue08/echo';
	$registry->baseDir 			= 'http://localhost/deepblue08/echo';
	$registry->secureBaseDir 	= 'http://localhost/deepblue08/echo';
	$registry->adminDir 		= 'http://localhost/deepblue08/echo/wombat7';
	$registry->viewDir 			= 'http://localhost/deepblue08/echo/view';
	$registry->formHandlersDir 	= 'http://localhost/deepblue08/echo/formHandlers';
	$registry->AjaxHandlerDir 	= 'http://localhost/deepblue08/echo/ajaxHandlers';
	$registry->cookieDomain 	= 'localhost';
	$registry->host 			= 'localhost';
	$registry->username 		= 'root';
	$registry->password 		= '';
	$registry->dbName 			= 'echo';
} else {
	$registry->rootDir 			= '';# dont change this one'http://www.echosupplements.com/';
	$registry->baseDir 			= 'http://www.echosupplements.com';
	$registry->secureBaseDir 	= 'https://www.echosupplements.com';
	$registry->adminDir 		= 'http://www.echosupplements.com/wombat7';
	$registry->viewDir 			= 'http://www.echosupplements.com/view';
	$registry->formHandlersDir 	= 'http://www.echosupplements.com/formHandlers';
	$registry->AjaxHandlerDir 	= 'http://www.echosupplements.com/ajaxHandlers';
	$registry->cookieDomain 	= '.echosupplements.com';
	$registry->host 			= 'localhost';
	$registry->username 		= 'echosupp_echo';
	$registry->password 		= 'm0rpeth3cho';
	$registry->dbName 			= 'echosupp_echodb';
}

/*$link = mysql_connect($registry->host,$registry->username,$registry->password) or die(mysql_error());
mysql_select_db($registry->dbName,$link);
die(var_dump($link));*/
$registry->PaymentGatewayMode = 'LIVE';

// Register the correct PayPal API details
if($registry->PaymentGatewayMode == 'LIVE') {
	$registry->paypalApiUsername 	= 'orders_api1.echosupplements.com';
	$registry->paypalApiPassword 	= 'KJ7G7YG9BMHM3S6L';
	$registry->paypalApiSignature 	= 'AFQy2L8QR1JE3LAMfke0UVyf03bHAtzLZxBD1e85xV8d9vZPTXepzKAC';
	$registry->paypalPaymentProcessingUrl = 'https://api-3t.paypal.com/nvp';
} else {
	$registry->paypalApiUsername 	= 'dave_1279214095_biz_api1.echosupplements.com';
	$registry->paypalApiPassword 	= '1279214106';
	$registry->paypalApiSignature 	= 'AFcWxV21C7fd0v3bYYYRCpSSRl31AVgVIX88YSYBxhiGrLHFjR4AitAs';
	$registry->paymentProcessingUrl = 'https://api-3t.sandbox.paypal.com/nvp';
}

// Set up the payment processing URL
switch ($registry->PaymentGatewayMode) {
	case 'SIMULATOR' :
		// Request URL for payment processing
		$registry->paymentProcessingUrl = 'https://test.sagepay.com/showpost/showpost.asp';
#		$registry->paymentProcessingUrl = 'https://ukvpstest.protx.com/VSPSimulator/VSPDirectGateway.asp';
		$registry->paymentReleaseUrl 	= 'https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?service=VendorReleaseTx';
		$registry->paymentCancelUrl 	= 'https://ukvpstest.protx.com/VSPSimulator/VSPServerGateway.asp?service=VendorAbortTx';
		$registry->payment3dCallback 	= 'https://ukvpstest.protx.com/VSPSimulator/VSPDirectCallback.asp';
		$registry->paymentVendor 		= 'echosupplements';
		break;
	case 'TEST' :
		// Request URL for payment processing
		#$registry->paymentProcessingUrl = 'https://test.sagepay.com/showpost/showpost.asp';
		$registry->paymentProcessingUrl = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';
		$registry->paymentReleaseUrl 	= 'https://test.sagepay.com/gateway/service/release.vsp';
		$registry->paymentCancelUrl 	= 'https://test.sagepay.com/gateway/service/abort.vsp';
		$registry->payment3dCallback 	= 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';
		$registry->paymentVendor 		= 'echosupplements';
		break;
	case 'LIVE' :
		// Request URL for payment processing
		//$registry->paymentProcessingUrl = 'https://test.sagepay.com/showpost/showpost.asp';
		//$registry->paymentReleaseUrl = 'https://test.sagepay.com/showpost/showpost.asp';

		$registry->paymentProcessingUrl = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
		$registry->paymentReleaseUrl 	= 'https://live.sagepay.com/gateway/service/release.vsp';
		$registry->paymentCancelUrl 	= 'https://live.sagepay.com/gateway/service/abort.vsp';
		$registry->payment3dCallback 	= 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
		$registry->paymentVendor 		= 'echosupplements';
		break;
}

// Google Keys
$registry->GoogleMapsApiKey 			= 'ABQIAAAARSKfg_H76zc0p8FTtAtMGxRa2XWjWa_oyWFhtZw-1FfZKpy8ixTQo-6XOoJosJGWeXHJItkevyMGpw'; // Registered to www.echosupplements.com
$registry->GoogleAnalyticsTrackerKey 	= 'UA-16824865-1'; // Tracking for www.echosupplements.com
$registry->GoogleBaseFile 				= 'base2.xml'; // Google Base (froogle) feed file name
$registry->GoogleBaseProductType 		= 'X';

// Google Checkout
if($registry->GoogleCheckoutLive) {
	$registry->GoogleCheckoutUrl			= 'https://checkout.google.com/api/checkout/v2/merchantCheckout/Merchant/327933992030665';
	$registry->GoogleCheckoutMerchantId		= '327933992030665';
	$registry->GoogleCheckoutMerchantKey	= '5Xot28nvmTMdZpLMNIKCPA';
	$registry->GoogleCheckoutMode			= 'live';
} else {
	$registry->GoogleCheckoutUrl			= 'https://sandbox.google.com/checkout/api/checkout/v2/merchantCheckout/Merchant/171220238430982';
	$registry->GoogleCheckoutMerchantId		= '171220238430982';
	$registry->GoogleCheckoutMerchantKey	= 'XkgLxUe85B41VN0PPUd8UQ';
	$registry->GoogleCheckoutMode			= 'sandbox';
}
/*
// Paypal Express Checkout
if($registry->PaypalCheckoutLive) {
	$registry->PaypalCheckoutUrl			= 'https://api-3t.paypal.com/nvp';
	$registry->PaypalApiUsername			= 'orders_api1.echosupplements.com';
	$registry->PaypalApiPassword			= 'KJ7G7YG9BMHM3S6L';
	$registry->PaypalApiSignature			= 'AFQy2L8QR1JE3LAMfke0UVyf03bHAtzLZxBD1e85xV8d9vZPTXepzKAC';
} else {
	$registry->PaypalCheckoutUrl			= 'https://api-3t.sandbox.paypal.com/nvp';
	$registry->PaypalApiUsername			= 'dgh500_1275155732_biz_api1.gmail.com';
	$registry->PaypalApiPassword			= '66ETCEZ2P7G425LD';
	$registry->PaypalApiSignature			= 'AH.7sPotjKWxgIqidrCUTj99tLTyA1--xk2D9m7eGbYlJFm17SXugWVV';
}*/

// Meta Info
$registry->metaDescription = 'At Echo Supplements we are totally committed to bringing you the best supplements on the market at great prices. We pride ourselves on our customer service and can talk you through the best supplements for your training. We stock all of the top brands including Sci-MX, Boditronics, MET-Rx, USN, VPX, BSN, Dorian Yates, 1 Rep Max, Gaspari and Pure Protein. In addition we have put together a comprehensive range of supplement packages for some of the most common muscle gain and fat loss goals!';
$registry->metaKeywords = 'Echo Supplements, protein, creatine, sci mx nutrition, boditronics, usn, biox, met rx, gaspari, bsn, muscletech, glutamine, protein bars, protein flapjacks';
$registry->welcomeText = 	'';
$registry->companyName	= 'Echo Supplements';

// Basket Options
$registry->basketMessage = 'Please make sure that <strong>ALL</strong> of the details are correct here - these cannot be changed later!';
$registry->packageVatFreeAllowed = true;

// Relative Directory Settings
$registry->smallImageDir 		= 'uploadImages/small/';
$registry->mediumImageDir 		= 'uploadImages/medium/';
$registry->largeImageDir 		= 'uploadImages/large/';
$registry->originalImageDir 	= 'uploadImages/original/';
$registry->manufacturerImageDir = 'manufacturerImages/';
$registry->contentImageDir 		= 'contentImages/';
$registry->tagImageDir 			= 'tagImages/';
$registry->debugDir 			= 'debug';

// Image properties
$registry->smallImageSize 		= 100;
$registry->manufacturerImageSize= 175;
$registry->tagImageSize 		= 175;
$registry->mediumImageSize 		= 140;
$registry->largeImageSize 		= 300;

// Content IDs
$registry->footerContent = 5;
$registry->returnContent = 2;

// Display other sites?
$registry->displayOtherSites = false;

// Has Admin?
$registry->hasAdmin = true;
#echo 'pre';
// Database Initialisation
try {
	if(!isset($registry->database)) {
		$registry->database = new PDO ( 'mysql:host=' . $registry->host . ';dbname=' . $registry->dbName, $registry->username, $registry->password );
#		$registry->database->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
	}
} catch ( Exception $e ) {
	die ( $e->getMessage () );
	// Null; do NOT display an error with the password in!
}

#echo 'post';
// Javascript Autoload
if(!isset($disableJavascriptAutoload)) {
	$registry->disableJavascriptAutoload = false;
} else {
	$registry->disableJavascriptAutoload = true;
}

// Include base classes
include_once ('formHandlers/Handler.php');
include_once ('helpers/Helper.php');

// Affiliate files - need to be sent before any other information (same as headers)
include_once ('model/AffiliateModel.php');
include_once ('helpers/AffiliateHelper.php');

// Include common files
require('common.php');
#die('tesst');
// Once the CatalogueModel is loaded (above) we can initialise the catalogue
$registry->galleryId 	 = 1;
$registry->catalogue 	 = new CatalogueModel(1);
$registry->contentType 	 = new ContentStatusModel(1);
$registry->sessionHelper = new SessionHelper();

// Ajax Base Handler
include_once('ajaxHandlers/AjaxHandler.php');

// PHPMailer
if(!class_exists("PHPMailer")) {
	include('phpMailer/class.phpmailer.php');
}

if ($registry->hasAdmin) {
	// Include FCKeditor
	include_once ("wombat7/fckeditor/fckeditor.php");
}
#echo 'autoload complete';
?>