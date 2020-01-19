<?php

/**
 *
 * This script updates the best seller field (BS_Product_ID) in the category table (tblCategory) based on total sales
 *
 */

// Connect to DB
mysql_connect('localhost','root','');
mysql_select_db('echo');

// Initialise Array
$resultsArr = array();
/*
// One at a time??
$categoryId = 1;
$sql = 'SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID = '.$categoryId;
$result = mysql_query($sql) or die($sql.'<br><br>'.mysql_error());
while($subRow = mysql_fetch_assoc($result)) {
	// Now got $subRow['Category_ID'] holding the Category ID of the sub category
	// Figure out the best selling product in this subcat
	$sqll =
"SELECT DISTINCT
	tblProduct.Product_ID,
	COUNT(tblProduct.Product_ID) AS ProductCount,
	tblProduct_Text.Display_Name
FROM
	tblProduct
INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
INNER JOIN tblProduct_Text ON tblProduct.Product_ID = tblProduct_Text.Product_ID
INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
LEFT JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
WHERE
	tblProduct.Hidden = '0'
AND
	tblCategory_Products.Category_ID = ".$subRow['Category_ID']."
GROUP BY
	tblProduct_Text.Product_ID,
	tblProduct_Text.Display_Name
ORDER BY ProductCount DESC LIMIT 1";
	$resultt = mysql_query($sqll) or die($sqll.'<br><br>'.mysql_error());
} // End sub cats

*/

/*
// Get top level category IDs
$sql = 'SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID IS NULL';
$result = mysql_query($sql) or die($sql.'<br><br>'.mysql_error());
while($row = mysql_fetch_assoc($result)) {
	// Got array where $row['Category_ID'] holds the ID of the top level category
	// Initialise Results for this top level category
	$resultsArr[$row['Category_ID']]['salesCount'] 	= 0;
	$resultsArr[$row['Category_ID']]['productId'] 	= NULL;
	// Get Subcats
	$sql = 'SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID = '.$row['Category_ID'];
	$result2 = mysql_query($sql) or die($sql.'<br><br>'.mysql_error());
	while($subRow = mysql_fetch_assoc($result2)) {
		// Now got $subRow['Category_ID'] holding the Category ID of the sub category
		// Figure out the best selling product in this subcat
		$sql = "SELECT DISTINCT
					tblProduct.Product_ID AS ProductId,
					COUNT(tblProduct.Product_ID) AS SalesCount
				FROM tblProduct
				INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
				INNER JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				INNER JOIN tblOrder on tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
				WHERE
					tblProduct.Hidden = '0'
					AND tblCategory.Category_ID = ".$subRow['Category_ID']."
				GROUP BY tblProduct.Product_ID
				ORDER BY SalesCount DESC
				LIMIT 1";
		$result3 = mysql_query($sql) or die($sql.'<br><br>'.mysql_error());
	} // End sub cats
} // End top level cats

*/













// Include models, controllers etc.
$noSessions = true;
include('../autoload.php');
$cController = new CategoryController;
$pController = new ProductController;

/* Initialise array
	Idea is $bsArr[topLevelCategoryId]['salesCount'] = topSellingProductInItselfAndSubCategoriesSalesCount
			$bsArr[topLevelCategoryId]['productId'] = topSellingProductInItselfAndSubCategoriesId
*/
$bsArray = array();

// Get all top level categories
$topLevelCategories = $cController->GetAllTopLevelCategoriesForCatalogue($registry->catalogue);
#$topLevelCategories = array_slice($topLevelCategories,1,2);
#var_dump($topLevelCategories); die();

foreach($topLevelCategories as $topLevelCategory) {
	// Initialise array key
	$bsArray[$topLevelCategory->GetCategoryId()]['productId'] 	= NULL;
	$bsArray[$topLevelCategory->GetCategoryId()]['salesCount'] 	= NULL;

	// Look at products within the top level category itself
	$topProduct = $topLevelCategory->CalculateBestSellingProduct();
	if($topProduct) {
		if($topProduct && intval($topProduct->ProductCount) > intval($bsArray[$topLevelCategory->GetCategoryId()]['salesCount'])) {
			$bsArray[$topLevelCategory->GetCategoryId()]['productId'] = $topProduct->Product_ID;
			$bsArray[$topLevelCategory->GetCategoryId()]['salesCount'] = $topProduct->ProductCount;
		}
	}

	// Look within subcategories
	$subCategories = $cController->GetAllSubCategoriesOf($topLevelCategory);
	if(count($subCategories) > 0) {
		// Loop over subcategories and update the value for the top level category if any of them 'beat' the current sales count
		foreach($subCategories as $subCategory) {
			// Returns both the Product_ID AND the Sales_Count (as object)
			$topProduct = $subCategory->CalculateBestSellingProduct();
			// If the top product in this category beats the current top product for the top level category, or none set yet then update it
			if($topProduct) {
				if(intval($topProduct->ProductCount) > intval($bsArray[$topLevelCategory->GetCategoryId()]['salesCount']) || $bsArray[$topLevelCategory->GetCategoryId()]['productId'] == NULL) {
					$bsArray[$topLevelCategory->GetCategoryId()]['productId'] = $topProduct->Product_ID;
					$bsArray[$topLevelCategory->GetCategoryId()]['salesCount'] = $topProduct->ProductCount;
				} // End if
			}
		} // End foreach
	} // End If

	// If we still don't have a top product then just pick one at random
	if($bsArray[$topLevelCategory->GetCategoryId()]['productId'] == NULL) {
		$topProduct = $pController->GetAnyProductInCategory($topLevelCategory);
		if($topProduct) {
			$bsArray[$topLevelCategory->GetCategoryId()]['productId'] = $topProduct->GetProductId();
			$bsArray[$topLevelCategory->GetCategoryId()]['salesCount'] = '0';
		}
	}

} // End foreach
#var_dump($bsArray); die();
// Process the results array
foreach($bsArray as $categoryId=>$productArr) {
	if($productArr['productId'] !== NULL) {
		$category = new CategoryModel($categoryId);
		$product  = new ProductModel($productArr['productId']);
		$category->SetBestSellingProduct($product);
	}
}

// Also work out the best seller for the catalogue
$catalogue = $registry->catalogue;
$productObj = $catalogue->CalculateBestSellingProduct();
$product = new ProductModel($productObj->Product_ID);
$catalogue->SetBestSellingProduct($product);


?>