<?php
require_once ('../autoload.php');

//! Adds a product to the database
class AddProductHandler {
	
	//! String : Display name of the product to add
	var $mDisplayName;
	//! Obj:CategoryModel : The category the product will belong to
	var $mCategory;
	
	//! Expects the category ID and display name
	/*
	 * @param [in] categoryId : The ID of the category that the product is in
	 * @param [in] displayName : The display name of the category
	 * @return true if successful
	 */
	function ValidateInput($categoryId, $displayName) {
		$validator = new ValidationHelper ( );
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check the category exists 
		$sql = 'SELECT Category_ID FROM tblCategory WHERE Category_ID = ' . $categoryId;
		if (0 === $database->query ( $sql )) {
			$error = new Error ( 'Validation of new product failed because the category ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCategory = new CategoryModel ( $categoryId );
		}
		
		// Remove HTML
		$this->mDisplayName = $validator->RemoveHtml ( $displayName );
		// Trim
		$this->mDisplayName = $validator->RemoveWhitespace ( $this->mDisplayName );
		// Make HTML eitities safe
		$this->mDisplayName = $validator->ConvertHtmlEntities ( $this->mDisplayName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mDisplayName = $validator->MakeMysqlSafe ( $this->mDisplayName );
		return true;
	}
	
	//! Insert the category into the database
	/* 
	 * @return true if successful
	 */
	function InsertProduct() {
		$productController = new ProductController ( );
		$newProduct = $productController->CreateProduct ();
		$newProduct->SetDisplayName ( $this->mDisplayName );
		$productController->CreateCategoryLink ( $newProduct, $this->mCategory );
		return true;
	}

}

try {
	$handler = new AddProductHandler ( );
	$handler->ValidateInput ( $_POST ['categoryId'], $_POST ['displayName'] );
	$handler->InsertProduct ();
	?>
<script language="javascript" type="text/javascript">
		self.parent.window.frames["productMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>