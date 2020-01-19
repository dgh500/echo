<?php
// Simple AJAX handler, just echo's the report (as HTML) itself rather than XML to be processed


header ( "Cache-Control: no-cache" );
set_time_limit ( 5000 );
$disableJavascriptAutoload = 1;
include_once ('../autoload.php');

$adminReports = new AdminReports ( );
$adminReports->LoadStyles ();

switch ($_POST ['reportType']) {
	case 'sageCodes' :
		$adminReports->GetSageCodes ();
		break;
	case 'images' :
		$adminReports->GetImages ();
		break;
	case 'descriptions' :
		$adminReports->GetDescriptions ();
		break;
	case 'price' :
		$adminReports->GetPrices ();
		break;
	case 'postage' :
		$adminReports->GetPostages ();
		break;
	case 'weight' :
		$adminReports->GetWeights ();
		break;
	case 'emptyCategories' :
		$adminReports->GetEmptyCategories ();
		break;
	case 'dodgyOptions' :
		$adminReports->GetDodgyOptions ();
		break;
	case 'dodgySage' :
		$adminReports->GetDodgySage ();
		break;
	case 'authorisedOrders' :
		$adminReports->GetOrdersOnOrder ();
		break;
	case 'missingSizes' :
		$adminReports->GetMissingSizes ();
		break;
	case 'allProducts' :
		$adminReports->GetAllProducts ();
		break;
	case 'allSkus' :
		$adminReports->GetAllSkus ();
		break;
	case 'ordersFromCatalogue' :
		$adminReports->GetOrdersFromCatalogue ();
		break;
	case 'relatedSimilar' :
		$adminReports->RelatedSimilar ();
		break;
	case 'zeroSkuProducts' :
		$adminReports->ZeroSkuProducts ();
		break;
	case 'topProducts' :
		$adminReports->TopProducts ();
		break;
	case 'referrer' :
		$adminReports->GetReferrersFromCatalogue ();
		break;
	case 'poorStockLevels':
		$adminReports->GetPoorStockLevels();
		break;
	case 'notInStacks':
		$adminReports->GetNotInStacks();
		break;
	case 'mostCancelled':
		$adminReports->GetMostCancelled();
		break;
	case 'topBrandsAll':
		$adminReports->GetTopBrands(false);
		break;
	case 'topBrandsExcl':
		$adminReports->GetTopBrands(true);
		break;
	case 'topStacks':
		$adminReports->GetTopStacks();
		break;
	case 'outOfStock':
		$adminReports->GetOutOfStock();
		break;
}

class AdminReports {

	// VERY dubious direct-use of _POST vars
	function __construct() {
		$this->mValidationHelper 		= new ValidationHelper ( );
		$this->mPresentationHelper		= new PresentationHelper;
		$this->mPackageController		= new PackageController;
		$this->mProductController 		= new ProductController ( );
		$this->mCategoryController 		= new CategoryController ( );
		$this->mOrderController 		= new OrderController ( );
		$this->mSkuController 			= new SkuController ( );
		$this->mCatalogueController 	= new CatalogueController ( );
		$this->mManufacturerController	= new ManufacturerController;

		// Get the date constraints ( dd/mm/yyyy format )
		$startDate = $_POST ['startDate'];
		$endDate = $_POST ['endDate'];

		// Make this into a format mktime() can use
		$startDateArr = explode ( '/', $startDate );
		$endDateArr = explode ( '/', $endDate );

		// Convert them to UNIX timestamps
		$this->mStartTimestamp = mktime ( 0, 0, 0, intval ( $startDateArr [1] ), intval ( $startDateArr [0] ), intval ( $startDateArr [2] ) );
		$this->mEndTimestamp = mktime ( 0, 0, 0, intval ( $endDateArr [1] ), intval ( $endDateArr [0] ), intval ( $endDateArr [2] ) );

		// This generates a comma-seperated list of catalogue IDs for use in a SQL statement
		if ($_POST ['catalogue'] == 'ALL') {
			$this->mCatalogueName = 'All Catalogues';
			$this->mCatalogueList = '';
			foreach ( $this->mCatalogueController->GetAllCatalogues () as $catalogue ) {
				$this->mCatalogueList .= $catalogue->GetCatalogueId () . ', ';
			}
			$this->mCatalogueList = substr ( $this->mCatalogueList, 0, strlen ( $this->mCatalogueList ) - 2 );
		} else {
			$catalogue = new CatalogueModel ( $_POST ['catalogue'] );
			$this->mCatalogueName = $catalogue->GetDisplayName ();
			$this->mCatalogueList = $_POST ['catalogue'];
		}
	}

