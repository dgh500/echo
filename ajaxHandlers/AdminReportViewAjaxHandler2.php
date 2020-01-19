<?php
// Simple AJAX handler, just echo's the report (as HTML) itself rather than XML to be processed
header ( "Cache-Control: no-cache" );
set_time_limit ( 5000 );
$disableJavascriptAutoload = 1;
include_once ('../autoload.php');


class AdminReportAjaxHandler2 {

	function __construct() {
		$this->mRegistry				= Registry::getInstance();
		$this->mValidationHelper 		= new ValidationHelper;
		$this->mPresentationHelper		= new PresentationHelper;
		$this->mCatalogueController		= new CatalogueController;
		$this->mCategoryController	 	= new CategoryController;
		$this->mManufacturerController	= new ManufacturerController;
		$this->mProductController		= new ProductController;

		/* Get the date constraints ( dd/mm/yyyy format )
		$startDate = $_POST ['startDate'];
		$endDate = $_POST ['endDate'];

		// Make this into a format mktime() can use
		$startDateArr = explode ( '/', $startDate );
		$endDateArr = explode ( '/', $endDate );

		// Convert them to UNIX timestamps
		$this->mStartTimestamp = mktime ( 0, 0, 0, intval ( $startDateArr [1] ), intval ( $startDateArr [0] ), intval ( $startDateArr [2] ) );
		$this->mEndTimestamp = mktime ( 0, 0, 0, intval ( $endDateArr [1] ), intval ( $endDateArr [0] ), intval ( $endDateArr [2] ) );*/

	} // End __construct()

	//! Handles the event "catalogue chosen"
	/*!
	 * @return String - The HTML for the 'choose category' and 'choose manufacturer' container
	 */
	function EventCatalogueChosen($catalogueChosen) {
		// This generates a comma-seperated list of catalogue IDs for use in a SQL statement
		if ($_POST ['catalogueChoice'] == 'ALL') {
			$catalogueList = '';
			foreach($this->mCatalogueController->GetAllCatalogues () as $catalogue) {
				$catalogueList .= $catalogue->GetCatalogueId().', ';
			}
			$catalogueList = substr($catalogueList,0,strlen($catalogueList)-2);
		} else {
			$catalogue = new CatalogueModel($catalogueChosen);
			$catalogueList = $catalogueChosen;
		} // End generating the list
		$response = '';
		$response .= '<form method="post" name="categoryManufacturerForm" id="categoryManufacturerForm" style="height: 100%;">';
		$response .= '<div class="clickableBg" id="categoryManufacturerGo" name="categoryManufacturerGo"></div>';
		$response .= '<div style="float: left">';	// Start category selection div
		$response .= '<strong>Select Category:</strong><br />';
		$response .= '<select id="categoryChoice" name="categoryChoice">';
		$response .= '<option value="ALL">All Categories</option>';
		foreach($this->mCategoryController->GetAllTopLevelCategoriesForCatalogueList($catalogueList) as $category) {
			$response .= '<option value="'.$category->GetCategoryId().'">'.$category->GetDisplayName().'</option>';
		}
		$response .= '</select>';
		$response .= '</div>';						// End category selection div

		// And/Or container
		$response .= '<div class="clickableBgAnd"></div>';

		// Manufacturer container
		$response .= '<div style="float: left">';	// Start manufacturer selection div
		$response .= '<strong>Select Manufacturer:</strong><br />';
		$response .= '<select id="manufacturerChoice" name="manufacturerChoice">';
		$response .= '<option value="ALL">All Manufacturers</option>';
		foreach($this->mManufacturerController->GetAllManufacturersForCatalogueList($catalogueList) as $manufacturer) {
			$response .= '<option value="'.$manufacturer->GetManufacturerId().'">'.$manufacturer->GetDisplayName().'</option>';
		}
		$response .= '</select>';
		$response .= '</div>';						// End manufacturer selection div

		// Retain the catalogueList variable
		$response .= '<input type="hidden" name="catalogueList" id="catalogueList" value="'.$catalogueList.'" />';

		$response .= '</form><br style="clear: both;" />';
		return $response;
	} // End EventCatalogueChosen

