<?php

//! Deals with tasks like opening/closing DIV containers, generating HREF code and image code for views
class PublicLayoutHelper {

	//! The base directory of the current catalogue (EG. /dive)
	var $mBaseDir;
	//! The secure part of the site (Eg. with https...)
	var $mSecureBaseDir;
	//! The root directory of the site (Eg. the /dive, /clay etc. are contained within it)
	var $mRootDir;
	//! A PDO Database connection
	var $mDatabase;
	//! The small image directory
	var $mSmallImageDir;
	//! The medium image directory
	var $mMediumImageDir;
	//! The large image directory
	var $mLargeImageDir;
	//! The original image directory
	var $mOriginalImageDir;
	//! The manufacturer image directory
	var $mManufacturerImageDir;

	//! Initialises the base directory, secure base directory and database connection
	function __construct($secure = false) {
		$this->mRegistry = Registry::getInstance ();
		if ($secure) {
			$this->mBaseDir = $this->mRegistry->secureBaseDir;
			$this->mSecureBaseDir = $this->mRegistry->secureBaseDir;
		} else {
			$this->mBaseDir = $this->mRegistry->baseDir;
			$this->mSecureBaseDir = $this->mRegistry->secureBaseDir;
		}
		$this->mRootDir = $this->mRegistry->rootDir;
		$this->mDatabase = $this->mRegistry->database;
		$this->mSmallImageDir = $this->mRegistry->smallImageDir;
		$this->mMediumImageDir = $this->mRegistry->mediumImageDir;
		$this->mLargeImageDir = $this->mRegistry->largeImageDir;
		$this->mOriginalImageDir = $this->mRegistry->originalImageDir;
		$this->mManufacturerImageDir = $this->mRegistry->manufacturerImageDir;
		$this->mCatalogue = $this->mRegistry->catalogue;
	}

	//! Adds an anchor with ID #top for use on long pages
	/*
	 * @return String - The XHTML code
	 */
	function AddTopRelativeAnchor() {
		$str = '<a name="top" id="top"></a>';
		return $str;
	}

	//! Sets the <title> tag
	/*
	 * @param $title [in] - The desired title
	 * @return String - The XHTML code
	 */
	function Title($title) {
		$str = '	<title>
		'.$title.'
	</title>
';
		return $str;
	}

	//! Sets the meta description
	/*
	 * @param $description String [in] - The description
	 * @return String - The XHTML code
	 */
	function MetaDescription($description='Echo Supplements') {
		$str = '	<meta name="Description" content="'.$description.'" />
';
		return $str;
	}

	function MetaKeywords($keywords='Echo Supplements') {
		$str = '<meta name="Keywords" content="'.$keywords.'" />';
		return $str;
	}

	//! Sets a <meta> tag to set the character set, defaults to ISO-8859-1 (Allows display of a few symbols such as £ sign etc.) Also allows changing the content type, defaults to 'text/html'
	/*
	 * @param $charset String [in] - The character set to use, defaults to ISO-8859-1
	 * @param $contentType String [in] - The content type, defaults to 'text/html'
	 * @return String - The XHTML code
	 */
	function Charset($charset = 'ISO-8859-1', $contentType = 'text/html') {
		$str = '	<meta http-equiv="Content-Type" content="' . $contentType . '; charset=' . $charset . '" />
';
		return $str;
	}

	//! Opens the body tag
	/*!
	 * @param $onload String [in] - A possible Javascript funtion to load with the body tag, optional
	 * @return String - The XHTML code
	 */
	function OpenBody($onload = false) {
		if (! $onload) {
			$str = '<body>
';
		} else {
			$str = '<body' . $onload . '>
';
		}
		return $str;
	}

	//! Closes the body tag
	/*!
	 * @return String - The XHTML code
	 */
	function CloseBody($basketPage=false) {
		$registry = Registry::getInstance ();

		if($basketPage || $registry->localMode) {
			$tracking = '';
		} else {
			$tracking = '
		<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			var pageTracker = _gat._getTracker("' . $registry->GoogleAnalyticsTrackerKey . '");
			pageTracker._setDomainName("none");
			pageTracker._setAllowLinker(true);
			pageTracker._trackPageview();
		</script>';
		}

		$plusOne = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
		$plusOne = '';

		$str = $tracking.$plusOne.'</body>';
/*		if(!$basketPage) {
		$str .= <<<EOT
<a href="http://www.instantssl.com" id="comodoTL">Server SSL Certificate</a>
<script language="JavaScript" type="text/javascript">
COT("http://www.echosupplements.com/images/cot.gif", "SC2", "none");
</script>
EOT;
		}*/
		return $str;
	}

