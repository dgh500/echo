<?php
require_once ('../autoload.php');

//! Adds a top level category to the database
class AdminCategoryHandler {

	//! Validates the input - actually just initialises the database
	function ValidateInput() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
	}

	//! Saves the category to the database
	/*!
	 * @param $category [in] : Obj:CategoryModel - The category to save
	 * @param $displayName [in] : String - The new display name
	 * @param $description [in] : String - The new description
	 */
	function SaveCategory($category, $displayName, $description) {
		$this->mValidationHelper = new ValidationHelper;
		$category->SetDisplayName ( $displayName );
		$category->SetDescription ( $description );
	}

	//! Deletes a category from the database
	/*!
	 * @param $category [in] : Obj:CategoryModel - The category to delete
	 */
	function DeleteCategory($category) {
		$categoryController = new CategoryController ( );
		$categoryController->DeleteCategory ( $category );
	}

}

try {
	$handler = new AdminCategoryHandler ( );
	$category = new CategoryModel ( $_POST ['categoryId'] );
	if (isset($_POST['deleteCategoryInput'])) {
		$handler->DeleteCategory($category);
	} else {
		$handler->SaveCategory ( $category, $_POST ['displayName'], $_POST ['description'] );
	}

	?>
<script language="javascript" type="text/javascript">
		self.parent.window.frames["productMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>