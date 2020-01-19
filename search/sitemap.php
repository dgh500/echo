<?php

header ( 'Content-type: application/xml; charset="utf-8"', true );

include ('../autoload.php');
$registry = Registry::getInstance ();
$catalogue = $registry->catalogue;
$validationHelper = new ValidationHelper ( );
$publicLayoutHelper = new PublicLayoutHelper ( );
$today = date ( 'Y-m-d' );

$sitemap [] = '<?xml version="1.0" encoding="UTF-8"?>
';
$sitemap [] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
$sitemap [] = '<url>
';
$sitemap [] = '<loc>http://www.echosupplements.com/</loc>
';
$sitemap [] = '<lastmod>' . $today . '</lastmod>
';
$sitemap [] = '<changefreq>weekly</changefreq>
';
$sitemap [] = '<priority>0.9</priority>
';
$sitemap [] = '</url>';

// Top-Level Categories
$categoryController = new CategoryController ( );
$allTopLevel = $categoryController->GetAllTopLevelCategoriesForCatalogue ( $catalogue );
foreach ( $allTopLevel as $topCategory ) {
	$sitemap [] = '<url>';
	$sitemap [] = '<loc>' . $registry->baseDir . '/department/' . $validationHelper->MakeLinkSafe ( $topCategory->GetDisplayName () ) . '/' . $topCategory->GetCategoryId () . '</loc>';
	$sitemap [] = '<lastmod>' . $today . '</lastmod>';
	$sitemap [] = '<changefreq>daily</changefreq>';
	$sitemap [] = '<priority>0.5</priority>';
	$sitemap [] = '</url>';
}

// Sub-level Categories
$allSubLevel = $categoryController->GetAllSubLevelCategoriesForCatalogue ( $catalogue );
foreach ( $allSubLevel as $subCategory ) {
	$parentCategory = $subCategory->GetParentCategory ();
	if(is_object($subCategory) && is_object($parentCategory)) {
		$sitemap [] = '<url>';
		$sitemap [] = '<loc>' . $registry->baseDir . '/department/' . $validationHelper->MakeLinkSafe ( $parentCategory->GetDisplayName () ) . '/' . $parentCategory->GetCategoryId () . '/' . $validationHelper->MakeLinkSafe ( $subCategory->GetDisplayName () ) . '/' . $subCategory->GetCategoryId () . '</loc>';
		$sitemap [] = '<lastmod>' . $today . '</lastmod>';
		$sitemap [] = '<changefreq>weekly</changefreq>';
		$sitemap [] = '<priority>0.5</priority>';
		$sitemap [] = '</url>';
	}
}

// Products
$productController = new ProductController ( );
$allProducts = $productController->GetAllProductsInCatalogue ( $catalogue );
foreach ( $allProducts as $product ) {
	$href = $publicLayoutHelper->LoadLinkHref ( $product );
	$sitemap [] = '<url>
	';
	$sitemap [] = '<loc>' . $href . '</loc>
	';
	$sitemap [] = '<lastmod>' . $today . '</lastmod>
	';
	$sitemap [] = '<changefreq>weekly</changefreq>
	';
	$sitemap [] = '<priority>0.9</priority>
	';
	$sitemap [] = '</url>
	';
}

// Manufacturers brand pages & 'shop by' pages
$manufacturerController = new ManufacturerController ( );
$allActiveManufacturers = $manufacturerController->GetAllManufacturersFor ( $catalogue, true );
foreach ( $allActiveManufacturers as $manufacturer ) {
	$href = $publicLayoutHelper->LoadManufacturerHref ( $manufacturer );
	$sitemap [] = '<url>
	';
	$sitemap [] = '<loc>' . $href . '</loc>
	';
	$sitemap [] = '<lastmod>' . $today . '</lastmod>
	';
	$sitemap [] = '<changefreq>weekly</changefreq>
	';
	$sitemap [] = '<priority>0.5</priority>
	';
	$sitemap [] = '</url>
	';
	if ($manufacturer->GetSizeChart ()) {
		$href = $publicLayoutHelper->LoadSizeChartHref ( $manufacturer );
		$sitemap [] = '<url>
		';
		$sitemap [] = '<loc>' . $href . '</loc>
		';
		$sitemap [] = '<lastmod>' . $today . '</lastmod>
		';
		$sitemap [] = '<changefreq>weekly</changefreq>
		';
		$sitemap [] = '<priority>0.5</priority>
		';
		$sitemap [] = '</url>
		';
	}
}

$sitemap [] = '</urlset>';

$fh = fopen ( 'sitemap.xml', 'w+' );
foreach ( $sitemap as $value ) {
	echo $value;
	fwrite ( $fh, $value );
}
fclose ( $fh );

?>