	//! Opens the public header container (with logo, other sites, account nav etc.)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenHeader() {
		$str = '		<div id="header">
';
		return $str;
	}

	//! Closes the public header container
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHeader() {
		$str = '</div> <!-- Close header -->
';
		return $str;
	}

	//! Opens the HTML tag
	/*!
	 * @param $namespace String [in] : The namespace to be used, defaults to 'http://www.w3.org/1999/xhtml'
	 * @return String - The XHTML code
	 */
	function OpenHtml($namespace = 'http://www.w3.org/1999/xhtml') {
		$str = '<html xmlns="' . $namespace . '">
';
		return $str;
	}

	//! Generates the DOCTYPE tag
	/*!
	 * @param $dtd String [in] : The document type definition for the page, defaults to 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd' (XHTML Strict)
	 * @param $mode String [in] : The mode for the page, defaults to '-//W3C//DTD XHTML 1.0 Strict//EN' (XHTML Strict)
	 * @return String - The XHTML code
	 */
	function Doctype($dtd = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd', $mode = '-//W3C//DTD XHTML 1.0 Strict//EN') {
		$str = '<!DOCTYPE html PUBLIC "' . $mode . '" "' . $dtd . '">';
		$str = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		return $str;
	}//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

	//! Closes the HTML tag
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHtml() {
		$str = '</html>';
		return $str;
	}

	//! Opens the <head> tag
	/*!
	 * @return String - The XHTML code
	 */
	function OpenHead() {
		$str = '<head>
  	<!-- Mimic Internet Explorer 7 -->
		<meta http-equiv="X-UA-Compatible" content="IE=8" >
	<link rel="icon" href="' . $this->mBaseDir . '/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="' . $this->mBaseDir . '/favicon.ico" type="image/x-icon" />
	<link href="https://plus.google.com/101746486729690416875" rel="publisher" />
';
		return $str;
	}

	//! Closes the <head> tag, any IE CSS hacks need to go in this function!
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHead($disableTrustLogo=true) {
		$str = '';
		if(!$disableTrustLogo) {
			$str .= <<<EOT
<script language="javascript" type="text/javascript">
//<![CDATA[
var cot_loc0=(window.location.protocol == "https:")? "https://secure.comodo.net/trustlogo/javascript/cot.js" :
"http://www.trustlogo.com/trustlogo/javascript/cot.js";
document.writeln('<scr' + 'ipt language="JavaScript" src="'+cot_loc0+'" type="text\/javascript">' + '<\/scr' + 'ipt>');
//]]>
</script>
EOT;
		}
		$str .= '</head>';
		return $str;
	}

	//! Opens the left part of the header (With the logo and search box in)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenHeaderLeft() {
		$str = '			<div id="headerLeft">
