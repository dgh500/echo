<?php
set_time_limit ( 500 );
require_once ('../autoload.php');

foreach ( $_POST as $key => $value ) {
	echo '<strong>' . $key . ':</strong> ' . $value . '<br />';
}

class AdminMissingHandler {

	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mProductController = new ProductController ( );
		$this->mCategoryController = new CategoryController ( );
		$this->mOrderController = new OrderController ( );
		$this->mSkuController = new SkuController ( );
	}

	function Process($postArr) {
		$this->LoadStyles ();
		$this->mCatalogue = new CatalogueModel ( $postArr ['catalogue'] );
		switch ($postArr ['searchForMissing']) {
			case 'sageCodes' :
				$this->GetSageCodes ();
				break;
			case 'images' :
				$this->GetImages ();
				break;
			case 'descriptions' :
				$this->GetDescriptions ();
				break;
			case 'price' :
				$this->GetPrices ();
				break;
			case 'postage' :
				$this->GetPostages ();
				break;
			case 'weight' :
				$this->GetWeights ();
				break;
			case 'emptyCategories' :
				$this->GetEmptyCategories ();
				break;
			case 'dodgyOptions' :
				$this->GetDodgyOptions ();
				break;
			case 'dodgySage' :
				$this->GetDodgySage ();
				break;
			case 'authorisedOrders' :
				$this->GetAuthorisedOrders ();
				break;
			case 'missingSizes' :
				$this->GetMissingSizes ();
				break;
			case 'allProducts' :
				$this->GetAllProducts ();
				break;
			case 'allSkus' :
				$this->GetAllSkus ();
				break;
			case 'ordersFromCatalogue' :
				$this->GetOrdersFromCatalogue ( $postArr ['nDays'] );
				break;
			case 'relatedSimilar' :
				$this->RelatedSimilar ();
				break;
			case 'zeroSkuProducts' :
				$this->ZeroSkuProducts ();
				break;
			case 'topProducts' :
				$this->TopProducts ();
				break;
			case 'referrer' :
				$this->GetReferrersFromCatalogue ( $postArr ['nRefDays'] );
				break;
		}
	}

	function LoadStyles() {
		echo '	<style>
					.topLevel {
						width: 200px;
						float: left;
					}
					.midLevel {
						width: 200px;
						float: left;
					}
					.productLevel {
						width: 250px;
						float: left;
					}
				</style>';
	}

	function GetImages() {
		$noImages = $this->mProductController->GetMissingImages ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>Missing Images for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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
		$allImages = $this->mProductController->GetAllProductsInCatalogue ( $this->mCatalogue );
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

	function GetDescriptions() {
		$noDescriptions = $this->mProductController->GetMissingDescriptions ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noDescriptions ) . ' Missing Descriptions for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetPrices() {
		$noPrices = $this->mProductController->GetMissingPrices ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noPrices ) . ' Missing Prices for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetPostages() {
		$noPostages = $this->mProductController->GetMissingPostages ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noPostages ) . ' Missing Postage for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetWeights() {
		$noWeight = $this->mProductController->GetMissingWeights ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noWeight ) . ' Missing Weight for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function ZeroSkuProducts() {
		$noSkus = $this->mProductController->GetZeroSkuProducts ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noSkus ) . ' Missing SKUs for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetEmptyCategories() {
		$emptyCats = $this->mCategoryController->GetEmptyCategories ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $emptyCats ) . ' Empty Categories for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
		foreach ( $emptyCats as $category ) {
			if ($category->GetParentCategory ()) {
				echo '|__| ' . $category->GetParentCategory ()->GetDisplayName () . ' >' . $category->GetDisplayName () . '<br />';
			} else {
				echo '|__| ' . $category->GetDisplayName () . '<br />';
			}
		}
		echo '</span>';
	}

	function GetDodgyOptions() {
		$dodgyOptions = $this->mProductController->GetDodgyOptions ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $dodgyOptions ) . ' Dodgy Options for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetDodgySage() {
		$dodgySage = $this->mProductController->GetDodgySage ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $dodgySage ) . ' Dodgy Sage for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetAuthorisedOrders() {
		$authOrders = $this->mOrderController->GetAuthorisedOrders ();
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . $this->mCatalogue->GetDisplayName () . ' - On Order</strong><br /><br />';
		foreach ( $authOrders as $order ) {
			$orderDate = date ( 'D jS \of F Y', $order->GetCreatedDate () );
			$customer = $order->GetCustomer ();
			$customerName = $customer->GetFirstName () . ' ' . $customer->GetLastName ();
			if ($order->GetCatalogue ()->GetCatalogueId () == $this->mCatalogue->GetCatalogueId ()) {
				echo '|__| <a href="http://www.echosupplements.com/admin/orders/' . $order->GetOrderId () . '">ECHO' . $order->GetOrderId () . '</a> on ' . $orderDate . ' by ' . $customerName . '<br />';
			}
		}
		echo '</span>';
	}

	function GetMissingSizes() {
		$missingSizes = $this->mProductController->GetMissingSizes ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $missingSizes ) . ' Missing Sizes for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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
		$allProducts = $this->mProductController->GetAllProducts ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allProducts ) . ' All Products for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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
		$allSkus = $this->mSkuController->GetTopSkus ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allSkus ['sku'] ) . ' Top Products for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
		foreach ( $allSkus ['sku'] as $sku ) {
			$attributes = $sku->GetSkuAttributes ();
			$combo = '';
			foreach ( $attributes as $attribute ) {
				$combo .= $attribute->GetAttributeValue () . ' ';
			}
			if ($combo != '') {
				$combo = ' - (' . trim ( $combo ) . ')';
			}
			$product = $sku->GetParentProduct ();
			$categories = $product->GetCategories ();
			if ($categories [0]->GetParentCategory () != NULL) {
				$topLevel = $categories [0]->GetParentCategory ()->GetDisplayName () . ' > ';
			} else {
				$topLevel = '';
			}
			echo '	<div><strong>' . $allSkus ['count'] [$sku->GetSkuId ()] . '</strong> | ' . $product->GetDisplayName () . '' . $combo . '</div>';
		}
		echo '</span>';
	}

	function RelatedSimilar() {
		$allProducts = $this->mProductController->GetMissingRelatedSimilar ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allProducts ) . ' Missing Related/Similar Products for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetSageCodes() {
		$noSageCodes = $this->mProductController->GetMissingSageCodes ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $noSageCodes ) . ' Missing Sage Codes for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

	function GetOrdersFromCatalogue($nDays) {
		$allOrders = $this->mOrderController->GetNDaysOrders ( $nDays, $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allOrders ) . ' Orders for ' . $this->mCatalogue->GetDisplayName () . ' in the last ' . $nDays . ' days</strong><br /><br />';
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
			echo '|__| <a href="https://www.echosupplements.com/admin/orders/' . $order->GetOrderId () . '">ECHO' . $order->GetOrderId () . '</a><br />';
		}
		echo '<br /><strong>Totals: </strong> In Transit: ' . $inTransitCount . ' | Authorised: ' . $authCount . ' | Cancelled: ' . $cancelledCount . '<br /><strong>Total Taken:</strong> £' . number_format ( $totalTaken, 2, '.', ',' ) . '';
		echo '</span>';
	}

	function GetReferrersFromCatalogue($nDays) {
		try {
			$allReferrers = $this->mOrderController->GetNDaysReferrers ( $nDays, $this->mCatalogue );
		} catch(Exception $e) {
			die($e->getMessage());
		}
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>Referrers for ' . $this->mCatalogue->GetDisplayName () . ' in the last ' . $nDays . ' days</strong><br /><br />';
		arsort ( $allReferrers );
		foreach ( $allReferrers as $referrerId => $count ) {
			$referrer = new ReferrerModel ( $referrerId );
			echo $count . ' - ' . $referrer->GetDescription () . '<br />';
		}
		echo '</span>';
	}

	function GetAllSkus() {
		$allSkus = $this->mSkuController->GetAllSkus ( $this->mCatalogue );
		echo '<span style="font-family: Arial; font-size: 10pt;">';
		echo '<strong>' . count ( $allSkus ) . ' All Products for ' . $this->mCatalogue->GetDisplayName () . '</strong><br /><br />';
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

} // End class


try {
	$handler = new AdminMissingHandler ( );
	$handler->Process ( $_POST );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>