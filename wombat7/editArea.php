<?php

include_once ('../autoload.php');
$registry = Registry::getInstance();

// Request Handler - takes a URL and loads the correct view
if (isset ( $_GET ['what'] ) && isset ( $_GET ['id'] )) {
	switch ($_GET ['what']) {
		case 'addCategory' :
			if ($_GET ['id'] == 0) {
				// Top Level Category
				include_once ('../view/AddTopLevelCategoryView.php');
			} else {
				// Sub Category
				include_once ('../view/AddSubLevelCategoryView.php');
			}
			break;
		case 'addPackageCategory' :
			include_once ('../view/AddPackageCategoryView.php');
			break;
		case 'addPackage' :
			include_once ('../view/AddPackageView.php');
			break;
		case 'category' :
			include_once ('../view/AdminCategoryView.php');
			break;
		case 'addProduct' :
			include_once ('../view/AddProductView.php');
			break;
		case 'product' :
			include_once ('../view/AdminProductView.php');
			break;
		case 'package' :
			include_once ('../view/AdminPackageView.php');
			break;
		case 'editCatalogue' :
			include_once ('../view/AdminCatalogueEditView.php');
			break;
		case 'addCatalogue' :
			include_once ('../view/AddCatalogueView.php');
			break;
		case 'editGallery' :
			include_once ('../view/AdminGalleryEditView.php');
			break;
		case 'addGallery' :
			include_once ('../view/AddGalleryView.php');
			break;
		case 'addOrder' :
			include_once ('../view/AddOrderView.php');
			break;
		case 'addOrder2' :
			// Unset any previous entries
			unset($_SESSION['init']);
			unset($_SESSION['cardHoldersName']);
			unset($_SESSION['cardType']);
			unset($_SESSION['cardNumber']);
			unset($_SESSION['validFromMonth']);
			unset($_SESSION['validFromYear']);
			unset($_SESSION['expiryDateMonth']);
			unset($_SESSION['expiryDateYear']);
			unset($_SESSION['cardVerificationNumber']);
			unset($_SESSION['issueNumber']);
			session_regenerate_id();
			echo "
			<script language=\"javascript\" type=\"text/javascript\">
			window.open('".$registry->secureBaseDir."/view/AddOrderView2.php?b=1','AddOrder','width=820,height=800,toolbar=yes, location=yes,directories=yes,status=no,menubar=no,scrollbars=yes,copyhistory=no, resizable=yes')
			</script>";
			break;
		case 'order' :
			include_once ('../view/OrdersEditView.php');
			break;
		case 'searchOrders' :
			include_once ('../view/OrdersSearchView.php');
			break;
		case 'manufacturer' :
			include_once ('../view/AdminManufacturerView.php');
			break;
		case 'tag' :
			include_once ('../view/AdminTagView.php');
			break;
	}
}

?>
