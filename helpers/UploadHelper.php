<?php

//! Allows for file uploads
class UploadHelper extends Helper {

	function __construct() {
		parent::__construct();
		($this->mRegistry->debugMode ? $this->mFh = fopen ( '../' . $this->mRegistry->debugDir . '/uploadLog.txt', 'w+' ) : NULL);
	}

	//! Given a valid image generates a thumbnail and uploads it to the small image directory
	/*!
	 * @param [in] fileName : String : The uploaded file (usually $_FILES['foo']['tmp_name']
	 * @param [in] newFilename : String : The desired filename for the output image
	 * @return Bool : True if successful
	 */
	function uploadSmallImage($fileName, $newFilename) {
		$registry = Registry::getInstance ();
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $registry->smallImageSize, '../' . $registry->smallImageDir, $newFilename )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate small thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	function uploadManufacturerImage($fileName, $newFilename, $maxHeight) {
		$registry = Registry::getInstance ();
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $registry->manufacturerImageSize, '../' . $registry->manufacturerImageDir, $newFilename, $maxHeight )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate manufacturer thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			($this->mRegistry->debugMode ? fwrite ( $this->mFh, $error->GetErrorMsg () ) : NULL);
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	function uploadTagImage($fileName, $newFilename, $maxHeight) {
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $this->mRegistry->tagImageSize, '../' . $this->mRegistry->tagImageDir, $newFilename, $maxHeight )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate tag thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			($this->mRegistry->debugMode ? fwrite ( $this->mFh, $error->GetErrorMsg () ) : NULL);
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	function uploadContentImage($fileName, $newFilename, $imageSize) {
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $imageSize, '../' . $this->mRegistry->contentImageDir, $newFilename )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate content thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			($this->mRegistry->debugMode ? fwrite ( $this->mFh, $error->GetErrorMsg () ) : NULL);
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Given a valid image generates a thumbnail and uploads it to the medium image directory
	/*!
	 * @param [in] fileName : String : The uploaded file (usually $_FILES['foo']['tmp_name']
	 * @param [in] newFilename : String : The desired filename for the output image
	 * @return Bool : True if successful
	 */
	function uploadMediumImage($fileName, $newFilename) {
		$registry = Registry::getInstance ();
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $registry->mediumImageSize, '../' . $registry->mediumImageDir, $newFilename )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate medium thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Given a valid image generates a thumbnail and uploads it to the large image directory
	/*!
	 * @param [in] fileName : String : The uploaded file (usually $_FILES['foo']['tmp_name']
	 * @param [in] newFilename : String : The desired filename for the output image
	 * @return Bool : True if successful
	 */
	function uploadLargeImage($fileName, $newFilename) {
		$registry = Registry::getInstance ();
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $registry->largeImageSize, '../' . $registry->largeImageDir, $newFilename )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate large thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Given a valid image generates a thumbnail and uploads it to the original image directory
	/*!
	 * @param [in] fileName : String : The uploaded file (usually $_FILES['foo']['tmp_name']
	 * @param [in] newFilename : String : The desired filename for the output image
	 * @return Bool : True if successful
	 */
	function uploadOriginalImage($fileName, $newFilename) {
		$registry = Registry::getInstance ();
		$imageSize = getimagesize ( $fileName );
		$size = max ( $imageSize [0], $imageSize [1] );
		// Generate thumbnail
		if ($this->GenerateThumbnail ( $fileName, $size, '../' . $registry->originalImageDir, $newFilename )) {
			return true;
		} else {
			$error = new Error ( 'Could not generate large thumbnail on line ' . __LINE__ . ' in file ' . __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Takes a file (usually a $_FILES['userfile']['tmp_name']) and resizes it to $maxSize and calls it $fileName and places it in $outputDir
	//! Supports jpeg, gif, png. (jpg/jpeg are interchangeable)
	//! See http://uk.php.net/manual/en/image.constants.php for the IMAGETYPE_XXX constants list
	/*! The IMAGETYPE_XXX constants and their integer value (as 07/07/2008)
	   Value  Constant
		1	IMAGETYPE_GIF
		2	IMAGETYPE_JPEG
		3	IMAGETYPE_PNG
		4	IMAGETYPE_SWF
		5	IMAGETYPE_PSD
		6	IMAGETYPE_BMP
		7	IMAGETYPE_TIFF_II (intel byte order)
		8	IMAGETYPE_TIFF_MM (motorola byte order)
		9	IMAGETYPE_JPC
		10	IMAGETYPE_JP2
		11	IMAGETYPE_JPX
		12	IMAGETYPE_JB2
		13	IMAGETYPE_SWC
		14	IMAGETYPE_IFF
		15	IMAGETYPE_WBMP
		16	IMAGETYPE_XBM
	*/
	/*!
	 * @param [in] file 	 : The image to resize
	 * @param [in] maxSize 	 : The maximum size of the output image - height or width, the function will work out the ratio
	 * @param [in] outputDir : Where to put the image (in the /small or /medium directories etc.)
	 * @param [in] fileName	 : What to call the resized output image
	 * @param [in] maxHeight : If present, the maxSize variable is used as a max width, and the image is constrained both ways to fit the rectangle
	 * @return Bool : true if successful
	 */
	function GenerateThumbnail($file, $maxSize, $outputDir, $fileName, $maxHeight = false) {
		// Get the image dimensions
		if (! @list ( $width, $height, $type ) = getimagesize ( $file )) {
			$error = new Error ( 'Could not get image size for image: ' . $file . ' <br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
			($this->mRegistry->debugMode ? fwrite ( $this->mFh, $error->GetErrorMsg () ) : NULL);
			throw new Exception ( $error->GetErrorMsg () );
		}
		$extension = image_type_to_extension ( $type, 1 );

		// Figure out the ratio to resize
		if ($width > $height) {
			$new_width = $maxSize;
			$new_height = $height * ($maxSize / $width);
		}
		if ($width < $height) {
			$new_width = $width * ($maxSize / $height);
			$new_height = $maxSize;
		}
		if ($width == $height) {
			$new_width = $maxSize;
			$new_height = $maxSize;
		}
		if ($maxHeight) {
			$new_width = $maxSize;
			$new_height = $maxHeight;
		}

		//! Only jpegs can (reliably) use imagecreatetruecolor() - PHP.net gives a warning about gifs, and it removes any transparency from pngs
		switch ($type) {
			case 1 : //! Gif
				if (! @$image_p = imagecreate ( $new_width, $new_height )) {
					$error = new Error ( 'Could not create new thumbnail image resource.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				if (! @$image = imagecreatefromgif ( $file )) {
					$error = new Error ( 'Could not create image resource from gif file ' . $file . ' supplied.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			case 2 : //! Jpeg
				if (! @$image_p = imagecreatetruecolor ( $new_width, $new_height )) {
					$error = new Error ( 'Could not create new thumbnail image resource.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				if (! @$image = imagecreatefromjpeg ( $file )) {
					$error = new Error ( 'Could not create image resource from jpeg file ' . $file . ' supplied.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			case 3 : //! Png
				if (! @$image_p = imagecreate ( $new_width, $new_height )) {
					$error = new Error ( 'Could not create new thumbnail image resource.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				if (! @$image = imagecreatefrompng ( $file )) {
					$error = new Error ( 'Could not create image resource from png file ' . $file . ' supplied.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			default :
				$error = new Error ( 'This image format (' . image_type_to_extension ( $type, 1 ) . ') is not supported.' );
				fwrite ( $this->mFh, $error->GetErrorMsg () );
				throw new Exception ( $error->GetErrorMsg () );
				break;
		}

		// Copy resampled
		if (! @imagecopyresampled ( $image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height )) {
			$error = new Error ( 'Could not copy resampled image to new image resource.<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
			fwrite ( $this->mFh, $error->GetErrorMsg () );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Output
		switch ($type) {
			case 1 : //! Gif
				if (! @imagegif ( $image_p, $outputDir . $fileName . $extension )) {
					$error = new Error ( 'Could not output the gif thumbnail generated to the output ' . $outputDir . $fileName . $extension . '<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			case 2 : //! Jpeg
				if (! imagejpeg ( $image_p, $outputDir . $fileName . $extension )) {
					$error = new Error ( 'Could not output the jpeg thumbnail generated to the output ' . $outputDir . $fileName . $extension . '<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					#die($error->GetErrorMsg());
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			case 3 : //! Png
				if (! @imagepng ( $image_p, $outputDir . $fileName . $extension )) {
					$error = new Error ( 'Could not output the png thumbnail generated to the output ' . $outputDir . $fileName . '<br />On line ' . (__LINE__ - 1) . ' in file ' . __FILE__ );
					fwrite ( $this->mFh, $error->GetErrorMsg () );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			default :
				$error = new Error ( 'This image format (' . image_type_to_extension ( $type, 1 ) . ') is not supported.' );
				fwrite ( $this->mFh, $error->GetErrorMsg () );
				throw new Exception ( $error->GetErrorMsg () );
				break;
		}
		return true;
	}
}

/* DEBUG
if(isset($_FILES['imageUp'])) {
	try {
		$upHelp = new UploadHelper;
		$prod   = new ProductModel(13);
		$upHelp->uploadSmallImage($_FILES['imageUp']['tmp_name'],'newFileName');
		$upHelp->uploadMediumImage($_FILES['imageUp']['tmp_name'],'newFileName');
		$upHelp->uploadLargeImage($_FILES['imageUp']['tmp_name'],'newFileName');
		$upHelp->uploadOriginalImage($_FILES['imageUp']['tmp_name'],'newFileName');
	} catch(Exception $e) {
		echo $e->GetMessage();
	}
}
<form enctype="multipart/form-data" action="UploadHelper.php" method="post">
Image: <input type="file" name="imageUp" id="imageUp" />
<input type="submit" />
</form>
*/

?>
