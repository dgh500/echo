<?php

class ProductSearchView extends View {

	function LoadDefault() {
		$this->mPage .= <<<HTMLOUTPUT
				<div id="searchBar"><div>
					<form method="post" id="productSearchForm" name="productSearchForm" action="{$this->mFormHandlersDir}/ProductSearchHandler.php" accept-charset="utf-8">
						<img src="{$this->mBaseDir}/images/searchIcon.png" width="24" height="20" id="searchIcon" name="searchIcon" />
						<input type="text" name="q" id="q" />
						<input type="image" src="{$this->mBaseDir}/images/searchButton.png" id="searchButton" name="searchButton" />
					</form>
				</div></div>
HTMLOUTPUT;
		return $this->mPage;
	}
}

?>