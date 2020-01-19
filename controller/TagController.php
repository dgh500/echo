<?php

//! Deals with tag tasks (create, delete etc)
class TagController extends Controller {
	
	function __construct() {
		parent::__construct();
		$this->mRegistry = Registry::getInstance ();
		$this->mDatabase = $this->mRegistry->database;
	}
	
	//! Creates a new dispatch date in the database then returns this date as an object of type DispatchDateModel
	/*!
	 * @return Obj:DispatchDateModel - the new dispatch date
	 */
	function CreateTag($displayName) {
		$sql = 'INSERT INTO tblTag (`Display_Name`) VALUES (\'' . $displayName . '\')';
		if ($this->mDatabase->query ( $sql )) {
			$sql = 'SELECT Tag_ID FROM tblTag ORDER BY Tag_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not select new tag.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$newTag = new TagModel ( $resultObj->Tag_ID );
			return $newTag;
		} else {
			$error = new Error ( 'Could not insert tage' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Creates a link between a tag and a catalogue (Eg. so 'Gain Weight' goes into supplements)
	function CreateCatalogueTagLink($catalogue,$tag) {
		// Create an array to store the IDs - so can compare IDs, not objects
		$catTagIds = array();
		// The tags related to this catalogue
		$catTags = $catalogue->GetTags();
		foreach($catTags as $tag) {
			$catTagIds[] = $tag->GetTagId();	
		}
		
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (!in_array($tag->GetTagId(),$catTagIds)) {
			$sql = 'INSERT INTO tblCatalogue_Tags (`Catalogue_ID`,`Tag_ID`) VALUES ('.$catalogue->GetCatalogueId().','.$tag->GetTagId().')';
			if(FALSE === $this->mDatabase->query($sql)) {
				$error = new Error('Problem creating link between catalogue '.$catalogue->GetCatalogueId().' and tag '.$tag->GetTagId().' with SQL:<br />'.$sql);
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Creates a link between a tag and a product (Eg. so 'Gain Weight' for 'Pharma Gain')
	function CreateProductTagLink($product,$tag) {
		// Create an array to store the IDs - so can compare IDs, not objects
		$prodTagIds = array();
		// The tags related to this product
		$prodTags = $product->GetTags();
		foreach($prodTags as $tag) {
			$prodTagIds[] = $tag->GetTagId();	
		}
		
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (!in_array($tag->GetTagId(),$prodTagIds)) {
			$sql = 'INSERT INTO tblProduct_Tags (`Product_ID`,`Tag_ID`) VALUES ('.$product->GetProductId().','.$tag->GetTagId().')';
			if(FALSE === $this->mDatabase->query($sql)) {
				$error = new Error('Problem creating link between product '.$product->GetProductId().' and tag '.$tag->GetTagId().' with SQL:<br />'.$sql);
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to remove a link between a product and a tag, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] tag : Obj:TagModel	 
	*/
	function RemoveProductTagLink($product,$tag) {
		$sql = 'DELETE FROM tblProduct_Tags WHERE Tag_ID = '.$tag->GetTagId().' AND Product_ID = '.$product->GetProductId();
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and tag ' . $tag->GetTagId () . ' with SQL:<br /> ' . $sql );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Attempts to delete a tag from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] tag : Obj:TagModel - the tag to delete
	 */
	function DeleteTag($tag) {
		// Delete the links to products
		$sql = 'DELETE FROM tblProduct_Tags WHERE Tag_ID = '.$tag->GetTagId();
		if ($this->mDatabase->query($sql) === false) {
			$error = new Error('Could not delete tag links for '.$tag->GetTagId());
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->getErrorMsg());
		}
		// Delete the tag itself
		$sql = 'DELETE FROM tblTag WHERE Tag_ID = '.$tag->GetTagId();
		if (!$this->mDatabase->query($sql)) {
			$error = new Error('Could not delete tag '.$tag->GetTagId());
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->getErrorMsg());
		} else {
			return true;
		}
	} // End DeleteTag

	//! Returns the number of products that is tagged with this tag
	/*!
	 * @param $tag Obj : TagModel - The tag to check
	 * @param $category Obj : CategoryModel - Constrain by a category - defaults to false
	 * @return Int - The number of products they make
	 */
	function CountProductsIn($tag, $category = false) {
		if ($category) {
			$categoryConstraintSql = '
			INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_Tags.Product_ID
			INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
			WHERE tblCategory.Category_ID = ' . $category->GetCategoryId () . ' AND ';
		} else {
			$categoryConstraintSql = ' WHERE ';
		}
		$sql = 'SELECT COUNT(DISTINCT tblProduct_Tags.Product_ID) AS productCount FROM tblProduct_Tags 
				'.$categoryConstraintSql.' tblProduct_Tags.Tag_ID = '.$tag->GetTagId();
		if (! $result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not count products.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->productCount;
	}

	//! Gets all tags that are in the supplied $catalogue
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the tags for
	 * @param [in] $empty : Boolean - Whether or not to include empty tags
	 * @return Array of Obj:TagModel or an exception
	 */
	function GetAllTagsFor($catalogue, $empty = true) {
		if ($empty) {
			$sql = 'SELECT tblTag.Tag_ID 
						FROM tblTag
						INNER JOIN tblCatalogue_Tags
							ON tblCatalogue_Tags.Tag_ID = tblTag.Tag_ID
						WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
						ORDER BY Display_Name ASC
									';
		} else {
			$sql = 'SELECT tblTag.Tag_ID 
						FROM tblTag 
						INNER JOIN tblCatalogue_Tags
							ON tblCatalogue_Tags.Tag_ID = tblTag.Tag_ID						
						WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
						AND tblTag.Tag_ID IN (SELECT Tag_ID FROM tblProduct_Tags)
						ORDER BY Display_Name ASC
									';
		}
		if (!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch all tags.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$tags = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $tags as $resultObj ) {
			$newTag = new TagModel($resultObj->Tag_ID );
			$retTags [] = $newTag;
		}
		if (0 == count ( $tags )) {
			$retTags = array ();
		}
		return $retTags;
	}

	//! Gets all of the categories with products tagged by $tag
	/*!
	 * @param $tag Obj:TagModel - The tag to check
	 * @return Array of CategoryModel objects
	 */
	function GetAllCategoriesIn($tag) {
		$sql = 'SELECT DISTINCT
					tblCategory.Category_ID,
					tblCategory.Display_Name
				FROM
					tblCategory
				INNER JOIN tblCategory_Products
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				INNER JOIN tblProduct
					ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblProduct_Tags 
					ON tblProduct_Tags.Product_ID = tblProduct.Product_ID
				WHERE tblProduct_Tags.Tag_ID = '.$tag->GetTagId().'
				ORDER BY tblCategory.Display_Name ASC';
		$result = $this->mDatabase->query ( $sql );
		$retCats = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$tempCat = new CategoryModel ( $resultObj->Category_ID );
			$retCats [] = $tempCat;
		}
		return $retCats;
	} // End GetAllCategoriesIn()
	
	//! Gets those products that are made by this tag
	/*!
	 * @param $tag Obj:TagModel - The tag to scan
	 * @param $numberOfProducts [in] : Int : The number of products to retrieve (default 1) (product per page)
	 * @param $sortBy [in] : String - Which field to sort the data by (Defaults Actual_Price)
	 * @param $sortDirection [in] : String - ASC(ending) or DESC(ending)
	 * @param $pageNumber : Which page is required - IE. From 0..X or X..Y etc.
	 * @param $category [in] : Obj : CategoryModel Optional - if present then constrains the function to the given category (and required manufacturer)
	 * @return Array of Obj:ProductModel - those products that satisfy the requirements
	 */
	function GetProductsIn($tag, $numberOfProducts = 1, $sortBy = 'Actual_Price', $sortDirection = 'ASC', $pageNumber = 1, $category = false) {
		if ($sortDirection == 'ASC') {
			$opposite = 'DESC';
		} else {
			$opposite = 'ASC';
		}
		if ($sortBy == 'Display_Name') {
			$firstSortBySql = ' ';
			$secondSortBySql = ' ';
		} else {
			$firstSortBySql = ' ' . $sortBy . ' ' . $sortDirection . ', ';
			$secondSortBySql = ' ' . $sortBy . ' ' . $opposite . ', ';
		}
		
		$endLimit = $pageNumber * $numberOfProducts;
		$totalProducts = $this->CountProductsIn ( $tag );
		if ($numberOfProducts * $pageNumber > $totalProducts) {
			$numberOfProducts = $numberOfProducts - (($numberOfProducts * $pageNumber) - $totalProducts);
		}
		
		if ($category) {
			$categoryConstraintSql = ' AND tblCategory_Products.Category_ID = ' . $category->GetCategoryId ();
		} else {
			$categoryConstraintSql = ' ';
		}
		$sql = '
				SELECT * FROM (
					SELECT DISTINCT * FROM (
							SELECT DISTINCT 
								tblCategory_Products.Product_ID,
								tblProduct_Text.Display_Name,
								tblProduct.Actual_Price
							FROM
								tblCategory_Products
							INNER JOIN tblProduct
								ON tblProduct.Product_ID = tblCategory_Products.Product_ID
							INNER JOIN tblProduct_Text
								ON tblProduct.Product_ID = tblProduct_Text.Product_ID
							INNER JOIN tblProduct_Tags
								ON tblProduct_Tags.Product_ID = tblProduct.Product_ID
							WHERE tblProduct_Tags.Tag_ID = ' . $tag->GetTagId () . '
							' . $categoryConstraintSql . '
							ORDER BY ' . $sortBy . ' ' . $sortDirection . '
							LIMIT ' . $endLimit . '
						) AS foo ORDER BY ' . $sortBy . ' ' . $opposite . ' LIMIT ' . $numberOfProducts . '
					) AS bar ORDER BY ' . $sortBy . ' ' . $sortDirection . '
					';
		#echo $sql;					
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not sort products: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$products = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $products as $product_id ) {
			$newProduct = new ProductModel ( $product_id->Product_ID );
			$retProducts [] = $newProduct;
		}
		if (0 == count ( $products )) {
			$retProducts = array ();
		}
		return $retProducts;
	} // End GetProductsIn()	

} // End TagController

?>