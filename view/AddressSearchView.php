<?php
require_once ('../autoload.php');

class AddressSearchView extends AdminView {
	
	function LoadDefault() {
		parent::__construct(true);
		$this->IncludeJavascript('RequestHandler.js');
		$this->IncludeJavascript('ResponseHandler.js');
		$this->IncludeJavascript('AddressSearchView.js');
		$this->IncludeCss('AddressSearchView.css.php');
		$this->LoadSearchBar ();
		return $this->mPage;
	}
	
	function LoadSearchBar() {
		$registry = Registry::getInstance ();
		$viewDir = $registry->viewDir;
		$this->mPage .= '
			<div id="searchBarContainer">
				<form method="get" action="" name="addressSuggestForm" id="addressSuggestForm" onsubmit="fillInFields()">
					<strong>Search&nbsp;</strong> 
					<input type="text" name="addressSearchText" id="addressSearchText" autocomplete="off" onkeyup="searchSuggest();" /> 
					<input type="hidden" name="id" id="id" /><input type="hidden" name="line1" id="line1" />
					<input type="hidden" name="line2" id="line2" /><input type="hidden" name="line3" id="line3" />
					<input type="hidden" name="selectedCustomerName" id="selectedCustomerName" />
					<input type="hidden" name="selectedCustomerEmail" id="selectedCustomerEmail" />
					<input type="hidden" name="selectedCustomerPhone" id="selectedCustomerPhone" />
					<input type="hidden" name="selectedCity" id="selectedCity" /><input type="hidden" name="selectedCounty" id="selectedCounty" />
					<input type="hidden" name="selectedPostcode" id="selectedPostcode" />
					on 
					<select name="method" id="method">
						<option value="postcode">Postcode</option>
					</select> 
					<input type="submit" value="Go" />
				</form>
				<div id="suggestions"></div>
			</div>
			';
	}

} // End AddressSearchView


$page = new AddressSearchView ( );
echo $page->LoadDefault ();

?>