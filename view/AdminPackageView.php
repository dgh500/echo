<?php
require_once ('../autoload.php');

//! View for the admin view of a package
class AdminPackageView extends AdminView {

	//! Obj:PackageModel : The package that is being edited by the administrator
	var $mPackage;

	function __construct() {
		$jsIncludes = array('jqueryUi.js','jquery.alerts.js','AdminPackageView.js','InputListView.js','Tabs.js');
		$cssIncludes = array('admin.css.php','MacFinder.css.php','AdminPackageView.css.php','jquery.alerts.css');
		parent::__construct(true,$cssIncludes,$jsIncludes);
	}

	//! Standard load function - call this on first load. Initialises and loads everything
	/*
	 * @param [in] packageId - The package that is being edited
	 */
	function LoadDefault($packageId) {
		$this->InitialisePackage ( $packageId );
		$this->InitialiseDisplay ();
		$this->LoadTabs ();
		$this->InitialiseContentDisplay ();
		$this->LoadDetailsDisplay ();
		$this->LoadContentsDisplay ();
		$this->LoadUpgradesDisplay ();
		$this->LoadImageDisplay ();
		$this->CloseContentDisplay ();
		$this->CloseDisplay ();
		return $this->mPage;
	}

	//! Initialises the product to be edited
	function InitialisePackage($packageId) {
		$this->mPackage = new PackageModel ( $packageId );
	}

