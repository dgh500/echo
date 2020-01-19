<?php

class RecentlyViewedView extends View {
	
	function __construct() {
		parent::__construct ();
	}
	
	function LoadDefault($product) {
		$this->IncludeCss('RecentlyViewedView.css.php');
		if (method_exists ( $product, 'GetProductId' )) {
			$productBreadCrumb = new ProductBreadCrumbView ( );
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( );
			$linkHref = $productBreadCrumb->LoadLinkHref ( $product );
			$prodTitle = substr ( $product->GetDisplayName (), 0, 20 );
			$this->mPage .= <<<HTMLOUTPUT
				<div id="recentlyViewedContainer">
					<img src="{$this->mBaseDir}/images/recentlyViewedTitle.gif" id="recentlyViewedTitle" alt="Recently Viewed Items" />
					<div id="recentlyViewedContent">
					<a href="{$linkHref}">
HTMLOUTPUT;
			$this->mPage .= '<div id="recentlyViewedProductContainer">';
			$this->mPage .= $this->mPublicLayoutHelper->SmallProductImage ( $product );
			$this->mPage .= '</div>';
			$this->mPage .= <<<HTMLOUTPUT
					</a>
						<h4><a href="{$linkHref}">{$prodTitle}</a></h4>
					</div>
				</div>
HTMLOUTPUT;
		} else {
			$package = $product;
			$packageBreadCrumb = new PackageBreadCrumbView ( );
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( );
			$linkHref = $packageBreadCrumb->LoadLinkHref ( $package );
			$prodTitle = substr ( $package->GetDisplayName (), 0, 20 );
			$this->mPage .= <<<HTMLOUTPUT
				<div id="recentlyViewedContainer">
					<img src="{$this->mBaseDir}/images/recentlyViewedTitle.gif" id="recentlyViewedTitle" />
					<div id="recentlyViewedContent">
					<a href="{$linkHref}">
HTMLOUTPUT;
			$this->mPage .= '<div id="recentlyViewedProductContainer">';
			$this->mPage .= $this->mPublicLayoutHelper->SmallPackageImage ( $package );
			$this->mPage .= '</div>';
			$this->mPage .= <<<HTMLOUTPUT
					</a>
						<h4><a href="{$linkHref}">{$prodTitle}</a></h4>
					</div>
				</div>
HTMLOUTPUT;
		}
		return $this->mPage;
	} // End LoadDefault


}

?>