	//! Load the styles for top/mid/product level displays
	function LoadStyles() {
		echo '	<style>
					.topLevel {
						width: 150px;
						height: 17px;
						float: left;
						overflow: hidden;
					}
					.midLevel {
						width: 150px;
						height: 17px;
						float: left;
						overflow: hidden;
					}
					.productLevel {
						width: 350px;
						float: left;
					}
				</style>';
	}

	//! Returns those products that no images, or whose images are broken
	/*!
	 * @return Void
	 */
	function GetImages() {
		$noImages = $this->mProductController->GetMissingImages ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>Missing Images for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noImages as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		$allImages = $this->mProductController->GetAllProductsInCatalogue ( $this->mCatalogueList, $this->mStartTimestamp, $this->mEndTimestamp );
		foreach ( $allImages as $product ) {
			$image = $product->GetMainImage ();
			if ($image) {
				$categories = $product->GetCategories ();
				$gif_filename = str_replace ( 'jpeg', 'gif', $image->GetFilename () );
				if (! @getimagesize ( 'http://212.78.85.25/uploadImages/small/' . $image->GetFilename () )) {
					echo '|__| ' . $categories [0]->GetDisplayName () . ' > ' . $product->GetDisplayName () . '<br />';
				}
				if (file_exists ( 'http://212.78.85.25/uploadImages/small/' . $gif_filename )) {
					echo '|__| ' . $categories [0]->GetDisplayName () . ' > ' . $product->GetDisplayName () . '<br />';
				}
			}
		}
		echo '</span>';
	}

