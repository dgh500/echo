<?php

//! Deals with Image tasks (create, delete etc)
class ImageController {
	
	//! Creates a new image in the database then returns this image as an object of type ImageModel
	/*!
	 * @return Obj:ImageModel - the new image
	 */
	function CreateImage() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_image_sql = 'INSERT INTO tblImage (`Main_Image`) VALUES (\'False\')';
		if ($database->query ( $create_image_sql )) {
			$get_latest_image_sql = 'SELECT Image_ID FROM tblImage ORDER BY Image_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_image_sql )) {
				$error = new Error ( 'Could not select new image.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_image = $result->fetch ( PDO::FETCH_OBJ );
			$newImage = new ImageModel ( $latest_image->Image_ID );
			return $newImage;
		} else {
			$error = new Error ( 'Could not create new image.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Attempts to delete an image from the database \todo{AND the filesystem}, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param image : Obj:ImageModel - the image to delete
	 */
	function DeleteImage($image) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_image_sql = 'DELETE FROM tblImage WHERE Image_ID = ' . $image->GetImageId ();
		if (! $database->query ( $delete_image_sql )) {
			$error = new Error ( 'Could not delete image ' . $image->GetImageId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}
	}
	
	//! Retrieves the main image for a supplied $product
	/*! 
	 * @param [in] product : Obj:ProductModel - the product to fetch the main image for
	 * @return Obj:ImageModel - the correct image, or false (if none is set). May also throw an exception
	 */
	function GetMainImageFor($product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_main_image_sql = 'SELECT tblImage.Image_ID FROM tblImage 
								INNER JOIN 
									tblProduct_Images
								ON 
									tblProduct_Images.Image_ID = tblImage.Image_ID
								WHERE tblProduct_Images.Product_ID = ' . $product->GetProductId () . '
								AND tblImage.Main_Image = 1
								LIMIT 1';
		if (! $result = $database->query ( $get_main_image_sql )) {
			$error = new Error ( 'Could not fetch the main image option for product :' . $product->GetProductId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$main_image = $result->fetch ( PDO::FETCH_OBJ );
		if (isset ( $main_image->Image_ID )) {
			$mainImage = new ImageModel ( $main_image->Image_ID );
		} else {
			$mainImage = false;
		}
		return $mainImage;
	}

}

?>