';
		return $str;
	}

	//! Closes the left part of the header (With the logo and search box in)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHeaderLeft() {
		$str = '			</div> <!-- Close headerLeft -->';
		return $str;
	}

	//! Generates the code for the logo in the header. Displays the $title text if CSS is disabled
	/*!
	 * @param $url String [in] - The URL the header should link to (usually homepage)
	 * @param $title String [in] - The title of the page (Ie. Usually the catalogue name)
	 * @return String - The XHTML code
	 */
	function HeaderLogo($url, $title, $secure=false) {
		$registry = Registry::getInstance();
		if($secure) {
			$dir = $registry->secureBaseDir;
		} else {
			$dir = $registry->baseDir;
		}
		$str = '				<a href="' . $url . '">
					<img src="'.$dir.'/images/headerLeft.jpg" title="'.$title.'" width="400" height="102" />
				</a>
';
		return $str;
	}

	//! Opens the middle part of the header (That tiles until it meets the right section)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenHeaderMid() {
		$str = '
			<div id="headerMidSection">';
		return $str;
	}

	//! Closes the middle part of the header
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHeaderMid() {
		$str = '</div> <!-- Close Header Mid -->
		';
		return $str;
	}

	//! Opens the left column (Which holds the shop-by-department view)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenLeftCol() {
		$str = '			<div id="leftCol">';
		return $str;
	}

	//! Closes the left column
	/*!
	 * @return String - The XHTML code
	 */
	function CloseLeftCol() {
		$str = '			</div> <!-- Close leftCol (Left Column) -->
		';
		return $str;
	}

	//! Opens the right column (Which holds the shopping basket, order hotline etc.) NB. Also inserts a div to fill in the 'mid-section' of the background
	/*!
	 * @return String - The XHTML code
	 */
	function OpenRightCol($checkout = false) {
		$str = '<div id="rightCol">';
		return $str;
	}

	//! Closes the right column
	/*!
	 * @return String - The XHTML code
	 */
	function CloseRightCol() {
		$str = '</div> <!-- Close rightCol (Right Column) -->
		';
		return $str;
	}

	//! Opens the inner container in the right column
	/*!
	 * @return String - The XHTML code
	 */
	function OpenRightNavContainer() {
		$str = '<div id="rightNavContainer">
		';
		return $str;
	}

	//! Closes the inner container in the right column
	/*!
	 * @return String - The XHTML code
	 */
	function CloseRightNavContainer() {
		$str = '</div> <!-- Close rightNavContainer -->
		';
		return $str;
	}

	//! Opens the middle column (Containing the product/category etc.)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenMainColumn() {
		$str = '<div class="col1">';
		return $str;
	}

	//! Closes the middle column (Containing the product/category etc.)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseMainColumn() {
		$str = '</div> <!-- Close col1 (Main Column) -->';
		return $str;
	}

	//! Generates the code to display the order hotline image
	/*!
	 * @return String - The XHTML code
	 */
	function OrderHotline() {
		$str = '<img src="' . $this->mBaseDir . '/images/orderHotline.jpg" alt="Order Hotline Phone Number 0191 2536220" style="margin-bottom: 5px;" />
		';
		return $str;
	}

	//! Generates the code to display the feedback image
	/*!
	 * @return String - The XHTML code
	 */
	function Feedback() {
		$str = '<a href="'.$this->mBaseDir.'/feedback"><img src="' . $this->mBaseDir . '/images/feedback.jpg" alt="Deep Blue Feedback" /></a>
		';
		return $str;
	}

	function CheckoutHotline() {
		$str = '<img src="' . $this->mBaseDir . '/images/chkOrderHotline.jpg" alt="Order Hotline Phone Number 0191 2536220" />
		';
		return $str;
	}

	//! Generates the code to display the free delivery image
	/*!
	 * @return String - The XHTML code
	 */
	function FreeDelivery() {
		$str = '<a href="' . $this->mBaseDir . '/postalRates"><img src="' . $this->mBaseDir . '/images/freeDelivery.gif" alt="Free Delivery Conditions Apply" /></a>
		';
		return $str;
	}

	//! Generates the code to display the free delivery image
	/*!
	 * @return String - The XHTML code
	 */
	function PriceMatch() {
		$str = '<a href="' . $this->mBaseDir . '/priceMatch"><img src="' . $this->mBaseDir . '/images/priceMatch.jpg" alt="Price Match Guarantee" style="margin-top: 5px;" /></a>
		';
		return $str;
	}

	//! Generates the code to display the clearance image
	/*!
	 * @return String - The XHTML code
	 */
	function Clearance() {
		$str = '<a href="' . $this->mBaseDir . '/clearance"><img src="' . $this->mBaseDir . '/images/clearance.jpg" alt="Clearance List" style="margin-top: 5px;" /></a>
		';
		return $str;
	}

	//! Generates the code to display the advice image
	/*!
	 * @return String - The XHTML code
	 */
	function Advice() {
		$str = '<a href="' . $this->mBaseDir . '/advice"><img src="' . $this->mBaseDir . '/images/equipmentAdvice.gif" alt="Equipment Advice" style="margin-top: 5px;" /></a>
		';
		return $str;
	}

	//! Generates the code to display the shop pics image
	/*!
	 * @return String - The XHTML code
	 */
	function ShopPics() {
		$str = '<img src="' . $this->mBaseDir . '/images/shop.gif" alt="Deep Blue Shop" style="margin-top: 5px;" />
		';
		return $str;
	}

	//! Generates the code to display the offers of the week image
	/*!
	 * @return String - The XHTML code
	 */
	function OffersOfTheWeekButton() {
		$str = '<a href="' . $this->mBaseDir . '/offers"><img src="' . $this->mBaseDir . '/images/offersOfTheWeekButton.jpg" alt="Offers of the Week" style="margin-top: 5px;" /></a>
		';
		return $str;
	}

	//! Generates the code to display the secure site image
	/*!
	 * @return String - The XHTML code
	 */
	function SecureSite() {
		$str = '<img src="' . $this->mBaseDir . '/images/secureSite.gif" alt="One Hundred Percent Secure Site" style="margin-top: 5px;" />
		';
		return $str;
	}

	//! Generates the code to display the order a brochure image
	/*!
	 * @return String - The XHTML code
	 */
	function OrderBrochure() {
		$str = '<a href="' . $this->mBaseDir . '/brochure"><img src="' . $this->mBaseDir . '/images/orderBrochure.gif" alt="Order Brochure" style="margin-top: 5px;" /></a>
		';
		return $str;
	}

	//! Generates the top blurb for the home page
	/*!
	 * @return String - The XHTML code
	 */
	function TopBlurb() {
		$str = $this->mCatalogue->GetLongDescription ();
		return $str;
	}

	//! Opens the right section of the header (with the other sites, account navigation etc.)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenHeaderRight() {
		$str = '	<div id="headerRight">
		';
		return $str;
	}

	//! Closes the right section of the header (with the other sites, account navigation etc.)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseHeaderRight() {
		$str = '</div> <!-- Close headerRight -->
		';
		return $str;
	}

	//! Generates the section of the header with the 3 catalogue images in
	/*!
	 * @return String - The XHTML code
	 */
	function HeaderRightImages() {
		$str = '	<div id="headerImagesContainer"></div>';
		return $str;
	}

	//! Opens the inner centre page container (that contains the main content)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenCentrePageContainer() {
		$str = '	<div id="centreCol">
