<?php

class EnlargeProductView extends View {
	
	function __construct() {
		parent::__construct ();
		$this->PublicLayoutHelper = new PublicLayoutHelper ( );
	}
	
	function LoadDefault($productId) {
		$product = new ProductModel ( $productId );
		$this->mPage .= '<a href="javascript:window.close();">';
		$this->mPage .= $this->PublicLayoutHelper->OriginalProductImage ( $product );
		$this->mPage .= '</a>';
		$this->mPage .= '<br /><hr /><br /><a href="javascript:window.close();">Close This Window</a>';
		return $this->mPage;
	}

}

?>