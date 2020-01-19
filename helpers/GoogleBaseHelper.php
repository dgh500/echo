<?php
//! Makes the google base feed
class GoogleBaseHelper extends Helper {

	function __construct() {
		parent::__construct();
		// Initialise what you need
		$this->mCatalogue = $this->mRegistry->catalogue;
		$this->mProductController 	= new ProductController();
		$this->mPackageController	= new PackageController();
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper();
		$this->mCurrentDate = date('Y-m-d');
		// Initialise the feed
		$this->mFeed = '';
	}

	function GenerateFeed() {
		$this->LoadPreamble();
		$this->LoadProducts();
	#	$this->LoadPackages();
		$this->CloseFeed();
	}

	function CloseFeed() {
		$this->mFeed[] = '</feed>';
	}

	//! Loads the XML start tag, feed and name,email,author,title etc.
	function LoadPreamble() {
		$this->mFeed[] = '<?xml version="1.0" encoding="iso-8859-1"?>';
		$this->mFeed[] = '<feed xmlns="http://www.w3c.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">';
		$this->mFeed[] = '<g:item_type>products</g:item_type>';
		$this->mFeed[] = '<author>';
		$this->mFeed[] = '<name>'.$this->mCatalogue->GetDisplayName().'</name>';
		$this->mFeed[] = '<email>'.$this->mCatalogue->GetEmail().'</email>';
		$this->mFeed[] = '</author>';
		$this->mFeed[] = '<title>'.$this->mCatalogue->GetDisplayName().'</title>';
	}

