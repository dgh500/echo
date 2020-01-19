<?php

class FooterView extends View {
	
	function LoadDefault() {
		$registry = Registry::getInstance ();
		$footerContent = new ContentModel ( $registry->footerContent );
		$this->mPage .= $footerContent->GetLongText ();
		return $this->mPage;
	}

}

?>