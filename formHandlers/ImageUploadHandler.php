<?php
require_once ('../autoload.php');
?>

<script language="javascript" type="text/javascript">
function submitProductFormUpdates() {
	//window.parent.document.forms[0].elements['saveProduct'].click();
	return true;
}
function submitPackageFormUpdates() {
	//window.parent.document.forms[0].elements['savePackage'].click();
	return true;
}
function submitCatalogueFormUpdates() {
	window.parent.document.forms[0].elements['catalogueEditSave'].click();
	return true;
}
</script>

<?php
$registry = Registry::getInstance ();
if (isset ( $_POST ['productId'] )) {
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$productController = new ProductController ( ); // To link the image and product
	$currentProduct = new ProductModel ( $_POST ['productId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?productId=' . $_POST ['productId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		($registry->debugMode ? $fh = fopen ( '../' . $registry->debugDir . '/ImageUploadHandler.txt', 'a+' ) : NULL);
		$numberOfImages = count ( $currentProduct->GetImages () );
		($registry->debugMode ? fwrite ( $fh, "Number of Images: " . $numberOfImages . "\r\n" ) : NULL);
		$imageNumber = $numberOfImages + 1;
		($registry->debugMode ? fwrite ( $fh, "Next Image Number: " . $imageNumber . "\r\n" ) : NULL);
		$fileName = 'product' . $currentProduct->GetProductId () . 'image' . $imageNumber;
		($registry->debugMode ? fwrite ( $fh, "File Name: " . $fileName . "\r\n" ) : NULL);
		($registry->debugMode ? fclose ( $fh ) : NULL);

		$imageNameArr = explode ( '.', $_FILES ['newImageUpload'] ['name'] );
		$extension = strtolower ( $imageNameArr [1] );
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}
		$newImage->SetFilename ( $fileName . '.' . $extension );
		if (0 == $numberOfImages) {
			$newImage->SetMainImage ( 1 );
		}

		// Upload
		$uploadHelper->uploadSmallImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadMediumImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadLargeImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadOriginalImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );

		// Add to product
		$productController->CreateImageLink ( $currentProduct, $newImage );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
		die();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->viewDir . '/AdminProductView.php?id=' . $_POST ['productId'] . '&tab=images\';
		</script>';
} elseif (isset ( $_POST ['packageId'] )) {
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$currentPackage = new PackageModel ( $_POST ['packageId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?packageId=' . $_POST ['packageId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		$fileName = 'package' . $currentPackage->GetPackageId () . 'image';
		$imageNameArr = explode ( '.', $_FILES ['newImageUpload'] ['name'] );
		$extension = strtolower ( $imageNameArr [1] );
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}

		$newImage->SetFilename ( $fileName . '.' . $extension );

		// Upload
		$uploadHelper->uploadSmallImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadMediumImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadLargeImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadOriginalImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );

		// Add to product
		$currentPackage->SetImage ( $newImage );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();die();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->viewDir . '/AdminPackageView.php?id=' . $_POST ['packageId'] . '&tab=image\';
		</script>';
} elseif (isset ( $_POST ['manufacturerId'] )) {
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$currentManufacturer = new ManufacturerModel ( $_POST ['manufacturerId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?manufacturerId=' . $_POST ['manufacturerId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		$fileName = 'manufacturer' . $currentManufacturer->GetManufacturerId () . 'image';
		$imageNameArr = explode ( '.', $_FILES ['newImageUpload'] ['name'] );
		$extension = strtolower ( $imageNameArr [1] );
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}

		$newImage->SetFilename ( $fileName . '.' . $extension );

		// Upload
		$uploadHelper->uploadManufacturerImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName, 80 );
		$uploadHelper->uploadOriginalImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );

		// Add to product
		$currentManufacturer->SetImage ( $newImage );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
/*	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->viewDir . '/AdminManufacturerView.php?id=' . $_POST ['manufacturerId'] . '&tab=image\';
		</script>';*/
} elseif (isset ( $_POST ['catalogueId'] )) {
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$currentCatalogue = new CatalogueModel ( $_POST ['catalogueId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?packageId=' . $_POST ['packageId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		$fileName = 'catalogue' . $currentCatalogue->GetCatalogueId () . 'image';
		$imageNameArr = explode ( '.', $_FILES ['newImageUpload'] ['name'] );
		$extension = strtolower ( $imageNameArr [1] );
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}

		$newImage->SetFilename ( $fileName . '.' . $extension );

		// Upload
		$uploadHelper->uploadSmallImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadMediumImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadLargeImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );
		$uploadHelper->uploadOriginalImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );

		// Add to product
		$currentCatalogue->SetImage ( $newImage );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->viewDir . '/AdminCatalogueEditView.php?id=' . $_POST ['catalogueId'] . '\';
		</script>';
} elseif(isset($_POST['tagId'])) {
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$currentTag = new TagModel ( $_POST ['tagId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?tagId=' . $_POST ['tagId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		$fileName = 'tag' . $currentTag->GetTagId () . 'image';
		$imageNameArr = explode ( '.', $_FILES ['newImageUpload'] ['name'] );
		$extension = strtolower ( $imageNameArr [1] );
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}

		$newImage->SetFilename ( $fileName . '.' . $extension );

		// Upload
		$uploadHelper->uploadTagImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName, 80 );
		$uploadHelper->uploadOriginalImage ( $_FILES ['newImageUpload'] ['tmp_name'], $fileName );

		// Add to product
		$currentTag->SetImage ( $newImage );
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->viewDir . '/AdminTagView.php?id=' . $_POST ['tagId'] . '&tab=image\';
		</script>';
} elseif(isset($_POST['contentId'])) {
	// Give the file a different filename depending on whether it is a header or thumbnail image
	switch($_POST['imageType']) {
		case 'thumbnail':
			$imageSuffix = 'thumb';
			$imageSize = 75;
		break;
		case 'header':
			$imageSuffix = 'header';
			$imageSize = 540; // 540x80
		break;
	}
	$uploadHelper = new UploadHelper ( ); // To upload the image
	$imageController = new ImageController ( ); // To create the image
	$currentContent = new ContentModel ( $_POST ['contentId'] );
	$newImage = $imageController->CreateImage ();

	// Validate
	if (! getimagesize ( $_FILES ['newImageUpload'] ['tmp_name'] )) {
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Not an image.</div>';
		echo '<meta http-equiv="refresh" content="1;url=ImageUploadHandler.php?contentId=' . $_POST ['contentId'] . '"/>';
		die ();
	}

	// Process
	try {
		// Filename
		$fileName = 'content'.$currentContent->GetContentId().$imageSuffix.'image';
		$imageNameArr = explode('.',$_FILES['newImageUpload']['name']);
		$extension = strtolower($imageNameArr[1]);
		if ('jpg' == $extension) {
			$extension = 'jpeg';
		}

		$newImage->SetFilename($fileName.'.'.$extension);

		// Upload
		$uploadHelper->uploadContentImage($_FILES['newImageUpload']['tmp_name'],$fileName,$imageSize);
		$uploadHelper->uploadOriginalImage($_FILES['newImageUpload']['tmp_name'],$fileName);

		// Add to product
		switch($_POST['imageType']) {
			case 'thumbnail':
				$currentContent->SetThumbImage($newImage);
			break;
			case 'header':
				$currentContent->SetHeaderImage($newImage);
			break;
		}
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}

	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Image Added.</div>';
	echo '<script language="javascript" type="text/javascript">
			self.parent.location.href=\'' . $registry->adminDir . '/content/'.$_POST['contentId'].'\';
		</script>';
}

if (isset ( $_GET ['productId'] )) {
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm" onsubmit="return submitProductFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="productId" id="productId" value="' . $_GET ['productId'] . '" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
} elseif (isset ( $_GET ['packageId'] )) {
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm"  onsubmit="return submitPackageFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="packageId" id="packageId" value="' . $_GET ['packageId'] . '" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
} elseif (isset ( $_GET ['manufacturerId'] )) {
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm"  onsubmit="return submitPackageFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="manufacturerId" id="manufacturerId" value="' . $_GET ['manufacturerId'] . '" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
} elseif(isset ( $_GET ['tagId'] )) {
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm"  onsubmit="return submitPackageFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="tagId" id="tagId" value="' . $_GET ['tagId'] . '" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
} elseif(isset($_GET['contentId'])) {
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm"  onsubmit="return submitPackageFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="contentId" id="contentId" value="'.$_GET ['contentId'].'" />
	<input type="hidden" name="imageType" id="imageType" value="'.$_GET ['imageType'].'" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
} else { // This assumes this is the catalogue form - need to abstract out and make a proper handler
	echo '
	<form action="ImageUploadHandler.php" method="post" enctype="multipart/form-data" name="imageUploadForm" id="imageUploadForm" onsubmit="return submitCatalogueFormUpdates()" />
	<input type="file" name="newImageUpload" id="newImageUpload" />
	<input type="hidden" name="catalogueId" id="catalogueId" value="' . $_GET ['catalogueId'] . '" />
	<br />
	<input type="submit" value="Add Image" style="width: auto; text-align: center;" />
	</form>
	';
}
?>