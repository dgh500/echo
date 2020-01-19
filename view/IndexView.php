<?php

//! Loads the home page
class IndexView extends View {

	//! The catalogue to load for
	var $mCatalogue;
	//! Settings to do with the catalogue such as whether to display different components
	var $mSystemSettings;
	//! Deals with managing the basket and any session variables
	var $mSessionHelper;
	//! Holds HTML code for public viewing
	var $mPublicLayoutHelper;
	//! ID of the current basket
	var $mBasketId;

	//! Constructor, sets some member variables based on the catalogue
	function __construct($catalogue) {
		parent::__construct ($catalogue->GetIndexTitle().' > '.$catalogue->GetDisplayName());
		$this->IncludeJs('s3Slider.js');
		$this->IncludeCss('s3Slider.css');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
		$this->mProductController	= new ProductController;
	}

	//! Main page load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= "<script type=\"text/javascript\">
						    $(document).ready(function() {
						        $('#slider1').s3Slider({
						            timeOut: 3500
						        });
						    });
						</script>";
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		$this->LoadTopBrands();
		parent::LoadLeftColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}

	//! Loads the horizontal navigation bar
	function LoadTopBrands() {
		$topBrandsView = new TopBrandsView;
		$this->mPage .= $topBrandsView->LoadDefault($this->mCatalogue);
	}

	//! Loads the centre column
	function LoadMainContentColumn() {

		// Deal of the Day
		$dotdProduct = $this->mProductController->GetDealOfTheWeek($this->mCatalogue);

		// Best Seller
		$bsProduct = $this->mCatalogue->GetBestSellingProduct();

		// Brand New
		$bnProduct = $this->mProductController->GetNewestProduct();

		// Generate HMTL
		$this->mPage .= '
<div id="indexPageContainer">
	<div id="welcomeBox">
		<h1>Echo Supplements - <a href="http://www.echosupplements.com/brand/reflex/25">Reflex Nutrition</a>, <a href="http://www.echosupplements.com/brand/maximuscle/31">Maximuscle</a>, <a href="http://www.echosupplements.com/brand/sci-mx-nutrition/1">Sci-MX Nutrition</a>, <a href="http://www.echosupplements.com/brand/gaspari/7">Gaspari</a> & <a href="http://www.echosupplements.com/brand/met-rx/18">MET-Rx</a></h1>
		<div id="indexBrandNewContainer">
			<img src="'.$this->mBaseDir.'/images/homepageBrandNewBanner.jpg" id="indexBrandNewBanner" width="29" height="145" />
			<h2 id="indexBrandNewHeader"><a href="'.$this->mPublicLayoutHelper->LoadLinkHref($bnProduct).'">'.$bnProduct->GetDisplayName().'</a></h2>
			<a href="'.$this->mPublicLayoutHelper->LoadLinkHref($bnProduct).'">'.$this->mPublicLayoutHelper->MediumProductImage($bnProduct).'</a>
		</div>

		<div id="indexBestSellerContainer">
			<img src="'.$this->mBaseDir.'/images/homepageBestSellerBanner.jpg" id="indexBestSellerBanner" width="29" height="144" />
			<h2 id="indexBestSellerHeader"><a href="'.$this->mPublicLayoutHelper->LoadLinkHref($bsProduct).'">'.$bsProduct->GetDisplayName().'</a></h2>
			<a href="'.$this->mPublicLayoutHelper->LoadLinkHref($bsProduct).'">'.$this->mPublicLayoutHelper->MediumProductImage($bsProduct).'</a>
		</div>
		<div id="welcomeText">
			<a href="http://www.trustpilot.co.uk/review/www.echosupplements.com">
				<img src="'.$this->mBaseDir.'/images/trustpilotBadge.png" style="float: right; margin-top: 5px; border: 0px;" />
			</a>
			<p>At Echo Supplements we are committed to bringing you the best supplements at great prices. We pride ourselves on our customer service and can talk you through the best supplements for your training. We stock all of the top  brands including <a href="http://www.echosupplements.com/brand/sci-mx-nutrition/1">Sci-MX Nutrition</a>, <a href="http://www.echosupplements.com/brand/boditronics/9">Boditronics</a>, <a href="http://www.echosupplements.com/brand/reflex/25">Reflex Nutrition</a>, <a href="http://www.echosupplements.com/brand/met-rx/18">MET-Rx</a>, <a href="http://www.echosupplements.com/brand/usn/17">USN</a>, <a href="http://www.echosupplements.com/brand/maximuscle/31">Maximuscle</a>, <a href="http://www.echosupplements.com/brand/gaspari/7">Gaspari</a> and <a href="http://www.echosupplements.com/brand/biox/23">BioX</a>. In addition we have put together a comprehensive range of supplement packages!</p>
			<p>Delivery is <strong>FREE</strong> on all orders over &pound;45 within the UK and all items are sent on a next day, recorded delivery. You will also get an alert telling you when the delivery will be in a 1 hour timeframe so no more waiting around all day for a parcel! We do also have a bricks and mortar store in Slough - <a href="http://www.echosupplements.com/contact">click here for a map</a> - if you would prefer to browse and take your supplements home the same day. <strong>EVERY</strong> order also comes with a <strong>FREE SAMPLE!</strong></p>
		</div>

		<div id="dealOfTheDayContainer">
			<h2>DEAL OF THE DAY</h2>
			<a href="'.$this->mPublicLayoutHelper->LoadLinkHref($dotdProduct).'">'.$this->mPublicLayoutHelper->MediumProductImage($dotdProduct).'</a>
			<p><b><a href="'.$this->mPublicLayoutHelper->LoadLinkHref($dotdProduct).'">'.$dotdProduct->GetDisplayName().'</a></b><br />'.$this->mPresentationHelper->ChopDown ( $dotdProduct->GetDescription (), 120, 1 ).'</p>
			<div id="dealOfTheDayWasPrice">WAS £'.$dotdProduct->GetWasPrice().'</div>
			<div id="dealOfTheDayNowPrice">TODAY ONLY £'.$dotdProduct->GetActualPrice().'</div>
		</div>
	</div>
	<div id="slider1">
        <ul id="slider1Content">
            <li class="slider1Image">
                <a href="http://www.echosupplements.com/department/protein/gaspari/product/gaspari-myofusion-elite-1841g-4lb/829"><img src="images/galleryEliteSeries.jpg" alt="1" width="785" height="250" /></a>
			</li>
            <li class="slider1Image">
                <a href="http://www.echosupplements.com/brand/ronnie-coleman/49"><img src="images/galleryRonnie.jpg" alt="2" width="785" height="250" /></a>
                <span class="right"><strong>Ronnie Coleman Supplements</strong><br>We stock the full Ronnie Coleman Signature Series range!</span></li>
            <li class="slider1Image">
                <a href="http://www.echosupplements.com/brand/reflex-nutrition/25"><img src="images/galleryReflexChange.jpg" alt="3" width="785" height="250" /></a>
                <span class="bottom"><strong>Reflex Nutrition - Are you ready to change to a superior protein?</strong></span></li>
            <li class="slider1Image">
                <a href="http://www.echosupplements.com/brand/sci-mx-nutrition/1"><img src="images/galleryScimxv2.jpg" alt="4" width="785" height="250" /></a>
                <span class="bottom"><strong>BUY WITH CONFIDENCE - Echo Supplements are an approved Sci-MX Nutrition E-Partner</strong></span></li>
<!--            <li class="slider1Image">
                <a href="http://www.facebook.com/pages/Echo-Supplements/133559329990582"><img src="images/galleryFacebook.jpg" alt="3" width="785" height="250" /></a>
                <span class="bottom"><strong>ECHO SUPPLEMENTS on Facebook - Follow us NOW!</strong></span></li> -->
            <div class="clear slider1Image"></div>
        </ul>
    </div>
</div> <!-- Close indexPageContainer -->
		';
		$this->AddTopBlurb();
	}

	//! Loads the other sites section, using OtherSitesView
	function LoadOtherSites() {
		$otherSites = new OtherSitesView ( );
		$this->mPage .= $otherSites->LoadDefault ();
	}

	//! Adds the top blurb
	function AddTopBlurb() {
		$this->mPage .= $this->mPublicLayoutHelper->TopBlurb ();
	}

	//! Adds the top brands, using TopBrandsView
	function AddTopBrands() {
		$topBrands = new TopBrandsView ( );
		$this->mPage .= $topBrands->LoadDefault ( $this->mCatalogue );
	}

} // End IndexView


?>