<?php
require_once ('../autoload.php');
$registry = Registry::getInstance ();
if (isset ( $_POST ['addProductAttribute'] )) {
	try {
		
		// Validate
		$validator = new ValidationHelper ( );
		// Remove HTML
		$addProductAttribute = $validator->RemoveHtml ( $_POST ['addProductAttribute'] );
		// Trim
		$addProductAttribute = $validator->RemoveWhitespace ( $addProductAttribute );
		// Make HTML eitities safe
		$addProductAttribute = $validator->ConvertHtmlEntities ( $addProductAttribute );
		// Make any (MS)SQL Injection Attack attempts safe
		$addProductAttribute = $validator->MakeMysqlSafe ( $addProductAttribute );
		
		// Process
		$currentProduct = new ProductModel ( $_POST ['productId'] );
		$productAttributeController = new ProductAttributeController ( );
		$newProductAttribute = $productAttributeController->CreateProductAttribute ( $addProductAttribute, $currentProduct, 0 );
		
		// Create values for the new attribute for each SKU
		$skuAttributeController = new SkuAttributeController ( );
		$skus = $currentProduct->getSkus ();
		foreach ( $skus as $sku ) {
			$skuAttributeController->CreateSkuAttribute ( $sku, $newProductAttribute );
		}
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
	
	// Reload
	echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Attribute Added.</div>';
	echo '<script language="javascript" type="text/javascript">
				self.parent.location.href=\'' . $registry->viewDir . '/AdminProductView.php?id=' . $_POST ['productId'] . '&tab=optionsz\';
		</script>';

}
?>
<script language="javascript" type="text/javascript">
	function validateForm(thisform)
	{
		document.getElementById("addProductAttribute").style.border="";
		productAttributeName = document.getElementById('addProductAttribute').value;
		if(productAttributeName==null||productAttributeName=="") { 
			document.getElementById("addProductAttribute").style.border="solid 2px #FF0000";
			document.getElementById("errorBox").style.fontFamily="Arial";
			document.getElementById("errorBox").style.fontSize="10pt";
			document.getElementById("errorBox").innerHTML="Error: The product attribute name cannot be left blank.";
			return false;
		}	
		submitProductFormUpdates();
		return true;
	}
	
	function submitProductFormUpdates() {
		window.parent.document.forms[0].elements['saveProduct'].click();
	}
</script>
<form action="ProductAttributeHandler.php" method="post"
	name="productAttributeForm" id="productAttributeForm"
	onsubmit="return validateForm(this)"><input type="text"
	name="addProductAttribute" id="addProductAttribute" /> <br />
<div id="errorBox"></div>
<input type="hidden" name="productId" id="productId"
	value="<?php
	echo $_GET ['productId'];
	?>" /> <input type="submit"
	value="Add" id="addProductAttributeSubmit"
	name="addProductAttributeSubmit" style="width: auto; margin: 0px;" /><br />
<br />
</form>