';
		return $str;
	}

	//! Closes the inner centre page container (that contains the main content)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseCentrePageContainer() {
		$str = '	</div> <!-- Close centreCol -->
		';
		return $str;
	}

	//! Opens the outer centre page container (that contains the main content)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenCentreColumn() {
		$str = '	<div class="centreCol">
		';
		return $str;
	}

	//! Closes the outer centre page container (that contains the main content)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseCentreColumn() {
		$str = '</div><!-- Close centreCol -->
		';
		return $str;
	}

	//! Opens the container of all 3 columns (after header)
	/*!
	 * @return String - The XHTML code
	 */
	function OpenLayoutContainers() {
		$str = '		<div class="threeColContainer">
';
		return $str;
	}

	//! Closes the container of all 3 columns (after header)
	/*!
	 * @return String - The XHTML code
	 */
	function CloseLayoutContainers() {
		$str = '</div> <!-- Close threeColContainer -->
		';
		return $str;
	}

	//! Opens the footer
	/*!
	 * @return String - The XHTML code
	 */
	function OpenFooterContainer() {
		$str = '<div id="footer">';
		return $str;
	}

	//! Closes the footer
	/*!
	 * @return String - The XHTML code
	 */
	function CloseFooterContainer() {
		$str = '</div> <!-- Close Footer --><br style="clear: both" />
		';
		return $str;
	}

	//! Generates the header part of the shopping basket. Displays text if CSS is disabled
	/*!
	 * @return String - The XHTML code
	 */
	function LoadBasketHeader() {
		$str = '<div id="shoppingBasketTitleContainer">
					<h1>Shopping Basket</h1>
				</div>
				';
		return $str;
	}

	//! Generates the footer part of the shopping basket
	/*!
	 * @return String - The XHTML code
	 */
	function LoadBasketFooter() {
		$str = '<div id="shoppingBasketFooterContainer">
				</div>
				';
		return $str;
	}

	//! Opens the shopping basket container
	/*!
	 * @return String - The XHTML code
	 */
	function OpenBasket() {
		$str = '<div id="shoppingBasketContainer">';
		return $str;
	}

	//! Closes the shopping basket container
	/*!
	 * @return String - The XHTML code
	 */
	function CloseBasket() {
		$str = '</div>';
		return $str;
	}

	//! Opens the product details container
	/*!
	 * @return String - The XHTML code
	 */
	function OpenProductDetailsContainer() {
		$str = '<div id="productDetailContainer">';
		return $str;
	}

	//! Closes the product details container
	/*!
	 * @return String - The XHTML code
	 */
	function CloseProductDetailsContainer() {
		$str = '</div><!-- End productDetailContainer -->';
		return $str;
	}

	//! Opens the package details container
	/*!
	 * @return String - The XHTML code
	 */
	function OpenPackageDetailsContainer() {
		$str = '<div id="packageDetailContainer">';
		return $str;
	}

	//! Closes the package details container
	/*!
	 * @return String - The XHTML code
	 */
	function ClosePackageDetailsContainer() {
		$str = '</div><!-- End packageDetailContainer -->';
		return $str;
	}

	function OpenProductDetailsTopSection() {
		$str = '<div id="productDetailsTopSection">';
		return $str;
	}

	function CloseProductDetailsTopSection() {
		$str = '</div><!-- End productDetailsTopSection -->';
		return $str;
	}

	function OpenPackageDetailsTopSection($containerHeight=false) {
		if($containerHeight) {
			$str = '<div id="packageDetailsTopSection" style="height: '.$containerHeight.'px !important;">';
		} else {
			$str = '<div id="packageDetailsTopSection">';
		}
		return $str;
	}

	function ClosePackageDetailsTopSection() {
		$str = '</div><!-- End packageDetailsTopSection -->';
		return $str;
	}

	function OpenProductOverviewSection($productName,$otherDescriptionTitle) {
		$str = '<div id="overviewTitle"><h2> '.$productName.' '.$otherDescriptionTitle.'</h2></div><div id="productDetailsOverviewSection">';
		return $str;
	}

	function CloseProductOverviewSection() {
		$str = '</div><!-- End productDetailsOverviewSection -->';
		return $str;
	}

	function OpenEchoDescriptionSection($productName) {
		$str = '<div id="echoDescriptionTitle"><h2>Echo Supplements on '.$productName.'</h2></div><div id="echoDescriptionOverviewSection">';
		return $str;
	}

	function CloseEchoDescriptionSection() {
		$str = '</div><!-- End echoDescriptionOverviewSection -->';
		return $str;
	}

	function OpenProductSocialNetworking() {
		$str = '<div id="productSocialNetworkingSection">';
		return $str;
	}

	function CloseProductSocialNetworking() {
		$str = '</div><!-- End productSocialNetworkingSection -->';
		return $str;
	}

	function OpenProductPackageCrossSellSection($productName,$packageDescription) {
		$packageDescription = substr($packageDescription,0,strlen($packageDescription)-1);
		$str = '<div id="packageCrosssellTitle"><h2>'.$productName.' - '.ucwords(strtolower($packageDescription)).' Deals</h2></div><div id="packageCrosssellSection">';
		return $str;
	}

	function CloseProductPackageCrossSellSection() {
		$str = '</div><!-- End packageCrosssellSection -->';
		return $str;
	}

	function OpenAdditionalImagesSection() {
		$str = '<div id="additionalImagesTitle"><h2>Additional Images</h2></div><div id="additionalImagesSection">';
		return $str;
	}

	function CloseAdditionalImagesSection() {
		$str = '</div><!-- End additionalImagesSection -->';
		return $str;
	}