	//! Loads all of the products in the catalogue into the feed
	function LoadProducts() {
		// Get the products
		$allProducts = $this->mProductController->GetAllProductsInCatalogue($this->mCatalogue->GetCatalogueId());
		// For every one
		foreach($allProducts as $product) {
		// Don't add if in ignore list
		$ignoreList = array(463,464,461,462,468,477,461,404,414,379,375,377,452,454,374,376,438,455,457,407,409,406,410,411,250,378,400,342,350,362,345,364,365,366,370,368,369,383,394,383,339,340,297,337,296,222,146,44,216,197,188,79,226,229,253,198,252,254,177,175,232,243,277,159,179,278,70,69,261,262,68,190,285,259,260,238,237,272,231,235,251,257,284,275,209);
		// Or if it is hidden!
		// .. or if it is glutamine - stupid google
		if(!in_array($product->GetProductId(),$ignoreList) && !$product->GetHidden() && false === stripos($product->GetDisplayName(),'glutamine')) {
			$skus = $product->GetSkus();
			foreach($skus as $sku) {
				// If the product is marked as out of stock OR the SKU code isn't there then don't do it!
				if($product->GetInStock() == 0 || $sku->GetSageCode() == '' || in_array($product->GetManufacturer ()->GetManufacturerId (),array(17,27,9,14,10,23))) {
					// Do Nothing
					} else {
 					// Get the SKU attribute list
					$attrList = str_replace('OUT OF STOCK -','',htmlspecialchars($sku->GetSkuAttributesList(),ENT_QUOTES,"UTF-8"));
					$attrList = str_replace('SOLD OUT -','',htmlspecialchars($attrList,ENT_QUOTES,"UTF-8"));
					$attrList = str_replace('( ','(',htmlspecialchars($attrList,ENT_QUOTES,"UTF-8"));

					// Get the link to the product
					$href = $this->mPublicLayoutHelper->LoadSkuLinkHref($sku,'-'.$attrList);
					// If the product has a manufacturer, include it
					if ($product->GetManufacturer ()) {
						$brand = $product->GetManufacturer ()->GetDisplayName ();
						$brandId = $product->GetManufacturer ()->GetManufacturerId ();
					// Otherwise put dummy values in
					} else {
						$brand = 'NA';
						$brandId = 0;
					}
					// If the product has an image, include it
					if ($product->GetMainImage ()) {
						$image = $product->GetMainImage ();
						$imageHref = 'http://www.echosupplements.com/'.$this->mPublicLayoutHelper->ImageHref ( $image );
					} else {
					// Otherwise put nothing in - google doesn't like dummy images
						$imageHref = '';
					}
					// Get the category the product is in
					$categories = $product->GetCategories();
					$category = trim($categories[0]->GetDisplayName());

					// Generate Title
					$title = str_replace('A Designs','Anabolic Designs',$product->GetDisplayName()).' '.$attrList;
					// Trim it
					$title = substr($title,0,70);
					// Sanitise it
					$title = $this->mValidationHelper->MakeXmlSafe($title);

					// Start the entry for the product
					$this->mFeed[] = '<entry>';
						$this->mFeed[] = '<title>' . $title.'</title>';
						$this->mFeed[] = '<g:brand>' . $this->mValidationHelper->MakeXmlSafe($brand,true).'</g:brand>';
						$this->mFeed[] = '<g:condition>new</g:condition>';
						// If the product has an empty description then put the product name in, otherwise put the proper description in
						if ($product->GetDescription() != '' && $product->GetDescription() != ' ') {
							$this->mFeed[] = '<summary>'.$this->mValidationHelper->MakeXmlSafe(str_replace('A Designs','Anabolic Designs',$product->GetDisplayName())).' '.$attrList.' : '.$this->mValidationHelper->MakeXmlSafe($product->GetDescription(),true).'</summary>';
						} else {
							$this->mFeed[] = '<summary>'.$this->mValidationHelper->MakeXmlSafe(str_replace('A Designs','Anabolic Designs',$product->GetDisplayName())).' '.$attrList.'</summary>';
						}
						// Product ID
						$this->mFeed [] = '<id>' . $sku->GetSkuId() . '</id>';
						// Link to the image
						$this->mFeed [] = '<g:image_link>' . $imageHref . '</g:image_link>';

						// Product URL
						$this->mFeed [] = '<link href="' . $href . '" />';
						// MPN = Manufacturer Product Number - Brand ID etc.
						$this->mFeed [] = '<g:mpn>'.$brandId.'</g:mpn>';
						// Product Price, in normal XX.YY format
						$this->mFeed [] = '<g:price>'.$product->GetActualPrice().'</g:price>';
						// Type of product - defined by google at the URL:
						// http://base.google.com/support/bin/answer.py?hl=en&answer=66818
			//		bk	$this->mFeed[] = '<g:product_type>'.$this->mRegistry->GoogleBaseProductType.$this->mValidationHelper->MakeXmlSafe($category,true).'</g:product_type>';
						$this->mFeed[] = '<g:product_type>'.$this->mValidationHelper->MakeXmlSafe($category,true).'</g:product_type>';
						// How many - always 1
						$this->mFeed[] = '<g:quantity>1</g:quantity>';
						// No UPC (Universal Product Code) because we don't use it
						if($sku->GetSageCode() != '') {
							// UPDATE: Google now requires a 14 char SKU code, eg. 05060161300871 - so pad out with zeros to the start.
							if(strlen($sku->GetSageCode()) == 13) {
								$googleCode = '0'.$sku->GetSageCode();
							} elseif(strlen($sku->GetSkuId()) == 12) {
								$googleCode = '00'.$sku->GetSageCode();
							} elseif(strlen($sku->GetSkuId()) == 11) {
								$googleCode = '000'.$sku->GetSageCode();
							} else {
								$googleCode = $sku->GetSageCode();
							}
							$this->mFeed[] = '<g:gtin>'.$googleCode.'</g:gtin>';
						} else {
							$this->mFeed[] = '<g:gtin>0</g:gtin>';
						}
						// How much it weighs
						$this->mFeed[] = '<g:weight>'.$product->GetWeight().'</g:weight>';
						// Stock?
						$this->mFeed[] = '<g:availability>in stock</g:availability>';
						// Postage Cost
						if($product->GetActualPrice() >= 45) {
							$shippingCost = 0;
						} else {
							$shippingCost = 2.95;
						}
						$this->mFeed[] = '<g:shipping><g:price>'.$shippingCost.'</g:price><g:service>Next Day Delivery</g:service><g:country>GB</g:country></g:shipping>';
						// Online Only = No!
						$this->mFeed[] = '<g:online_only>n</g:online_only>';
						// Google Category
						$this->mFeed[] = '<g:google_product_category>Health &amp; Beauty &gt; Health Care &gt; Fitness &amp; Nutrition</g:google_product_category>';
						// End product entry
						$this->mFeed[] = '</entry>';
					} // End excluding out of stock, no sage code SKUs
				} // End looping over SKUs
			} // End if in stock
			} // End looping over products
	} // End LoadProducts()

