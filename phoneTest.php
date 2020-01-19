<?php

include ('autoload.php');

$packages = array ();
$packageQty = array ();
$packagePrice = array ();
$packageUpgrades = array ();

$packages [] = 32;
$packageQty [32] = 2;
$packagePrice [32] = 643;

$packageUpgrades [32] [] = 426630; // Suunto CB-Double in Line
$packageUpgrades [32] [] = 427606; // Uwatec 3 Gauge 2/1 Console
$packageUpgrades [32] [] = 426656; // Scubapro R395 Octopus


foreach ( $packageUpgrades [32] as $productID ) {
	$product = new ProductModel ( $productID );
	echo 'Upgrade: ' . $product->GetDisplayName () . '(' . $productID . ')<br />';
}
echo '<br /><br />';

foreach ( $packages as $packageID ) {
	// The current package
	$package = new PackageModel ( $packageID );
	for($i = 0; $i < $packageQty [$package->GetPackageId ()]; $i ++) {
		// $packageContents - The products in the package
		$packageContents = $package->GetContents ();
		foreach ( $packageContents as $packageContentProduct ) {
			// $packageContentProductUpgrades - All the upgrades possible for the current product
			$packageContentProductUpgrades = $package->GetUpgradesFor ( $packageContentProduct );
			$found = false;
			foreach ( $packageContentProductUpgrades as $possibleUpgrade ) {
				if (! $found) {
					echo 'Looking at possible upgrade: ' . $packageContentProduct->GetProductId () . ' : ' . $packageContentProduct->GetDisplayName () . ' TO ' . $possibleUpgrade->GetDisplayName () . '<br />';
					$searchForUpgrade = array_search ( $possibleUpgrade->GetProductId (), $packageUpgrades [$package->GetPackageId ()] );
					if ($searchForUpgrade !== false) {
						echo 'Found Upgrade: ' . $possibleUpgrade->GetDisplayName () . '<br />';
						// Does it have attributes > Add to basket
						

						unset ( $packageUpgrades [$package->GetPackageId ()] [$searchForUpgrade] );
						$found = true;
					}
				}
			}
			if (! $found) {
				// Does it have attributes > Add to basket
				echo 'Non-Upgrade: ' . $packageContentProduct->GetDisplayName () . '<br />';
			}
		}
		foreach ( $packageUpgrades [32] as $productID ) {
			$product = new ProductModel ( $productID );
			echo '<br />Upgrade LEFT: ' . $product->GetDisplayName () . '<br />';
		}
		echo '<br /><br />';
	}
}

?>