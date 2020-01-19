<?php

include ('../autoload.php');

class ProductAttributeCombinationsView extends View {
	
	function LoadDefault($productId) {
		$product = new ProductModel ( $productId );
		$this->mPage .= '<h2>' . $product->GetDisplayName () . ' Attributes<h2>';
		$productAttributes = $product->GetAttributes ();
		// Start SKUS table section
		$skus = $product->GetSkus ();
		// Only do something if there are attributes...
		if (0 != count ( $productAttributes )) {
			// This is used to have the bottom row span the whole table. Seeded to 3 for the 3 mandatory columns
			$columns = 0;
			$this->mPage .= '<table><tbody id="attributesTable">';
			$this->mPage .= '<tr>';
			foreach ( $productAttributes as $productAttribute ) {
				$attributeOrderArray [] = $productAttribute->GetProductAttributeId ();
				$columns ++;
				$productAttributeIdArray [] = $productAttribute->GetProductAttributeId ();
				$this->mPage .= <<<EOT
					<th>
						<span id="tableHeading{$productAttribute->GetProductAttributeId()}">{$productAttribute->GetAttributeName()}</span> 
					</th>
EOT;
			} // End looping over product attributes
			$this->mPage .= '<th>Price</th></tr>';
		}
		// ****** Start SKU rows ******
		foreach ( $skus as $sku ) {
			$this->mPage .= '<tr>';
			$skuAttributes = $sku->GetSkuAttributes ();
			// Loop over the ordering array - this introduces a nasty triple nested loop, but because almost all products will have 3 or less attributes this shouldnt be an issue
			foreach ( $attributeOrderArray as $key => $index ) {
				// Loop over SKU attributes
				foreach ( $skuAttributes as $skuAttribute ) {
					// Only show the attribute if it corresponds properly to the product attribute in the column
					if ($skuAttribute->GetProductAttributeId () == $index) {
						$this->mPage .= <<<EOT
						<td>
							<input 	type="text" value="{$skuAttribute->GetAttributeValue()}" readonly="readonly" />
						</td>
EOT;
					} // End if
				} // End foreach($skuAttributes...
			} // End foreach($attributeOrderArray...
			$this->mPage .= '<td><input type="text" value="£' . $sku->GetSkuPrice () . '" readonly="readonly" /></td>';
			$this->mPage .= '</tr>';
		} // End foreach($skus...
		// ****** End SKU rows ******
		$this->mPage .= <<<EOT
			</tbody></table>
EOT;
		return $this->mPage;
	}

}

$page = new ProductAttributeCombinationsView ( );
echo $page->LoadDefault ( $_GET ['productId'] );

?>