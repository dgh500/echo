<?php

class OffersOfTheWeekView extends View {
	
	function LoadDefault($catalogue) {
		$productController = new ProductController ( );
		$productsOfTheWeek = $productController->GetOffersOfTheWeek ( $catalogue, 3 );
		$this->mPage .= <<<HTMLOUTPUT
		
			<div id="offersOfTheWeekContainer">
				<img src="images/offersOfTheWeekTitle.gif" id="offersOfTheWeekTitle" alt="Offers of the Week" />
				<div id="offersOfTheWeekContent">
HTMLOUTPUT;
		$i = 0;
		foreach ( $productsOfTheWeek as $product ) {
			$linkTo = $this->mPublicLayoutHelper->LoadLinkHref ( $product );
			$this->mPage .= <<<HTMLOUTPUT
					<div class="offerContainer">
						<div class="offerImageContainer">
							<table><tr><td>
								<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumProductImage($product)}</a>
							</td></tr></table>
						</div> <!-- Close offerImageContainer -->
						<h3><a href="{$linkTo}">{$this->mPresentationHelper->ChopDown($product->GetDisplayName(),25)}</a></h3>
						<h6>Now &pound;{$this->mPresentationHelper->Money($product->GetActualPrice())}</h6>
					</div> <!-- Close offerContainer -->
HTMLOUTPUT;
		}
		$this->mPage .= <<<HTMLOUTPUT
				</div> <!-- Close offersOfTheWeekContent -->
			</div> <!-- Close offersOfTheWeekContainer -->
			<a href="{$this->mBaseDir}/offers"><img src="images/offersOfWeekFooter.jpg" alt="For all offers of the week click here!" /></a>
HTMLOUTPUT;
		return $this->mPage;
	}

}

?>