/*	function OpenMultibuySection() {
		$str = '<div id="multibuyTitle"><h2>Multibuy Discount</h2></div><div id="additionalImagesSection">';
		return $str;
	}

	function CloseMultibuySection() {
		$str = '</div><!-- End additionalImagesSection -->';
		return $str;
	}*/

	function OpenPackageOverviewSection() {
		$str = '<div id="overviewTitle"><h2>Package Overview</h2></div><div id="packageDetailsOverviewSection">';
		return $str;
	}

	function ClosePackageOverviewSection() {
		$str = '</div><!-- End productDetailsOverviewSection -->';
		return $str;
	}

	function OpenProductSimilarSection($productName='') {
		$str = '<div id="similarTitle"><h2>'.$productName.' You may also be interested in...</h2></div><div id="productDetailsSimilarSection">';
		return $str;
	}

	function CloseProductSimilarSection() {
		$str = '</div><!-- End productDetailsSimilarSection -->';
		return $str;
	}

	function OpenProductRelatedSection($productName='this item') {
		$str = '<div id="relatedTitle"><h2>Customers who bought '.$productName.' also bought...</h2></div><div id="productDetailsRelatedSection">';
		return $str;
	}

	function CloseProductRelatedSection() {
		$str = '</div><!-- End productDetailsRelatedection -->';
		return $str;
	}

	function OpenProductReviewSection($productName='this item') {
		$str = '<div id="reviewTitle"><h2>'.$productName.' Reviews</h2></div><div id="productDetailsReviewSection">';
		return $str;
	}

	function CloseProductReviewSection() {
		$str = '</div><!-- End productDetailsReviewSection -->';
		return $str;
	}

	function OpenCategoryViewProductContainer() {
		$str = '<div class="categoryViewProductContainer">';
		return $str;
	}

	function CloseCategoryViewProductContainer() {
		$str = '</div> <!-- End categoryViewProductContainer -->';
		return $str;
	}

	function OpenCategoryViewProductImageContainer() {
		$str = '<div class="categoryViewProductImageContainer">';
		return $str;
	}

	function CloseCategoryViewProductImageContainer() {
		$str = '</div> <!-- End categoryViewProductImageContainer -->';
		return $str;
	}

	function OpenCatProductDetailsContainer() {
		$str = '<div class="productDetailsContainer">';
		return $str;
	}

	function CloseCatProductDetailsContainer() {
		$str = '</div> <!-- End productDetailsContainer -->';
		return $str;
	}

	function OpenCategoryViewButtonsContainer() {
		$str = '<div class="categoryViewButtonsContainer">';
		return $str;
	}

	function CloseCategoryViewButtonsContainer() {
		$str = '</div> <!-- End categoryViewButtonsContainer -->';
		return $str;
	}





	function ImageHref($image) {
		return $this->mSmallImageDir.$image->GetFilename ();
	}

	//! Given a product, generates an absolute directory-based URL for it
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @return String - The URL for the product
	 */
	function LoadLinkHref($product,$extra=false) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mProduct = $product;
		$allCategories = $this->mProduct->GetCategories();
		// If the product is in more than one category this could link to the wrong category...
		if (count ( $allCategories ) == 0) {
			return '#';
		}
		$this->mCategory = $allCategories [0];
		$hasParentCategory = $this->mCategory->GetParentCategory ();
		// Start URL
		$href = $this->mBaseDir;
		// Indicate within a category
		$href .= '/department';
		// If the product is in a sub category, then need to include IT's parent to avoid duplicate content
		if ($hasParentCategory) {
			$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
		}
		// Add the direct parent category
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/';
		// Indicate loading a product
		$href .= 'product/';
		// Product name
		$href .= $this->mValidationHelper->MakeLinkSafe ( $this->mProduct->GetDisplayName () );
		if($extra) {
			$href .= $this->mValidationHelper->MakeLinkSafe($extra);
		}
		// Product ID
		$href .= '/' . $this->mProduct->GetProductId ();
		return $href;
	}

	//! Given an sku, generates an absolute directory-based URL for it
	/*!
     * @param $sku [in] SkuModel - The sku you want a link to
	 * @return String - The URL for the sku
	 */
	function LoadSkuLinkHref($sku,$extra=false) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mProduct = $sku->GetParentProduct();
		$allCategories = $this->mProduct->GetCategories ();
		// If the product is in more than one category this could link to the wrong category...
		if (count ( $allCategories ) == 0) {
			return '#';
		}
		$this->mCategory = $allCategories [0];
		$hasParentCategory = $this->mCategory->GetParentCategory ();
		// Start URL
		$href = $this->mBaseDir;
		// Indicate within a category
		$href .= '/department';
		// If the product is in a sub category, then need to include IT's parent to avoid duplicate content
		if ($hasParentCategory) {
			$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
		}
		// Add the direct parent category
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/';
		// Indicate loading a product
		$href .= 'sku/';
		// Product name
		$href .= $this->mValidationHelper->MakeLinkSafe ( $this->mProduct->GetDisplayName () );
		if($extra) {
			$href .= $this->mValidationHelper->MakeLinkSafe($extra);
		}
		// Product ID
		$href .= '/' . $sku->GetSkuId();
		return $href;
	}

	//! Given a package, generates an absolute directory-based URL for it
	/*!
     * @param $product [in] PackageModel - The package you want a link to
	 * @return String - The URL for the product
	 */
	function LoadPackageLinkHref($package) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mPackage = $package;
		// If the package is in more than one category this could link to the wrong category...
		$this->mCategory = $this->mPackage->GetParentCategory ();
		// Start URL
		$href = $this->mBaseDir;
		// Indicate within a category
		$href .= '/packages';
		// Add the direct parent category
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/';
		// Indicate loading a product
		$href .= 'package/';
		// Product name
		$href .= $this->mValidationHelper->MakeLinkSafe ( $this->mPackage->GetDisplayName () );
		// Product ID
		$href .= '/' . $this->mPackage->GetPackageId ();
		return $href;
	}

	//! Given a manufacturer, generates an absolute directory-based URL for it
	/*!
     * @param $manufacturer [in] ManufacturerModel - The manufacturer you want a link to
	 * @return String - The URL for the manufacturer
	 */
	function LoadManufacturerHref($manufacturer) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mManufacturer = $manufacturer;
		// Start URL
		$href = $this->mBaseDir;
		// Indicate a brand
		$href .= '/brand';
		// Add the brand name
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mManufacturer->GetDisplayName () );
		// Manufacturer ID
		$href .= '/' . $this->mManufacturer->GetManufacturerId ();
		return $href;
	}

	//! Given a manufacturer, generates an absolute directory-based URL for its size chart page
	/*!
     * @param $manufacturer [in] ManufacturerModel - The manufacturer you want a link to
	 * @return String - The URL for the manufacturer
	 */
	function LoadSizeChartHref($manufacturer) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mManufacturer = $manufacturer;
		// Start URL
		$href = $this->mBaseDir;
		// Indicate a brand
		$href .= '/content';
		// Add the brand ID
		$href .= '/' . $this->mManufacturer->GetSizeChart ()->GetContentId ();
		// Manufacturer name
		$href .= '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mManufacturer->GetDisplayName () );
		return $href;
	}

	//! Given a manufacturer, generates the <img /> code to display the manufacturer's logo, complete with ALT text and an ID if desired
	/*!
     * @param $manufacturer [in] ManufacturerModel - The manufacturer you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function ManufacturerImage($manufacturer, $id = '') {
		// Set optional ID
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		// Does the manufacturer HAVE an image?
		if ($manufacturer->GetImage ()) {
			$image = $manufacturer->GetImage ();
			if (! is_null ( $manufacturer )) {
			#	die('before');
			#	getimagesize ( $this->mRootDir . $this->mManufacturerImageDir . $image->GetFilename () );
			#	die('after');

				// Display the image if it seems valid (IE. if getimagesize can deal with it, the browser can probably display it)
				if ($size = @getimagesize ( $this->mManufacturerImageDir . $image->GetFilename () )) {
					$str = '<img src="' .$this->mBaseDir.'/' .$this->mManufacturerImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="175" height="80" />';

				} else {
					$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" width="175" height="80" />';
				}
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" width="175" height="80" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" width="175" height="80" />';
		}
		return $str;
	}

	//! Given a tag, generates the <img /> code to display the tag's logo, complete with ALT text and an ID if desired
	/*!
     * @param $tag [in] TagModel - The tag you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function TagImage($tag, $id = '') {
		// Set optional ID
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		// Does the manufacturer HAVE an image?
		if ($tag->GetImage()) {
			$image = $tag->GetImage();
			if (! is_null ( $tag )) {
				// Display the image if it seems valid (IE. if getimagesize can deal with it, the browser can probably display it)
				if ($size = @getimagesize ( $this->mRegistry->tagImageDir.$image->GetFilename () )) {
					$str = '<img src="' . $this->mRegistry->tagImageDir.$image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" />';
				} else {
					$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" />';
				}
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableManufacturer.gif" ' . $id . ' alt="No Image Available" />';
		}
		return $str;
	}

	//! Given a article, generates the <img /> code to display the article's image, complete with ALT text and an ID if desired
	/*!
     * @param $tag [in] ContentModel - The article you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function ArticleThumbImage($content, $id = '') {
		// Set optional ID
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		// Does the manufacturer HAVE an image?
		if ($content->GetThumbImage()) {
			$image = $content->GetThumbImage();
			if (! is_null ( $content )) {
				// Display the image if it seems valid (IE. if getimagesize can deal with it, the browser can probably display it)
				if ($size = @getimagesize ( $this->mRegistry->contentImageDir.$image->GetFilename () )) {
					$str = '<img src="' . $this->mRegistry->contentImageDir.$image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" style="float: left; margin: 10px;" />';
				} else {
					$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
				}
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
		}
		return $str;
	}

	//! Given a article, generates the <img /> code to display the article's image, complete with ALT text and an ID if desired
	/*!
     * @param $tag [in] ContentModel - The article you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function ArticleHeaderImage($content, $id = '') {
		// Set optional ID
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		// Does the manufacturer HAVE an image?
		if ($content->GetHeaderImage()) {
			$image = $content->GetHeaderImage();
			if (! is_null ( $content )) {
				// Display the image if it seems valid (IE. if getimagesize can deal with it, the browser can probably display it)
				if ($size = getimagesize ( $this->mRegistry->contentImageDir.$image->GetFilename () )) {
					$str = '<img src="' . $this->mRegistry->contentImageDir.$image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" style="float: left; margin: 10px;" />';
				} else {
					$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
				}
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailableArticle.gif" ' . $id . ' alt="No Image Available" style="float: left; margin: 10px;" />';
		}
		return $str;
	}

	//! Given a product, generates the <img /> code to display the small product image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function SmallProductImage($product, $id = '') {
		$image = $product->GetMainImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = getimagesize ( '' . $this->mSmallImageDir . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mSmallImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="100" height="100" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" width="100" height="100" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" width="100" height="100" />';
		}
		return $str;
	}

	function LargeImagePath($image) {
		return $this->mLargeImageDir . '/' . $image->GetFilename ();
	}

	function SmallImage($image, $id = '') {
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = getimagesize ( '' . $this->mSmallImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mSmallImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" />';
		}
		return $str;
	}

	//! Given a product, generates the <img /> code to display the medium product image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function MediumProductImage($product, $id = '') {
		$image = $product->GetMainImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ($this->mMediumImageDir . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mMediumImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="140" height="140" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable140.gif" ' . $id . ' alt="No Image Available" width="140" height="140" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable140.gif" ' . $id . ' alt="No Image Available" width="140" height="140" />';
		}
		return $str;
	}

	//! Given a product, generates the <img /> code to display the large product image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function LargeProductImage($product, $id = '') {
		$image = $product->GetMainImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mLargeImageDir . $image->GetFilename () )) {
				$str = '<img src="'. $this->mBaseDir.'/' . $this->mLargeImageDir . $image->GetFilename () . '" ' . $id . ' alt="' . $product->GetDisplayName
				 () . '" width="300" height="300" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" width="300" height="300" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" width="300" height="300" />';
		}
		return $str;
	}

	//! Given a product, generates the <img /> code to display whichever is larger - the large or original image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function LargestProductImage($product, $id = '') {
		$image = $product->GetMainImage ();
		if($image) {
			$originalWidth = $image->GetOriginalWidth($this->mOriginalImageDir . '/');
			$largeWidth = $this->mRegistry->largeImageSize;
			if($originalWidth>$largeWidth) {
				$retStr = $this->OriginalProductImage($product,$id);
			} else {
				$retStr = $this->LargeProductImage($product,$id);
			}
		} else {
			$retStr = $this->LargeProductImage($product,$id);
		}
		return $retStr;
	}

	//! Given a product, generates the <img /> code to display the original product image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] ProductModel - The product you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function OriginalProductImage($product, $id = '') {
		$image = $product->GetMainImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mOriginalImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mOriginalImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" border="0" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
		}
		return $str;
	}

	//! Given a package, generates the <img /> code to display the small package image, complete with ALT text and an ID if desired
	/*!
     * @param $package [in] PackageModel - The package you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function SmallPackageImage($package, $id = '') {
		$image = $package->GetImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( '' . $this->mSmallImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mSmallImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="100" height="100" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" width="100" height="100" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable100.gif" ' . $id . ' alt="No Image Available" width="100" height="100" />';
		}
		return $str;
	}

	//! Given a package, generates the <img /> code to display the medium package image, complete with ALT text and an ID if desired
	/*!
     * @param $package [in] PackageModel - The package you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function MediumPackageImage($package, $id = '') {
		$image = $package->GetImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mMediumImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mMediumImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="140" height="140" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable140.gif" ' . $id . ' alt="No Image Available" width="140" height="140" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable140.gif" ' . $id . ' alt="No Image Available" width="140" height="140" />';
		}
		return $str;
	}

	//! Given a package, generates the <img /> code to display the large package image, complete with ALT text and an ID if desired
	/*!
     * @param $package [in] PackageModel - The package you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function LargePackageImage($package, $id = '') {
		$image = $package->GetImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mLargeImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mLargeImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" width="300" height="300" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" width="300" height="300" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" width="300" height="300" />';
		}
		return $str;
	}

	//! Given a package, generates the <img /> code to display whichever is larger - the large or original image, complete with ALT text and an ID if desired
	/*!
     * @param $product [in] PackageModel - The package you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function LargestPackageImage($package, $id = '') {
		$image = $package->GetImage ();
		if(!$image) {
			return null;
		}
 		$originalWidth = $image->GetOriginalWidth($this->mOriginalImageDir . '/');
		$largeWidth = $this->mRegistry->largeImageSize;
		if($originalWidth>$largeWidth) {
			$retStr = $this->OriginalPackageImage($package,$id);
		} else {
			$retStr = $this->LargePackageImage($package,$id);
		}
		return $retStr;
	}

	//! Given a package, generates the <img /> code to display the original package image, complete with ALT text and an ID if desired
	/*!
     * @param $package [in] PackageModel - The package you want a link to
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function OriginalPackageImage($package, $id = '') {
		$image = $package->GetImage ();
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mOriginalImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' .$this->mBaseDir.'/' . $this->mOriginalImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
		}
		return $str;
	}

	//! Given an image, generates the <img /> code to display the original image, complete with ALT text and an ID if desired
	/*!
     * @param $image [in] ImageModel - The image you want to display
	 * @param $id [in] String - An id if desired for the id attribute. Can be used if DOM manipulation is needed (optional)
	 * @return String - The full <img /> code
	 */
	function GetImageCode($image, $id = '') {
		if ($id == '') {
			$id = '';
		} else {
			$id = 'id="' . $id . '"';
		}
		if (! is_null ( $image )) {
			if ($size = @getimagesize ( $this->mBaseDir .'/'. $this->mLargeImageDir . '/' . $image->GetFilename () )) {
				$str = '<img src="' . $this->mBaseDir .'/'. $this->mLargeImageDir . '/' . $image->GetFilename () . '" ' . $id . ' alt="' . $image->GetAltText () . '" />';
			} else {
				$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
			}
		} else {
			$str = '<img src="' . $this->mBaseDir . '/images/noImageAvailable300.gif" ' . $id . ' alt="No Image Available" />';
		}
		return $str;
	}

}

?>