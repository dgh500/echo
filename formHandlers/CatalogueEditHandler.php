<?php

require_once ('../autoload.php');

class CatalogueEditHandler {

	var $mCatalogue;
	var $mClean;

	function ValidateInput($submittedArray, $catalogue) {
		$validator = new ValidationHelper ( );
		$this->mCatalogue = $catalogue;
		foreach ( $submittedArray as $key => $value ) {
			switch ($key) {
				case 'displayName' :
					$this->mClean [$key] = $validator->MakeSafe ( $value );
					break;
				case 'longDescription' :
					$this->mClean [$key] = $value;
					break;
				default :
					$this->mClean [$key] = $submittedArray [$key];
					break;
			}
		}
	}

	function DeleteCatalogue($catalogue) {
		$catalogueController = new CatalogueController ( );
		$catalogueController->DeleteCatalogue ( $catalogue );
	}

	function IsCorrectKey($test) {
		if (2 == count ( $test )) {
			return true;
		} else {
			return false;
		}
	}

	// ValidateInput MUST be called before this!
	function SaveCatalogue() {
		$manufacturerController = new ManufacturerController();
		$dispatchDateController = new DispatchDateController();
		$tagController			= new TagController();

		// Text
		$this->mCatalogue->SetDisplayName($this->mClean['displayName']);
		$this->mCatalogue->SetLongDescription($this->mClean['longDescription']);

		// Loop over the encoded fields (Manufacturers, Tags, Estimates etc.) and edit/delete them as appropriate
		foreach($this->mClean as $key=>$value) {

			// Manufacturers - Edit
			$editManufacturerArr = explode("MANUFACTURERhidden",$key);
			// Ignore any POST values without MANUFACTURERhidden in them
			if ($this->IsCorrectKey ( $editManufacturerArr )) {
				$manufacturerToEdit = new ManufacturerModel ( $editManufacturerArr [1] );
				$manufacturerToEdit->SetDisplayName ( $value );
			}

			// Manufacturers - Delete
			$deleteManufacturerArr = explode ( "DELETEMANUFACTURER", $key );
			// Ignore any POST values without DELETEMANUFACTURER in them
			if ($this->IsCorrectKey ( $deleteManufacturerArr )) {
				$manufacturerToDelete = new ManufacturerModel ( $deleteManufacturerArr [1] );
				$manufacturerController->DeleteManufacturer ( $manufacturerToDelete );
			}

			// Dispatch Dates - Edit
			$editDispatchesArr = explode ( "ESTIMATEhidden", $key );
			// Ignore any POST values without ESTIMATEhidden in them
			if ($this->IsCorrectKey ( $editDispatchesArr )) {
				$dispatchDateToEdit = new DispatchDateModel ( $editDispatchesArr [1] );
				$dispatchDateToEdit->SetDisplayName ( $value );
			}

			// Dispatch Dates - Delete
			$deleteDispatchesArr = explode ( "DELETEESTIMATE", $key );
			// Ignore any POST values without DELETEESTIMATE in them
			if ($this->IsCorrectKey ( $deleteDispatchesArr )) {
				$dispatchDateToDelete = new DispatchDateModel ( $deleteDispatchesArr [1] );
				$dispatchDateController->DeleteDispatchDate ( $dispatchDateToDelete );
			}

			// Tags - Edit
			$editTagsArr = explode("TAGhidden",$key);
			// Ignore any POST values without TAGhidden in them
			if ($this->IsCorrectKey($editTagsArr)) {
				$tagToEdit = new TagModel($editTagsArr[1]);
				$tagToEdit->SetDisplayName($value);
			}

			// Tags - Delete
			$deleteTagsArr = explode("DELETETAG",$key);
			// Ignore any POST values without DELETETAG in them
			if ($this->IsCorrectKey($deleteTagsArr)) {
				$tagToDelete = new TagModel($deleteTagsArr[1]);
				$tagController->DeleteTag($tagToDelete);
			}
		} // End looping over encoded values

	} // End function
} // End class


try {
	$catalogue = new CatalogueModel($_POST['catalogueId']);
	$handler = new CatalogueEditHandler();
	if(isset($_POST['catalogueEditSave'])) {
		$registry = Registry::getInstance();
		$handler->ValidateInput($_POST,$catalogue);
		$handler->SaveCatalogue();
		echo '<h1 style="font-family: Arial; font-size: 12pt;">'.$catalogue->GetDisplayName().' Saved.</h1>';
		echo '<script language="javascript" type="text/javascript">
				self.location.href=\''.$registry->viewDir.'/AdminCatalogueEditView.php?id='.$_POST['catalogueId'].'\'
		</script>';
	} elseif(isset($_POST['catalogueEditDelete'])) {
		$name = $catalogue->GetDisplayName();
		$handler->DeleteCatalogue($catalogue);
		echo '<h1 style="font-family: Arial; font-size: 12pt;">'.$name.' Deleted.</h1>';
		?>
<script language="javascript" type="text/javascript">
				self.parent.window.frames["catalogueMenu"].location.reload();
			</script>
<?php
	}
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>