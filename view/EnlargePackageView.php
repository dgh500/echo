<?php

class EnlargePackageView extends View {
	
	function __construct() {
		parent::__construct ();
		$this->PublicLayoutHelper = new PublicLayoutHelper ( );
	}
	
	function LoadDefault($packageId) {
		$package = new PackageModel ( $packageId );
		$this->mPage .= '<a href="javascript:window.close();">';
		$this->mPage .= $this->PublicLayoutHelper->OriginalPackageImage ( $package );
		$this->mPage .= '</a>';
		$this->mPage .= '<br /><hr /><br /><a href="javascript:window.close();">Close This Window</a>';
		return $this->mPage;
	}

}

?>