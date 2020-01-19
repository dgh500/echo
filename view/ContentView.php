<?php

//! Loads a single content/article
class ContentView extends View {

	var $mSessionHelper;

	// Init
	function __construct($catalogue, $content) {
		// Params
		$this->mCatalogue 	= $catalogue;
		$this->mContent 	= new ContentModel($content);

		// CSS
		$cssIncludes = array('ContentView.css.php','Category.css.php');

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > '.$this->mContent->GetDisplayName(),$cssIncludes);

		// Member vars
		$this->mContentController 	= new ContentController ( );
		$this->mSessionHelper 		= new SessionHelper ( );
	} // End __construct

	//! Generic load function
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

	//! Loads the content
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadContent();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()


	//! Loads the content itself (long text etc.
	function LoadContent() {
		// Open container
		$this->mPage .= '<div id="contentContainer">';

		// If this is a manufacturer page then display their products etc. otherwise display the header image and long text
		if ($this->mContentController->IsAManufacturerPage($this->mContent)) {
			$this->LoadManufacturerContentDisplay();
		} else {
			$this->LoadArticleContentDisplay();
		}

		// Close container
		$this->mPage .= '</div>';
	}

	//! Loads the display for article content (header image etc.)
	function LoadArticleContentDisplay() {
		// Display a header image if the content has one
		if($this->mContent->GetHeaderImage()) {
			$imageUrl = $this->mPublicLayoutHelper->ArticleHeaderImage($this->mContent);
			$this->mPage .= $imageUrl;
		}
		// Display the article text
		$this->mPage .= $this->mContent->GetLongText();
	} // End LoadArticleContentDisplay()

	//! Loads the display for a manufacturer page - so it includes all their products etc.
	function LoadManufacturerContentDisplay() {
		// Display the manufacturer text
		$this->mPage .= $this->mContent->GetLongText();

		// Init some controllers
		$this->mManufacturerController 	= new ManufacturerController();
		$this->mCategoryController 		= new CategoryController();

		// Which manufacturer?
		$this->mManufacturer 			= $this->mContentController->GetManufacturerFor($this->mContent);

		// Set relative anchor
		$this->mPage .= '<h3>'.$this->mManufacturer->GetDisplayName().' Products</h3><a name="products" id="products"></a>';

		// Get all categories that have products from this manufacturer
		$allCategories = $this->mManufacturerController->GetAllCategoriesIn($this->mManufacturer);

		// Show a product from each category
		foreach($allCategories as $category) {

			// Pick a product
			$product = $this->mCategoryController->GetAProductIn ( $category );

			// Show the parent category if you can (This stops them all saying 'ManufacturerName' if they are in 'Category > ManufacturerName'
			if ($category->GetParentCategory()) {
				$displayCategory = $category->GetParentCategory();
			} else {
				$displayCategory = $category;
			}

			// Show the content
			$this->mPage .= '<strong>'.$this->mManufacturer->GetDisplayName().'</strong> > ';
			$this->mPage .= '<strong><a href="' . $this->mBaseDir . '/department/' . $displayCategory->GetDisplayName () . '/' . $category->GetCategoryId () . '">';
			$this->mPage .= $displayCategory->GetDisplayName () . '</a></strong><br />';
			$categoryListProductView = new CategoryListProductView ( );
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
			$this->mPage .= '
				<div style="float: right;">
				<strong>...
					<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId () . '">
						All ' . $this->mManufacturer->GetDisplayName () . ' ' . $displayCategory->GetDisplayName () . '
					</a>
				</strong></div><br style="clear: both" /><br /><br />
				';
		}
		$this->mPage .= '<a href="#top"><strong>Back to Top</strong></a>';
	} // End LoadManufacturerContentDisplay()

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
}

?>