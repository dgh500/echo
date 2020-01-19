<?php
@include_once('../autoload.php');

//! New, AJAX-Based Telephone Order Form
class AddOrderBasketTabView extends AdminView {

	function __construct() {
		$cssIncludes = array('jqueryUI.css','jquery.tooltip.css','AddOrderView2.css.php');
		$jsIncludes  = array('jqueryUi.js','jquery.tooltip.min.js','AddOrderView2.js','BasketMacViewHandler.js');
		parent::__construct('1',$cssIncludes,$jsIncludes);
		(isset($_SESSION['catalogueId']) ? $this->mCatalogueId = $_SESSION['catalogueId'] : $this->mCatalogueId = 1 );
		$this->mCatalogue = new CatalogueModel($this->mCatalogueId);
	}

	//! Loads a MacFinder interface, using orderContentsList as the DIV to write the output to, and ORDERCONTENTS as the prefix
	function LoadMacFinder() {
		$productFinder = new BasketMacFinderView();
		$this->mPage .= $productFinder->LoadDefault($this->mCatalogue, true );
	}

	//! Loads a MacFinder interface, using orderContentsList as the DIV to write the output to, and ORDERCONTENTS as the prefix
	function LoadBasketContents() {
		$productFinder = new BasketContentsView($this->mCatalogue);
		$this->mPage .= $productFinder->LoadDefault($this->mCatalogueId, true );
	}

	function LoadDefault() {
		$catalogueController = new CatalogueController;
		$this->mPage .= '<div id="catalogueSelection">';
		foreach($catalogueController->GetAllCatalogues() as $catalogue) {
			$this->mPage .= '<a href="'.$this->mViewDir.'/AddOrderView2.php?b=1" id="'.$catalogue->GetCatalogueId().'">'.$catalogue->GetDisplayName().'</a> | ';
		}
		$this->mPage = substr($this->mPage,0,strlen($this->mPage)-3); // Remove the pipe (|) separator after the last catalogue
		$this->mPage .= ' | <a href="#" id="misc" name="misc">#Misc Item</a>';
		$this->mPage .= '</div>';
		$this->mPage .= '<div id="basketTab">';
			$this->mPage .= '<div id="basketFinder">';
			$this->LoadMacFinder();
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="basketContents">';
			$this->LoadBasketContents();
			$this->mPage .= '</div>';
		$this->mPage .= '</div>';
		return $this->mPage;
	} // End LoadDefault();

} // End AddOrderCustomerTabView

$page = new AddOrderBasketTabView;
echo $page->LoadDefault();

?>