	//! Loads all of the packages in the catalogue into the feed
	function LoadPackages() {
		$packageId = 1000;
		// Get the package
		$allPackages = $this->mPackageController->GetAllPackages();
		// For every one
		foreach($allPackages as $package) {
			// Get the link to the product
			$href = $this->mPublicLayoutHelper->LoadPackageLinkHref($package);
			$brand = 'NA';
			$brandId = 0;
			// If the package has an image, include it
			if ($package->GetImage ()) {
				$image = $package->GetImage();
				$imageHref =  'http://www.echosupplements.com/'.$this->mPublicLayoutHelper->ImageHref($image);
			} else {
			// Otherwise put nothing in - google doesn't like dummy images
				$imageHref = '';
			}
			// Get the category the package is in
			$category = 'Supplement Stacks';

			// Start the entry for the package
			$this->mFeed[] = '<entry>';
				$this->mFeed[] = '<title>' . $this->mValidationHelper->MakeXmlSafe($package->GetDisplayName()).'</title>';
				$this->mFeed[] = '<g:brand>' . $this->mValidationHelper->MakeXmlSafe($brand,true).'</g:brand>';
				$this->mFeed[] = '<g:condition>new</g:condition>';
				// If the product has an empty description then put the product name in, otherwise put the proper description in
				if ($package->GetDescription() != '' && $package->GetDescription() != ' ') {
					$this->mFeed[] = '<summary>'.$this->mValidationHelper->MakeXmlSafe($package->GetDescription(),true).'</summary>';
				} else {
					$this->mFeed[] = '<summary>'.$package->GetDisplayName().'</summary>';
				}
				// Product ID
				$this->mFeed [] = '<id>'.$packageId.'</id>';
				// Link to the image
				$this->mFeed [] = '<g:image_link>' . $imageHref . '</g:image_link>';
				// Product URL
				$this->mFeed [] = '<link href="' . $href . '" />';
				// MPN = Manufacturer Product Number - Brand ID etc.
				$this->mFeed [] = '<g:mpn>'.$brandId.'</g:mpn>';
				// Product Price, in normal XX.YY format
				$this->mFeed [] = '<g:price>'.$package->GetActualPrice().'</g:price>';
				// Type of product - defined by google at the URL:
				// http://base.google.com/support/bin/answer.py?hl=en&answer=66818
	//		bk	$this->mFeed[] = '<g:product_type>'.$this->mRegistry->GoogleBaseProductType.$this->mValidationHelper->MakeXmlSafe($category,true).'</g:product_type>';
				$this->mFeed[] = '<g:product_type>'.$this->mValidationHelper->MakeXmlSafe($category,true).'</g:product_type>';
				// How many - always 1
				$this->mFeed[] = '<g:quantity>1</g:quantity>';
				// No UPC (Universal Product Code) because we don't use it
				$this->mFeed[] = '<g:upc>0</g:upc>';
				// How much it weighs
				$this->mFeed[] = '<g:weight>2000</g:weight>';
			// End product entry
			$this->mFeed[] = '</entry>';
			$packageId++;
		} // End looping over package
	} // End LoadPackages()

	//! Puts the file online
	function PublishFeed() {
		$this->mFh = fopen($this->mRegistry->GoogleBaseFile,'w+');
		foreach($this->mFeed as $value) {
			fwrite($this->mFh,$value);
		}
		fclose($this->mFh);
	} // End PublishFeed
} // End GoogleBaseHelper

?>