<?php

class SubCategoryListView extends View {

	function __construct() {
		parent::__construct ();
	}

	function LoadDefault($category) {
		$this->mCategory = $category;
		$this->mCategoryController = new CategoryController ( );
		$subCategories = $this->mCategoryController->GetAllSubCategoriesOf ( $this->mCategory );
		if (count ( $subCategories ) > 0) {
			$this->mPage .= '<div id="manufacturerListContainer">';
			$this->mPage .= '<ul id="manufacturerList">';
			foreach ( $subCategories as $category ) {
				if (strlen ( str_replace ( '&amp;', '', $category->GetDisplayName () ) ) < 13) {
					$this->mPage .= '
						<li>
							<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/' . $this->mCategory->GetCategoryId () . '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId () . '" style="line-height: 32px;">
							' . $category->GetDisplayName () . '
							</a>
						</li>';
				} else {
					$this->mPage .= '
						<li>
							<a href="' . $this->mBaseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/' . $this->mCategory->GetCategoryId () . '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId () . '">' . $category->GetDisplayName () . '
							</a>
						</li>';
				}
			}
			$this->mPage .= '</ul>';
			$this->mPage .= '</div>';
		}
		return $this->mPage;
	} // End LoadDefault


} // End class


?>