	//! Handles the event "catalogue/manufacturer chosen" event - returns those products that 'fit' with both the category and manufacturer (both of which can be 'ALL')
	/*!
	 * @return String - The HTML for the 'choose product' container
	 */
	function EventCategoryManufacturerChosen($categoryList,$manufacturerList,$catalogueList) {
		$response = '';
		// Test for - Category
		if($categoryList == 'ALL') {
			$categoryList = '';
			foreach($this->mCategoryController->GetAllTopLevelCategoriesForCatalogueList($catalogueList) as $category) {
				$categoryList .= $category->GetCategoryId().', ';
			}
			// Remove the last comma
			$categoryList = substr($categoryList,0,strlen($categoryList)-2);
		}

		// Test for - Manufacturer
		if($manufacturerList == 'ALL') {
			$manufacturerList = '';
			foreach($this->mManufacturerController->GetAllManufacturersForCatalogueList($catalogueList) as $manufacturer) {
				$manufacturerList .= $manufacturer->GetManufacturerId().', ';
			}
			// Remove the last comma
			$manufacturerList = substr($manufacturerList,0,strlen($manufacturerList)-2);
		}

		// Loop over the results and generate the options
		$response .= '<form method="post" name="productChoiceForm" id="productChoiceForm" style="height: 100%">';
		$response .= '<div class="clickableBg" id="productChoiceGo" name="productChoiceGo"></div>';
		$response .= '<div style="float: left">';
		$response .= '<strong>Select Product:</strong><br />';
		$response .= '<select name="productChoice" id="productChoice">';
		$response .= '<option value="ALL">All Products</option>';
		foreach($this->mProductController->GetProductsForCategoryListManufacturerList($categoryList,$manufacturerList) as $product) {
			$response .= '<option value="'.$product->GetProductId().'">'.$product->GetDisplayName().'</option>';
		}
		$response .= '</select>';
		$response .= '</div>';

		// Close form
		$response .= '<input type="hidden" name="catalogueList" id="catalogueList" value="'.$catalogueList.'" />';
		$response .= '<input type="hidden" name="manufacturerList" id="manufacturerList" value="'.$manufacturerList.'" />';
		$response .= '<input type="hidden" name="categoryList" id="categoryList" value="'.$categoryList.'" />';
		$response .= '</form><br style="clear: both;" />';

		// Return the response
		return $response;
	} // End EventCategoryManufacturerChosen

	//! Handles the event "product chosen" event - returns the 'date picker'
	/*!
	 * @return String - The HTML for the 'date picker and generate report' container
	 */
	function EventProductChosen($productList,$categoryList,$manufacturerList,$catalogueList) {
		$response = '';
		// Test for - Product
		if($productList == 'ALL') {
			$productList = '';
			foreach($this->mProductController->GetProductsForCategoryListManufacturerList($categoryList,$manufacturerList) as $product) {
				$productList .= $product->GetProductId().', ';
			}
			// Remove the last comma
			$productList = substr($productList,0,strlen($productList)-2);
		}

		// Loop over the results and generate the options
		$response .= '<form method="post" name="dateRangeForm" id="dateRangeForm" style="height: 100%">';
		$response .= '<div class="clickableBg" id="dateRangeChoiceGo" name="dateRangeChoiceGo"></div>';
		$response .= '<div style="float: left" id="reportDateRange">';
		$response .= '<strong>Date Range:</strong><br style="clear: both" />';
		$response .= '<label for="startDate">From: </label><input type="text" id="startDate" name="startDate" /><br style="clear: both" />';
		$response .= '<label for="endDate">To: </label><input type="text" id="endDate" name="endDate" /><br /><br />';
		$response .= ' <a href="#" onClick="switchDate(\'lastWeek\')">Last Week</a>
						| <a href="#" onClick="switchDate(\'lastMonth\')">Last Month</a>
						| <a href="#" onClick="switchDate(\'lastYear\')">Last Year</a>
						| <a href="#" onClick="switchDate(\'allTime\')">All Time</a>
						';
		$response .= '</div>';

		// Close form
		$response .= '<input type="hidden" name="catalogueList" id="catalogueList" value="'.$catalogueList.'" />';
		$response .= '<input type="hidden" name="manufacturerList" id="manufacturerList" value="'.$manufacturerList.'" />';
		$response .= '<input type="hidden" name="categoryList" id="categoryList" value="'.$categoryList.'" />';
		$response .= '<input type="hidden" name="productList" id="productList" value="'.$productList.'" />';
		$response .= '</form><br style="clear: both;" />';

		// Return the response
		return $response;
	} // End EventProductChosen

