<?php

class TopBrandsView extends View {

	function LoadDefault($catalogue) {
		$publicLayoutHelper = new PublicLayoutHelper ( );
		$this->mPage .= '
			<div id="topBrands">';
		$manufacturerController = new ManufacturerController ( );
		$allManufacturers = $manufacturerController->GetNManufacturersFor($catalogue,5);
		foreach ( $allManufacturers as $manufacturer ) {
			if ($manufacturer->GetDisplay ()) {
				$linkTo = $this->mBaseDir . '/brand/' . $this->mValidationHelper->MakeLinkSafe ( trim ( $manufacturer->GetDisplayName () ) ) . '/' . $manufacturer->GetManufacturerId ();
				$this->mPage .= '
				<div class="singleTopBrand">
					<a href="' . $linkTo . '">';
				$this->mPage .= $publicLayoutHelper->ManufacturerImage ( $manufacturer );
				$this->mPage .= '</a>
				</div> <!-- Close singleTopBrand -->';
			}
		}
		$this->mPage .= '
		<div id="allBrandsLinkContainer">
			<a href="' . $this->mBaseDir . '/brands">
				<img src="images/allBrandsLink.png" alt="All Brands" width="29" height="100" />
			</a>
		</div> <!-- Close allBrandsLinkContainer -->
		</div> <!-- Close topBrands -->
		<br style="clear: both;" />
		';
		return $this->mPage;
	}

}

?>