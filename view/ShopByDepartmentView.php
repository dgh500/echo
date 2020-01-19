<?php

//! Generates the shop by department menu present of all pages
class ShopByDepartmentView extends View {

	function LoadDefault($catalogue) {
		$registry 				= Registry::getInstance ();
		$this->mCatalogue 		= $catalogue;
		$this->mSystemSettings 	= new SystemSettingsModel($this->mCatalogue);
		$categoryController 	= new CategoryController ( );
		$manufacturerController = new ManufacturerController ( );
		if ($registry->hasAdmin) {
			$dir = $this->mSecureBaseDir;
		} else {
			$dir = $this->mBaseDir;
		}

		// Shop by department section
		$this->mPage .= '<div class="section">
					<div class="sectionHeader">CATEGORIES</div>
					<div class="sectionBody"><ul>
						';
		$allCategories = $categoryController->GetAllTopLevelCategoriesForCatalogue ( $this->mCatalogue );

		// Display packages
		if ($this->mSystemSettings->GetShowPackages()) {
			$packagesCategory = $this->mCatalogue->GetPackagesCategory ();
				$this->mPage .= '
	<li>
		<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $packagesCategory->GetDisplayName () ) . '/' . $packagesCategory->GetCategoryId () . '">
			'.$packagesCategory->GetDisplayName().'
		</a>
	</li>';
		}

		// Display regular categories
		foreach ( $allCategories as $category ) {
			if ('' != $category->GetDisplayName ()) {
				$this->mPage .= '
	<li>
		<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId () . '">
			'.$category->GetDisplayName().'
		</a>
	</li>';
			}
		}
		$this->mPage .= '</ul></div> <!-- Close sectionBody --> </div> <!-- End section -->';

		/********** BRANDS *********/
		$this->mPage .= '
				<div class="section">
					<div class="sectionHeader">BRANDS</div>
					<div class="sectionBody"><ul>';
		// Shop by brand section
		$allManufacturers = $manufacturerController->GetAllManufacturersFor ( $this->mCatalogue, false );
		foreach ( $allManufacturers as $manufacturer ) {
			$url = $this->mPublicLayoutHelper->LoadManufacturerHref($manufacturer);
			$this->mPage .= '<li><a href="'.$url.'">'.$manufacturer->GetDisplayName().'</a></li>';
		}//baseDir + '/brand/' + BrandName + '/' + BrandId;
		$this->mPage .= '
					</ul></div> <!-- End sectionBody --> </div><!-- End section -->';
		return $this->mPage;
	}

}

?>