	//! Handles the event "date range chosen" event - returns the 'generate report' button
	/*!
	 * @return String - The HTML for the 'generate report' container
	 */
	function EventDateRangeChosen($startDate,$endDate,$productList,$categoryList,$manufacturerList,$catalogueList) {
		$response = '';
		// Convert UK date to US date for use by strtotime
		list($day,$month,$year) = split("/",$startDate);
		$startDate = $month."/".$day."/".$year;
		list($day,$month,$year) = split("/",$endDate);
		$endDate = $month."/".$day."/".$year;

		// Loop over the results and generate the options
		$response .= '<form name="dateAndProductsForm" id="dateAndProductsForm" method="post">';
		$response .= '<input type="hidden" name="dateRangeStartDate" id="dateRangeStartDate" value="'.strtotime($startDate).'" />';
		$response .= '<input type="hidden" name="dateRangeEndDate" id="dateRangeEndDate" value="'.strtotime($endDate).'" />';
		$response .= '<input type="hidden" name="catalogueList" id="catalogueList" value="'.$catalogueList.'" />';
		$response .= '<input type="hidden" name="manufacturerList" id="manufacturerList" value="'.$manufacturerList.'" />';
		$response .= '<input type="hidden" name="categoryList" id="categoryList" value="'.$categoryList.'" />';
		$response .= '<input type="hidden" name="productList" id="productList" value="'.$productList.'" />';
		$response .= '</form>';

		// Return the response
		return $response;
	} // End EventProductChosen

