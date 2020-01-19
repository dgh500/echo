<?php

//! Loads the home page
class ArticlesView extends View {

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
		parent::__construct ('Advice | Echo Supplements');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
		$this->mManufacturerController	= new ManufacturerController;
	}

	//! Main page load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
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
		$this->mPage .= <<<EOT
			<h1>Echo Supplements - Advice</h1>
			<img src="http://www.echosupplements.com/images/articleMuscle.gif" style="float: left; margin-right: 10px;" />
			<h2 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/24/muscle-building-for-beginners">Muscle Building for Beginners</a></h2>
			<p>Just getting started and want to gain some muscle but a bit unsure where to start? Read this article to get an overview of training, diet and supplements suited to the beginner!</p>
			<hr style="clear: both" />

			<img src="http://www.echosupplements.com/images/articleBoditronics.gif" style="float: left; margin-right: 10px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/boditronics">Boditronics Company Profile</a></h3>
			<p>Boditronics company profile with an at-a-glance guide to their most interesting products.</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleGlossary.gif" style="float: left; margin-right: 10px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/20/supplements-glossary">Supplements Glossary?</a></h3>
			<p>Supplements from Aminogen to ZMA explained in 1 or 2 lines, a great quick reference guide!</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleBcaa.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/19/how-many-bcaas-in-a-protein-shake">How Many BCAAs in a Protein Shake?</a></h3>
			<p>Something we get asked fairly often is "do I need a BCAA supplement, I think I get some from my protein shakes already..." - this is usually based on an off-the-cuff comment by someone else that doesn't really understand it. This is a pity as it is pretty simple to work out - so I will give an example using Sci-Mx Ultragen Whey Protein. You should be able to do this on your own tubs, every good protein supplement will give and amino acid profile on the side of the tub!</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleProtein.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/11/protein-comparison">Protein Comparison</a></h3>
			<p>It can be difficult to choose a protein supplement if you have to keep on switching between open windows and reading through the manufacturers descriptions. To make things easier we have compiled a list of the proteins we stock and the protein percentage in each one - this is a fairer comparison as the serving sizes vary a lot!</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articlePre.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/16/pre-workout-comparison">Pre-Workout Comparison</a></h3>
			<p>The pre-workout category of sports supplements is one where you will find a HUGE difference of opinion among people - and also the most difficult to say for sure what is 'working' and what is the placebo effect, where people will convince themselves that they had a good workout because their favourite star was in the advert for the product!</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleAll.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/13/all-in-one-comparison">All In One Comparison</a></h3>
			<p>Buying an all in one supplement is meant to make life easy - as the name suggests you shouldn't really need to buy anything else apart from maybe a multivitamin and extra protein. We have put the values in this table per DAILY serving - as recommended by the manufacturer which is usually pre & post workout so 2 servings.</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleAmino.gif" style="float: left; margin-right: 10px; margin-top: 20px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/12/amino-acid-comparison">Amino Acid Comparison</a></h3>
			<p>Comparing the amino acids supplements on offer is fairly difficult if you are trying to compare the amino acid profile of each one - so we've done it here but to make it fit the page we have labelled the products with numbers, which are the column headings - hopefully this is clear enough! To make a couple of things clearer we have marked up the branched chain amino acids (BCAAs) in bold and the essential amino acids with an asterisk (*).</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleCreatine.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/14/creatine-comparison">Creatine Comparison</a></h3>
			<p>We have divided up our creatine products into some groups to make things easier - these are creatine monohydrate, other creatines and creatine-based products. This should allow us to give a good and fair comparison of all the creatine based products on sale at Echo Supplements!</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleMass.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/15/weight-gainers-comparison">Weight Gainer Comparison</a></h3>
			<p>Buying a weight gainer is fairly simple, you need to know how many calories you want to make up and then what source of carbohydrates you would like to get these calories from. Generally low GI, complex carbs are considered a more 'clean' source of calories than high GI carbs. We have given the macronutrient breakdown (protein/carbs/fat) per 100g of the product, as the serving sizes vary considerably</p>
			<hr style="clear: both; margin-top: 20px;" />

			<img src="http://www.echosupplements.com/images/articleProtein2.gif" style="float: left; margin-right: 10px; margin-top: 15px;" />
			<h3 style="text-decoration: underline"><a href="http://www.echosupplements.com/content/17/different-protein-types">Different Protein Types</a></h3>
			<p>When you are buying protein based supplements you will see many different types of protein listed and the prices vary depending on the type - to help you out with this we have put together a little 'cribsheet' to look at when trying to remember what the difference between a concentrate and a hydrolysate is!</p>
			<hr style="clear: both; margin-top: 20px;" />

EOT;
	} // End LoadMainContentColumn

	//! Load a single manufacturer
	function LoadManufacturerDescriptionSection($manufacturer) {
		if ($manufacturer && $manufacturer->GetSizeChart ()) {
			$sizeChart = $manufacturer->GetSizeChart ();
			$href = $this->mBaseDir . '/content/' . $sizeChart->GetContentId () . '/' . $this->mValidationHelper->MakeLinkSafe ( $manufacturer->GetDisplayName () );
		} else {
			$href = $this->mBaseDir . '/brand/' . $this->mValidationHelper->MakeLinkSafe ( $manufacturer->GetDisplayName () ) . '/' . $manufacturer->GetManufacturerId ();
		}
		$this->mPage .= '<div id="manufacturerDescriptionContainer">';
		$this->mPage .= '<a href="' . $href . '">';
		$this->mPage .= $this->mPublicLayoutHelper->ManufacturerImage ( $manufacturer );
		$this->mPage .= '</a>';
		$this->mPage .= '<div id="manufacturerDescriptionText">';
		$this->mPage .= nl2br($manufacturer->GetDescription());
		$this->mPage .= '</div><!-- Close manufacturerDescriptionText -->';
		$this->mPage .= '</div><!-- Close manufacturerDescriptionContainer --><hr />';
	} // End LoadManufacturerDescriptionSection

} // End ArticlesView


?>