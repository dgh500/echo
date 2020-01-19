<?php

require_once ('../autoload.php');

class AdminPackageHandler {

	var $mPackage;
	var $mClean;

	function ValidateInput($submittedArray, $package) {
		$validator = new ValidationHelper ( );
		$this->mPackage = $package;
		foreach ( $submittedArray as $key => $value ) {
			switch ($key) {
				case 'displayName' :
				case 'description' :
					$this->mClean [$key] = $validator->MakeSafe ( $value );
					break;
				case 'longDescription' :
					$this->mClean [$key] = $validator->RemoveWhitespace ( $value );
					break;
				case 'actualPrice' :
				case 'wasPrice' :
				case 'postage' :
					if (! $validator->IsNumeric ( $value )) {
						$error = new Error ( 'Validation failed because the ' . $key . ' must be numeric.' );
						throw new Exception ( $error->GetErrorMsg () );
					}
					$this->mClean [$key] = $validator->MakeSafe ( $value );
					break;
				default :
					$this->mClean [$key] = $submittedArray [$key];
					break;
			}
		}

		// Get Quantities
		foreach($package->GetContents() as $product) {
			// The inputs for quantity have the ID 'PRODUCTQTY1234' where 1234 is the product ID
			if(isset($submittedArray['PRODUCTQTY'.$product->GetProductId()])) {
				$this->mClean['Qty'][$product->GetProductId()] = $submittedArray['PRODUCTQTY'.$product->GetProductId()];
			} else {
				$this->mClean['Qty'][$product->GetProductId()] = 0;
			}
		}
	} // End ValidateInput

	function IsCorrectKey($test) {
		if (2 == count ( $test )) {
			return true;
		} else {
			return false;
		}
	}

	function DeletePackage($package) {
		$packageController = new PackageController ( );
		$packageController->DeletePackage ( $package );
	}

	// ValidateInput MUST be called before this!
	function SavePackage() {

		// Text
		$this->mPackage->SetDisplayName		($this->mClean['displayName']);
		$this->mPackage->SetDescription		($this->mClean['description']);
		$this->mPackage->SetLongDescription ($this->mClean['longDescription']);
		$this->mPackage->SetActualPrice 	($this->mClean['actualPrice']);
		$this->mPackage->SetWasPrice 		($this->mClean['wasPrice']);
		$this->mPackage->SetPostage 		($this->mClean['postage']);

		// Qty in package
		foreach($this->mPackage->GetContents() as $product) {
			if($this->mClean['Qty'][$product->GetProductId()]>0) {
				$this->mPackage->SetProductQty($product,$this->mClean['Qty'][$product->GetProductId()]);
			}
		}

		// Checkbox
		(isset ( $this->mClean ['offerOfTheWeek'] ) ? $this->mPackage->SetOfferOfWeek ( 1 ) : $this->mPackage->SetOfferOfWeek ( 0 ));

		// Any that have to be matched by their IDs (non-static)
		foreach ( $this->mClean as $key => $value ) {
			// Contents - Add
			$contentsArr = explode ( "PACKAGECONTENTS", $key );
			if ($this->IsCorrectKey ( $contentsArr ) && $contentsArr [1] [0] != 'C') {
				$product = new ProductModel ( $contentsArr [1] );
				$this->mPackage->AddProduct ( $product );
			}
			// Upgrades - Add
			$upgradesArr = explode ( "PACKAGEUPGRADES", $key );
			if ($this->IsCorrectKey ( $upgradesArr ) && $upgradesArr [1] [0] != 'C' && $upgradesArr [1] [0] != 'p') {
				$product = new ProductModel ( $upgradesArr [0] );
				$upgrade = new ProductModel ( $upgradesArr [1] );
				$this->mPackage->AddUpgrade ( $product, $upgrade );
			} else if ($this->IsCorrectKey ( $upgradesArr ) && $upgradesArr [1] [0] == 'p') {
				// Process price
				$splitUpgradesArr = explode ( 'productUpgradePrice', $upgradesArr [1] );
				$product = new ProductModel ( $upgradesArr [0] );
				$upgrade = new ProductModel ( $splitUpgradesArr [1] );
				$this->mPackage->SetUpgradePrice ( $product, $upgrade, $value );
			}
		}

		// Contents - Remove
		$allContents = $this->mPackage->GetContents ();
		foreach ( $allContents as $product ) {
			if (! in_array ( 'PACKAGECONTENTS' . $product->GetProductId (), $this->mClean )) {
				$this->mPackage->RemoveProduct ( $product );
			}
		}

		// Upgrades - Remove
		foreach ( $this->mPackage->GetContents () as $product ) {
			$allUpgrades = $this->mPackage->GetUpgradesFor ( $product );
			foreach ( $allUpgrades as $upgrade ) {
				if (! in_array ( $product->GetProductId () . 'PACKAGEUPGRADES' . $upgrade->GetProductId (), $this->mClean )) {
					$this->mPackage->RemoveUpgrade ( $product, $upgrade );
				}
			}
		}

	} // End function
} // End class


try {
	$package = new PackageModel ( $_POST ['packageId'] );
	$handler = new AdminPackageHandler ( );
	if (isset ( $_POST ['savePackage'] )) {
		$handler->ValidateInput ( $_POST, $package );
		$handler->SavePackage ();
		$registry = Registry::getInstance();
		echo '<h1 style="font-family: Arial; font-size: 12pt;">'.$package->GetDisplayName().' Saved.</h1>';
		echo '<script language="javascript" type="text/javascript">
				self.location.href=\''.$registry->baseDir.'/view/AdminPackageView.php?id='.$_POST['packageId'].'\'
		</script>';
	} elseif (isset ( $_POST ['deletePackageInput'] )) {
		$name = $package->GetDisplayName ();
		$handler->DeletePackage ( $package );
		echo '<h1 style="font-family: Arial; font-size: 12pt;">' . $name . ' Deleted.</h1>';
	}
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>