	//! Actually generates the report
	function EventGenerateReport($startDate,$endDate,$productList,$categoryList,$manufacturerList,$catalogueList) {
		$response = '';
		$response .= '<script language="javascript" type="text/javascript">';
		// Initialise 1 x array for each product
		$productArray = explode(', ',$productList);
		foreach($productArray as $productId) {
			$response .= 'var Product'.$productId.'Sales = [];
			';
		}
		// Sort out the date range
		$endDate  = $endDate * 1000;	// Convert to JS timestamp
		$startDate = $startDate * 1000;	// Convert to JS timestamp

		// Initialise totals section
		$totalsSection = '<table id="salesReportTable">
					<thead>
						<tr>
							<th class="alignLeft">Product</th>
							<th class="alignCenter">Sales</th>
							<th class="alignCenter">Value</th>
						</tr>
					</thead>
						';

		// Initialise total values
		$totalValueOfSales = 0;
		$totalSales = 0;

		// Fill up the array for each product, for the date range
		foreach($productArray as $productId) {
			// Initialise a product instance (for the display name)
			$product = new ProductModel($productId);
			// Zero the total for this product
			$currentProductTotal = 0;
			// If more than 4 products, or over 1 week then change the sample rate
			// NB. Working in miliseconds for javascript here - divide by 1000 to get seconds for PHP
			$dateRangeInSeconds = $endDate-$startDate;
			$dateRangeInWords	= $this->mPresentationHelper->SecondsToDays($dateRangeInSeconds/1000).' Days';
			switch($dateRangeInSeconds) {
				// Less than 1 week
				case $dateRangeInSeconds <= 604800000:
					$sampleRate = 86400000;	// Per day
				break;
				// Less than 32 days (1 month)
				case $dateRangeInSeconds <= 2678400000:
					$sampleRate = 345600000;	// Per 4 Days
				break;
				// Less than 60 days (2 months)
				case $dateRangeInSeconds <= 5184000000:
					$sampleRate = 6220800000; // Per Week
				break;
				// Over 2 months
				case $dateRangeInSeconds <= 15552000000:
					$sampleRate = 1209600000; // Per Fortnight
				break;
				// Over 6 months
				case $dateRangeInSeconds > 15552000000:
					$sampleRate = 7257600000; // Per 3 Months
				break;
			}
			$sampleRateInWords  = $this->mPresentationHelper->SecondsToDays($sampleRate/1000).' Days';
			$numberOfSamples	= 0;
			// Loop over the date range
		//	$fh = fopen('A.txt','w+');
			for($i=$startDate;$i<$endDate;$i+=$sampleRate) {
				// Get the sales for the product at that day (function goes back 86400 seconds)
				$sales = $this->mProductController->GetSalesForProductIdForTimestamp($productId,ceil($i/1000),ceil($sampleRate/1000));
				// Fill up the JS array
				$response .= 'Product'.$productId.'Sales.push(['.$i.','.$sales.']);';
				// Tot up the total
				$currentProductTotal += $sales;
				$numberOfSamples++;

				// Make sure we don't miss any sales because of a large sample size
				if($i+$sampleRate>$endDate) {
					// Figure out the gap (small last sample)
					$sampledSoFar = $startDate + ($sampleRate*($numberOfSamples-1));
					$gapSampleRate = $endDate-$sampledSoFar;
					// Get the sales
					$sales = $this->mProductController->GetSalesForProductIdForTimestamp($productId,ceil(($i+$gapSampleRate)/1000),$gapSampleRate/1000);
					$response .= 'Product'.$productId.'Sales.push(['.$endDate.','.$sales.']);';
					// Tot up the total
					$currentProductTotal += $sales;
					$numberOfSamples++;
				}
			}
			// Proportion in stacks/not in stacks
			$endDate2 = $endDate/1000; $startDate2 = $startDate/1000;
			$salesNotInStacks = $this->mProductController->GetSalesForProductIdForTimestamp($productId,$endDate2,($endDate2-$startDate2),true);
			$salesInStacks = $currentProductTotal-$salesNotInStacks;

			// Column - Product
			$totalsSection .= '<tr><td class="alignLeft"><strong>'.$product->GetDisplayName().'</strong><br>Stack Sales: '.$salesInStacks.' | Regular Sales: '.$salesNotInStacks.'</td>';
			// Column - Sales
			$totalsSection .= '<td class="alignCenter">'.$currentProductTotal.'</td>';
			// Column - Value
			$valueOfSales = $currentProductTotal*$product->GetActualPrice();
			$totalsSection .= '<td class="alignCenter">&pound;'.$this->mPresentationHelper->Money($valueOfSales).'</td></tr>';
			// Keep the totals totting up
			$totalValueOfSales += $valueOfSales;
			$totalSales += $currentProductTotal;
		}
		// Grand totals row
		$totalsSection .= '
						<tfoot>
							<tr>
								<td>&nbsp;</td>
								<td id="cellTotalSales" class="alignCenter">'.$totalSales.'</td>
								<td id="cellTotalValue" class="alignCenter">&pound;'.$this->mPresentationHelper->Money($totalValueOfSales).'</td>
							</tr>
						</tfoot>';
		// Close table
		$totalsSection .= '</table>';

		// Make the plot
		$response .= '
			$.plot($("#placeholder"),
					 [';
					 foreach($productArray as $productId) {
						$product = new ProductModel($productId);
					 	$response .= '{ label: "'.$product->GetDisplayName().'", data: Product'.$productId.'Sales },';
					 }
		// Remove the last comma
		$response = substr($response,0,strlen($response)-1);
		$response .= '],
					 {
						series: {
							lines: { show: true },
							points: { show: true },
							bars: {show: false }
						},
						grid: {
							backgroundColor: { colors: ["#fff", "#eee"] }
						},
						legend: {
							position: "nw",
							container: $(\'#legendContainer\')
						},
						xaxis: {
							mode: "time"
						}
					 }
					 );
			</script>
			DATASPLIT
			<strong>Date Range:</strong> '.$dateRangeInWords.'  |
			<strong>Sample Rate:</strong> '.$sampleRateInWords.' |
			<strong>Number of Samples:</strong> '.$numberOfSamples.'<br><br>
			'.$totalsSection.'
		';

		return $response;
	} // End EventGenerateReport

	//! Loads the manufacturers for the catgeory ID supplied
	/*!
	 * @param $newCategoryId - Int - The ID of the category
	 * @return String - The HTML for the 'manufacturer' drop down
	 */
	function EventChangeCategory($newCategoryList,$catalogueList) {
		// Initialise the response
		$response = '';

		// Allow for 'ALL'
		if($newCategoryList == 'ALL') {
			$newCategoryList = '';
			foreach($this->mCategoryController->GetAllTopLevelCategoriesForCatalogueList($catalogueList) as $category) {
				$newCategoryList .= $category->GetCategoryId().', ';
			}
			// Remove the last comma
			$newCategoryList = substr($newCategoryList,0,strlen($newCategoryList)-2);
		}

		// Get the manufacturers that exist in this category
		$manufacturers = $this->mCategoryController->GetManufacturersInCategoryList($newCategoryList);

		$response .= '<option value="ALL">All Manufacturers</option>';
		// Loop over them and generate the <options>
		foreach($manufacturers as $manufacturer) {
			$response .= '<option value="'.$manufacturer->GetManufacturerId().'">'.$manufacturer->GetDisplayName().'</option>';
		}

		// Return the options
		return $response;
	} // End EventChangeCategory

	//! Loads the categories for the catgeory ID supplied
	/*!
	 * @param $newManufacturerId - Int - The ID of the manufacturer
	 * @return String - The HTML for the 'category' drop down
	 */
	function EventChangeManufacturer($newManufacturerList,$catalogueList) {
		// Initialise the response
		$response = '';

		// Allow for 'ALL'
		if($newManufacturerList == 'ALL') {
			$newManufacturerList = '';
			foreach($this->mManufacturerController->GetAllManufacturersForCatalogueList($catalogueList) as $manufacturer) {
				$newManufacturerList .= $manufacturer->GetManufacturerId().', ';
			}
			// Remove the last comma
			$newManufacturerList = substr($newManufacturerList,0,strlen($newManufacturerList)-2);
		}

		// Get the categories that exist for this manufacturer
		$categories = $this->mManufacturerController->GetCategoriesInManufacturerList($newManufacturerList);

		$response .= '<option value="ALL">All Categories</option>';
		// Loop over them and generate the <options>
		foreach($categories as $category) {
			$response .= '<option value="'.$category->GetCategoryId().'">'.$category->GetDisplayName().'</option>';
		}

		// Return the options
		return $response;
	} // End EventChangeManufacturer

} // End AdminReport

// Initialise the object
$adminReports = new AdminReportAjaxHandler2;

switch($_POST['event']) {
	case 'catalogueChosen':
		echo $adminReports->EventCatalogueChosen($_POST['catalogueChoice']);
	break;
	case 'categoryManufacturerChosen':
		echo $adminReports->EventCategoryManufacturerChosen($_POST['categoryChoice'],$_POST['manufacturerChoice'],$_POST['catalogueList']);
	break;
	case 'productChosen':
		echo $adminReports->EventProductChosen($_POST['productChoice'],$_POST['categoryList'],$_POST['manufacturerList'],$_POST['catalogueList']);
	break;
	case 'dateRangeChosen':
		echo $adminReports->EventDateRangeChosen($_POST['startDate'],$_POST['endDate'],$_POST['productList'],$_POST['categoryList'],$_POST['manufacturerList'],$_POST['catalogueList']);
	break;
	case 'generateReport':
		echo $adminReports->EventGenerateReport($_POST['dateRangeStartDate'],$_POST['dateRangeEndDate'],$_POST['productList'],$_POST['categoryList'],$_POST['manufacturerList'],$_POST['catalogueList']);
	break;
	case 'changeCategory':
		echo $adminReports->EventChangeCategory($_POST['newCategory'],$_POST['catalogueList']);
	break;
	case 'changeManufacturer':
		echo $adminReports->EventChangeManufacturer($_POST['newManufacturer'],$_POST['catalogueList']);
	break;
}

?>