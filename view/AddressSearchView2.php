<?php
require_once ('../autoload.php');

class AddressSearchView2 extends AdminView {
	
	function LoadDefault() {
		parent::__construct(true);
		$this->IncludeJavascript('RequestHandler.js');
		$this->IncludeJavascript('ResponseHandler.js');
		$this->IncludeJavascript('AddressSearchView.js');
		$this->IncludeCss('AddressSearchView2.css.php');
		$this->LoadSearchBar ();
		return $this->mPage;
	}
	
	function LoadSearchBar() {
		$registry = Registry::getInstance();
		$viewDir = $registry->viewDir;
		$this->mPage .= '
			<div id="searchBarContainer">
				<form method="get" action="" name="addressSuggestForm" id="addressSuggestForm">
					<strong style="margin-left: 50px;">Search&nbsp;</strong> 
					<input type="text" name="addressSearchText" id="addressSearchText" autocomplete="off" onkeyup="searchSuggest();" /> 
					on 
					<select name="method" id="method">
						<option value="postcode">Postcode</option>
					</select> 
				</form>
				<div id="suggestions"></div>
			</div>
			';
	}

} // End AddressSearchView


$page = new AddressSearchView2( );
echo $page->LoadDefault ();

?>