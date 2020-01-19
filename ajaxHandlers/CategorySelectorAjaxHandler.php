<?php
$disableJavascriptAutoload = 1;
include_once ('../autoload.php');

class CategorySelectorAjaxHandler extends AjaxHandler {

	//! Prefixed to all IDs to avoid clashes
	var $mPrefix;
	//! The ID of the element to write any output to
	var $mTargetElement;

	//! Sets default header() values
	function __construct() {
		$this->Initialise ();
		$this->SetDataType ( 'xml' );
	}

	//! Handles requests and returns the appropriate response
	function RequestHandler($getArray) {
		$this->mPrefix = $getArray ['prefix'];
		$this->mTargetElement = $getArray ['targetElement'];
		foreach ( $getArray as $key => $value ) {
			switch ($key) {
				case 'addTopLevelCategory' :
					$categoryId = $value;
					$this->HandleAddTopLevelCategory ( $categoryId );
					break;
				case 'openTopLevelCategory' :
					$categoryId = $value;
					$this->HandleOpenTopLevelCategory ( $categoryId );
					break;
				case 'categoryAdd' :
					$categoryId = $value;
					$this->HandleCategoryAdd ( $categoryId );
					break;
				case 'categoryRemove' :
					$categoryId = $value;
					$this->HandleCategoryRemove ( $categoryId );
					break;
			} // End switch
		} // End foreach
	} // End RequestHandler()


	function HandleAddTopLevelCategory($categoryId) {
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>CategorySelector</who>';
		$this->mReturn .= '<what>addTopLevel</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<categoryId>' . $categoryId . '</categoryId>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandleAddTopLevelCategory()


	function HandleOpenTopLevelCategory($categoryId) {
		// Get Subcategories
		$category = new CategoryModel ( $categoryId );
		$categoryController = new CategoryController ( );
		$allSubCategories = $categoryController->GetAllSubCategoriesOf ( $category,true,true );
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>CategorySelector</who>';
		$this->mReturn .= '<what>openTopLevel</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<subCategoryList>';
		foreach ( $allSubCategories as $subCategory ) {
			$this->mReturn .= '<subCategoryId>' . $subCategory->GetCategoryId () . '</subCategoryId>';
			$this->mReturn .= '<subCategoryName>' . htmlspecialchars ( $subCategory->GetDisplayName () ) . '</subCategoryName>';
		}
		$this->mReturn .= '</subCategoryList></root>';
		$this->ReturnResponse ();
	} // End HandleOpenTopLevelCategory


	function HandleCategoryAdd($categoryId) {
		$category = new CategoryModel ( $categoryId );
		$parentCategory = $category->GetParentCategory ();
		if (NULL !== $parentCategory) {
			$parent = $parentCategory->GetDisplayName ();
		} else {
			$parent = 'X';
		}
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>CategorySelector</who>';
		$this->mReturn .= '<what>categoryAdd</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<categoryId>' . $category->GetCategoryId () . '</categoryId>';
		$this->mReturn .= '<categoryName>' . htmlspecialchars ( $category->GetDisplayName () ) . '</categoryName>';
		$this->mReturn .= '<parentCategory>' . htmlspecialchars ( $parent ) . '</parentCategory>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandleCategoryAdd()


	function HandleCategoryRemove($categoryId) {
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>CategorySelector</who>';
		$this->mReturn .= '<what>categoryRemove</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<categoryId>' . $categoryId . '</categoryId>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandleCategoryRemove


} // End CategorySelectorAjaxHandler


$page = new CategorySelectorAjaxHandler ( );
$page->RequestHandler ( $_GET );

?>