	// Initialise the display - MUST be matched by $this->CloseDisplay()
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminPackageViewContainer">';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	// Loads the tab navigation
	function LoadTabs() {
		$this->mPage .= <<<EOT
		<div id="adminPackageViewTabContainer">
			<ul>
				<li id="adminPackageViewTabContainer-details"><a href="#" id="detailsLink">Details</a></li>
				<li id="adminPackageViewTabContainer-contents"><a href="#" id="contentsLink">Contents</a></li>
				<li id="adminPackageViewTabContainer-upgrades"><a href="#" id="upgradesLink">Upgrades</a></li>
				<li id="adminPackageViewTabContainer-image"><a href="#" id="imageLink">Image</a></li>
			</ul>
		</div>
EOT;
	}

	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminPackageViewContentContainer">
			<form id="adminPackageForm" name="adminPackageForm" method="post" action="{$registry->formHandlersDir}/AdminPackageViewHandler.php">
EOT;
	}

	// Closes the content display
	function CloseContentDisplay() {
		$this->mPage .= '</div><div id="adminPackageFormButtons">
							<input type="submit" value="Save" name="savePackage" id="savePackage" />
							<input type="button" value="Delete" name="deletePackage" id="deletePackage" />
						</div><br /><br />
						<div id="errorBox"></div>
					</form>';
	}

	//! Loads the package contents section
	function LoadDetailsDisplay() {
		if ($this->mPackage->GetOfferOfWeek ()) {
			$offerOfWeek = 'checked';
		} else {
			$offerOfWeek = '';
		}
		$registry = Registry::getInstance ();
		$adminPath = $registry->adminDir;
		$oFCKeditor = new FCKeditor ( 'longDescription' );
		$oFCKeditor->BasePath = $adminPath . '/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = $this->mPackage->GetLongDescription ();
		$oFCKeditor->Height = 350;

		$this->mPage .= <<<EOT
		<div id="detailsContentArea">
			<input type="hidden" name="packageId" id="packageId" value="{$this->mPackage->GetPackageId()}" />
			<div class="halfWidthContainer">
				<label for="displayName">		Display Name:		</label>
					<input type="text" name="displayName" id="displayName" value="{$this->mPackage->GetDisplayName()}" />
			</div>
			<div class="halfWidthContainer">
				<label for="wasPrice">		Was Price:		</label>
					<input type="text" name="wasPrice" id="wasPrice" value="{$this->mPackage->GetWasPrice()}" />
			</div>
			<div class="halfWidthContainer">
			<label for="description">		Description:		</label>
				<input type="text" name="description" id="description" value="{$this->mPackage->GetDescription()}" />
			</div>
			<div class="halfWidthContainer">
				<label for="actualPrice">		Actual Price:		</label>
					<input type="text" name="actualPrice" id="actualPrice" value="{$this->mPackage->GetActualPrice()}" />
			</div>

			<div class="halfWidthContainer">
				<label for="offerOfTheWeek">		Offer of Week:		</label>
					<input type="checkbox" name="offerOfTheWeek" id="offerOfTheWeek" {$offerOfWeek} />
			</div>
			<div class="halfWidthContainer">
			<label for="postage">		Postage:		</label>
				<input type="text" name="postage" id="postage" value="{$this->mPackage->GetPostage()}" />
			</div>
			<br /><br />
EOT;
		$this->mPage .= $oFCKeditor->Create ();
		$this->mPage .= <<<EOT
		</div>
EOT;
	}

	//! Loads the package contents section
	function LoadContentsDisplay() {
		$packageContentsView = new PackageContentsView ( );
		$presentationHelper = new PresentationHelper ( );
		$registry = Registry::getInstance ();
		$this->mPage .= '<div id="contentsContentArea">';
		$this->mPage .= $packageContentsView->LoadDefault ( $this->mPackage->GetCatalogue ()->GetCatalogueId () );
		$this->mPage .= '<div id="packageContentsList">';
		foreach ( $this->mPackage->GetContents () as $product ) {
			$image = $product->GetMainImage ();
			if (NULL === $image) {
				$filename = 'noImage.gif';
			} else {
				$filename = $image->GetFilename ();
			}
			if ($product->GetDescription () == '') {
				$prodDesc = 'No Description';
			} else {
				$prodDesc = $presentationHelper->ChopDown ( $product->GetDescription (), 50, 1 );
			}
			$this->mPage .= <<<EOT
					<input type="hidden" value="PACKAGECONTENTS{$product->GetProductId()}" name="PACKAGECONTENTS{$product->GetProductId()}" id="PACKAGECONTENTS{$product->GetProductId()}" />
					<div class="packageContentProductContainer" id="PACKAGECONTENTSproductContainer{$product->GetProductId()}">
							<img src="{$this->mAdminDir}/images/minusIcon.jpg" class="minusIcon" id="{$product->GetProductId()}" />
								<input 	type="text"
										name="PRODUCTQTY{$product->GetProductId()}"
										id="PRODUCTQTY{$product->GetProductId()}"
										value="{$this->mPackage->GetProductQty($product)}"
										readonly="readonly" />
							<img src="{$this->mAdminDir}/images/plusIcon.jpg" class="plusIcon" id="{$product->GetProductId()}" />
							<strong> x {$product->GetDisplayName()}</strong><br />

					</div>
EOT;
		}
		$this->mPage .= '</div>'; // End packageContentsList
		$this->mPage .= '</div>'; // End contentsContentArea
	}

	//! Loads the package contents section
	function LoadUpgradesDisplay() {
		$presentationHelper = new PresentationHelper ( );
		$registry = Registry::getInstance ();
		$this->mPage .= '<div id="upgradesContentArea">';
		foreach ( $this->mPackage->GetContents () as $product ) {
			$packageUpgradeView = new PackageUpgradesView ( );
			$this->mPage .= '<div class="packageContentContainer">';
			$this->mPage .= '<h2>' . $product->GetDisplayName () . '</h2>';
			$this->mPage .= '<div id="' . $product->GetProductId () . 'packageUpgradesList">';
			foreach ( $this->mPackage->GetUpgradesFor ( $product ) as $upgrade ) {
				$upgradePrice = $this->mPackage->GetUpgradePrice ( $product, $upgrade );
				$this->mPage .= '<div id="' . $product->GetProductId () . 'PACKAGEUPGRADES' . $upgrade->GetProductId () . '">';
				$this->mPage .= '<input type="text"
											class="price"
											name="' . $product->GetProductId () . 'PACKAGEUPGRADESproductUpgradePrice' . $upgrade->GetProductId () . '"
											id="' . $product->GetProductId () . 'PACKAGEUPGRADESproductUpgradePrice' . $upgrade->GetProductId () . '"
											value="' . $upgradePrice . '" />';
				$this->mPage .= '<input type="hidden"
											name="' . $product->GetProductId () . 'PACKAGEUPGRADES' . $upgrade->GetProductId () . '"
											id="' . $product->GetProductId () . 'PACKAGEUPGRADES' . $upgrade->GetProductId () . '"
											value="' . $product->GetProductId () . 'PACKAGEUPGRADES' . $upgrade->GetProductId () . '" />';
				$this->mPage .= $upgrade->GetDisplayName () . '<br /></div>';
			}
			$this->mPage .= '</div><br style="clear: both;" />';
			$this->mPage .= '<a href="#"
								id="showHideHeading' . $product->GetProductId () . '"
								name="showHideHeading' . $product->GetProductId () . '"
								onclick="showHide(\'' . $product->GetProductId () . '\')">
							Show Options
							</a>
							<div id="showHide' . $product->GetProductId () . '" style="display: none">'; // Div is only here to contain the MacView for show/hide
			$this->mPage .= $packageUpgradeView->LoadDefault ( $this->mPackage->GetCatalogue ()->GetCatalogueId (), $product->GetProductId () );
			$this->mPage .= '</div>';
			$this->mPage .= '</div>';
		}
		$this->mPage .= '</div>'; // End upgradesContentArea
	}

	//! Loads the package contents section
	function LoadImageDisplay() {
		$registry = Registry::GetInstance ();
		if (NULL === $this->mPackage->GetImage ()) {
			$image = false;
			$instruction = 'Add an Image';
		} else {
			$image = true;
			$instruction = 'Replace Image';
		}
		$this->mPage .= <<<EOT
			<div id="imageContentArea">
				<strong>{$instruction}</strong><br />

				<iframe src="{$registry->formHandlersDir}/ImageUploadHandler.php?packageId={$this->mPackage->GetPackageId()}"
						id="uploadImageIframe"
						name="uploadImageIframe"
						scrolling="no"
						frameborder="0"
						/></iframe><br />
				<strong>Current Image</strong><br /><br />
EOT;
		if ($image) {
			$image = $this->mPackage->GetImage ();
			$this->mPage .= <<<EOT
					<div class="packageImageContainer">
						<img src="{$registry->rootDir}/{$registry->largeImageDir}{$image->GetFilename()}" alt="{$image->GetAltText()}" />
							<input type="hidden" name="imageId" id="imageId" value="{$image->GetImageId()}" />
					</div>
EOT;
		} else {
			$this->mPage .= 'No Image';
		}

		$this->mPage .= <<<EOT
			</div>
EOT;
	}

} // End AdminPackageView class


$page = new AdminPackageView ( );
if (isset ( $_GET ['id'] )) {
	echo $page->LoadDefault ( $_GET ['id'] );
}
if (isset ( $_GET ['tab'] )) {
	echo '<script language="javascript" type="text/javascript">
			showTab(\'' . $_GET ['tab'] . '\');
			</script>';
}
?>