	//! Returns those products that have no desciptions
	/*!
	 * @return Void
	 */
	function GetDescriptions() {
		$noDescriptions = $this->mProductController->GetMissingDescriptions ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noDescriptions ) . ' Missing Descriptions for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noDescriptions as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that have a price of zero
	/*!
	 * @return Void
	 */
	function GetPrices() {
		$noPrices = $this->mProductController->GetMissingPrices ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noPrices ) . ' Missing Prices for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noPrices as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that have a postage of zero
	//////////// HACK - NOW GETS THOSE WHICH ARE MARKED AS NOT FOR SALE ///////////////
	/*!
	 * @return Void
	 */
	function GetPostages() {
		$noPostages = $this->mProductController->GetMissingPostages ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noPostages ) . ' Missing Postage for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noPostages as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that have a weight of zero
	/*!
	 * @return Void
	 */
	function GetWeights() {
		$noWeight = $this->mProductController->GetMissingWeights ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noWeight ) . ' Missing Weight for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noWeight as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that have NO SKUs (because of a bug?) and therefore can't be bought or deleted
	/*!
	 * @return Void
	 */
	function ZeroSkuProducts() {
		$noSkus = $this->mProductController->GetZeroSkuProducts ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noSkus ) . ' Missing SKUs for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noSkus as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that are out of stock
	/*!
	 * @return Void
	 */
	function GetOutOfStock() {
		$skus = $this->mSkuController->GetOutOfStockSkus ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $skus ) . ' Out of Stock Products for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $skus as $sku ) {
			if($sku->GetParentProduct()->GetForSale()) {
				echo '|__| ' . $sku->GetParentProduct()->GetDisplayName () . ' '.$sku->GetSkuAttributesList(). '<br />';
			} else {
				echo '|__| <em><strong>' . $sku->GetParentProduct()->GetDisplayName () . ' '.$sku->GetSkuAttributesList(). '</strong></em><br />';
			}
		}
		echo '</span>';
	}

	//! Returns those categories which have no products in them
	/*!
	 * @return Void
	 */
	function GetEmptyCategories() {
		$emptyCats = $this->mCategoryController->GetEmptyCategories ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $emptyCats ) . ' Empty Categories for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $emptyCats as $category ) {
			if ($category->GetParentCategory ()) {
				echo '|__| ' . $category->GetParentCategory ()->GetDisplayName () . ' >' . $category->GetDisplayName () . '<br />';
			} else {
				echo '|__| ' . $category->GetDisplayName () . '<br />';
			}
		}
		echo '</span>';
	}

	//! Returns those products that have dubious options (Where the sytem used to break when adding over 10 at a time)
	/*!
	 * @return Void
	 */
	function GetDodgyOptions() {
		$dodgyOptions = $this->mProductController->GetDodgyOptions ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $dodgyOptions ) . ' Dodgy Options for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $dodgyOptions as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Returns those products that have dubious sage codes (numeric rather than textual)
	/*!
	 * @return Void
	 */
	function GetDodgySage() {
		$dodgySage = $this->mProductController->GetDodgySage ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $dodgySage ) . ' Dodgy Sage for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $dodgySage as $sku ) {
			$categories = $sku->GetParentProduct ()->GetCategories ();
			$product = $sku->GetParentProduct ();
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';

		}
		echo '</span>';
	}

	function GetPoorStockLevels() {
		$poorStock = $this->mProductController->GetPoorStock($this->mCatalogueList,$this->mStartTimestamp, $this->mEndTimestamp);
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $poorStock ) . ' Poor Stock Levels for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $poorStock as $sku ) {
			$categories = $sku->GetParentProduct ()->GetCategories ();
			$product = $sku->GetParentProduct ();
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '<div>|__| '.$product->GetDisplayName().' '.$sku->GetAttributeList().'</div>';

		}
		echo '</span>';
	}

	function GetMostCancelled() {
		$poorStock = $this->mProductController->GetMostCancelled($this->mCatalogueList,$this->mStartTimestamp,$this->mEndTimestamp);
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $poorStock ) . ' Most Cancelled for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $poorStock as $sku ) {
			$categories = $sku->GetParentProduct ()->GetCategories ();
			$product = $sku->GetParentProduct ();
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '<div>|__| '.$product->GetDisplayName().' '.$sku->GetAttributeList().'</div>';

		}
		echo '</span>';
	}

	function GetNotInStacks() {
		$products = $this->mProductController->GetNotInStacks($this->mCatalogueList);
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $products ) . ' Products Not In Stacks for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $products as $product ) {
			echo '<div>|__| '.$product->GetDisplayName().'</div>';
		}
		echo '</span>';
	}

	//! Returns those orders on order (that have had their dispatch date changed) in the catalogue(s) chosen
	function GetOrdersOnOrder() {
		$authOrders = $this->mOrderController->GetAuthorisedOrders ( false, $this->mStartTimestamp, $this->mEndTimestamp );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . $this->mCatalogueName . ' - On Order</strong><br /><br />';
		foreach ( $authOrders as $order ) {
			// Get some extra details
			$orderDate = date ( 'D jS \of F Y', $order->GetCreatedDate () );
			$customer = $order->GetCustomer ();
			$customerName = $customer->GetFirstName () . ' ' . $customer->GetLastName ();
			$catalogueIdArray = explode ( ',', $this->mCatalogueList );
			// Only show the orders the user wants to see
			if (in_array ( $order->GetCatalogue ()->GetCatalogueId (), $catalogueIdArray )) {
				echo '|__| <a href="http://www.echosupplements.com/wombat7/orders/' . $order->GetOrderId () . '">ECHO' . $order->GetOrderId () . '</a> on ' . $orderDate . ' by ' . $customerName . '<br />';
			}
		}
		echo '</span>';
	}

	function GetMissingSizes() {
		$missingSizes = $this->mProductController->GetMissingSizes ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $missingSizes ) . ' Missing Sizes for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $missingSizes as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	function GetAllProducts() {
		$allProducts = $this->mProductController->GetAllProducts ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allProducts ) . ' All Products for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $allProducts as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	function TopProducts() {
		$allSkus = $this->mSkuController->GetTopSkus ( $this->mCatalogueList, $this->mStartTimestamp, $this->mEndTimestamp );
		if (count ( $allSkus ) > 0) {
			echo '<span style="font-family: Arial; font-size: 10pt;">';
			echo '<strong>' . count ( $allSkus ['sku'] ) . ' Top Products for ' . $this->mCatalogueName . '</strong><br /><br />';
			foreach ( $allSkus ['sku'] as $sku ) {
				// Put together a 'combo' description
				$combo = $sku->GetSkuAttributesList ();
				$product = $sku->GetParentProduct ();
				$categories = $product->GetCategories ();
				if ($categories [0] && $categories [0]->GetParentCategory () != NULL) {
					$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
				} else {
					$topLevel = '';
				}
				echo '	<div class="printLineBreak"><strong>' . $allSkus ['count'] [$sku->GetSkuId ()] . '</strong> | ' . $product->GetDisplayName () . ' ' . $combo . '</div>';
			}
			echo '</span>';
		} else {
			echo '<strong>No Products sold for ' . $this->mCatalogueName . '</strong><br /><br />';
		}
	}

	//! Displays the top stacks
	function GetTopStacks() {
		$topPackages = $this->mPackageController->GetTopPackages( $this->mCatalogueList, $this->mStartTimestamp, $this->mEndTimestamp );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
	echo '<strong> Top Stacks for ' . $this->mCatalogueName . ' between '.date('D jS \of M Y',$this->mStartTimestamp).' and '.date('D jS \of M Y',$this->mEndTimestamp).'</strong><br><br>';
		for($i=0;$i<count($topPackages['packageId']);$i++) {
			$package = new PackageModel($topPackages['packageId'][$i]);
		echo '
			<div> <strong>'.$topPackages['saleCount'][$i].' </strong> '.$package->GetDisplayName().' </div>	';
		}
		echo '</span>';
	}

	function RelatedSimilar() {
		$allProducts = $this->mProductController->GetMissingRelatedSimilar ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allProducts ) . ' Missing Related/Similar Products for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $allProducts as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	//! Gets the top brands for the catalogues supplied
	function GetTopBrands($excludeSmallItems=false) {
		if($excludeSmallItems) { $msg = ' (Excluding Protein Bars) '; } else { $msg = ''; }
		$topBrands = $this->mManufacturerController->GetTopBrands($this->mCatalogueList,$this->mStartTimestamp,$this->mEndTimestamp,$excludeSmallItems);
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong> Top Brands for ' . $this->mCatalogueName . ' between '.date('D jS \of M Y',$this->mStartTimestamp).' and '.date('D jS \of M Y',$this->mEndTimestamp).' '.$msg.'</strong><br /><br />';
	echo '
		<div style="float: left; width: 70px; text-align: right;"> <strong><u>Value</u></strong> </div>
		<div style="float: left; width: 80px; text-align: center;"> <strong><u>Orders</u></strong> </div>
		<div style="float: left;"><strong><u>Brand</u></strong></div>
		<br style="clear: both" /><br>';

		for($i=0;$i<count($topBrands['manufacturer']);$i++) {
	echo '
		<div style="float: left; width: 70px; text-align: right;"> <strong>&pound;'.$this->mPresentationHelper->Money($topBrands['orderValue'][$i]).' </strong> </div>
		<div style="float: left; width: 80px; text-align: center;"> <strong>'.$topBrands['productCount'][$i].' </strong> </div>
		<div style="float: left;">'.$topBrands['manufacturer'][$i].'</div>
		<br style="clear: both" />';
		}
		echo '</span>';
	} // End GetTopBrands

	function GetSageCodes() {
		$noSageCodes = $this->mProductController->GetMissingSageCodes ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noSageCodes ) . ' Missing Sage Codes for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $noSageCodes as $product ) {
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div><br style="clear: both" />';
		}
		echo '</span>';
	}

	function GetOrdersFromCatalogue() {
		try {
			$allOrders = $this->mOrderController->GetOrders ( $this->mCatalogueList, $this->mStartTimestamp, $this->mEndTimestamp );
		} catch(Exception $e) {
			die($e->getMessage());
		}
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allOrders ) . ' Orders for ' . $this->mCatalogueName . ' in between ' . date ( 'l jS \of F Y', $this->mStartTimestamp ) . ' and ' . date ( 'l jS \of F Y', $this->mEndTimestamp ) . '</strong><br /><br />';
		$authCount = 0;
		$inTransitCount = 0;
		$cancelledCount = 0;
		$totalTaken = 0;
		foreach ( $allOrders as $order ) {
			if ($order->GetStatus ()->IsInTransit ()) {
				$inTransitCount ++;
				$totalTaken += $order->GetActualTaken ();
			}
			if ($order->GetStatus ()->IsAuthorised ()) {
				$authCount ++;
			}
			if ($order->GetStatus ()->IsCancelled ()) {
				$cancelledCount ++;
			}
			// Get some extra details
			$orderDate = date ( 'D jS \of F Y', $order->GetCreatedDate () );
			$customer = $order->GetCustomer ();
			$customerName = $customer->GetFirstName () . ' ' . $customer->GetLastName ();

			echo '|__| <a href="https://www.echosupplements.com/admin/orders/' . $order->GetOrderId () . '">ECHO' . $order->GetOrderId () . '</a> on ' . $orderDate . ' by ' . $customerName . '<br />';
		}
		echo '<br /><strong>Totals: </strong> In Transit: ' . $inTransitCount . ' | Authorised: ' . $authCount . ' | Cancelled: ' . $cancelledCount . '<br /><strong>Total Taken:</strong> £' . number_format ( $totalTaken, 2, '.', ',' ) . '';
		echo '</span>';
	}

	function GetReferrersFromCatalogue() {
		try {
			$allReferrers = $this->mOrderController->GetReferrers ( $this->mCatalogueList, $this->mStartTimestamp, $this->mEndTimestamp );
		} catch(Exception $e) {
			echo $e->getMessage();
		}
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>Referrers for ' . $this->mCatalogueName . ' in between ' . date ( 'l jS \of F Y', $this->mStartTimestamp ) . ' and ' . date ( 'l jS \of F Y', $this->mEndTimestamp ) . '</strong><br /><br />';
		foreach ( $allReferrers as $referrerId => $referrerInfo ) {
			$referrer = new ReferrerModel ( $referrerId );
			echo $referrerInfo ['referrerCount'] . ' - ' . $referrer->GetDescription () . ' -  &pound;' . $referrerInfo ['totalSpend'] . '<br />';
		}
		echo '</span>';
	}

	function GetAllSkus() {
		$allSkus = $this->mSkuController->GetAllSkus ( $this->mCatalogueList );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allSkus ) . ' All Products for ' . $this->mCatalogueName . '</strong><br /><br />';
		foreach ( $allSkus as $sku ) {
			$attributes = $sku->GetSkuAttributes ();
			$combo = '';
			foreach ( $attributes as $attribute ) {
				$combo .= $attribute->GetAttributeValue () . ' ';
			}
			if ($combo != '') {
				$combo = '<strong>(' . trim ( $combo ) . ')</strong>';
			}
			$product = $sku->GetParentProduct ();
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div class="topLevel">|__| ' . $topLevel . '</div>
					<div class="midLevel">' . $categories [0]->GetDisplayName () . ' </div>
					<div class="productLevel">' . $product->GetDisplayName () . '</div>' . $combo . '<br style="clear: both" />';
		}
		echo '</span>';
	}
}
?>