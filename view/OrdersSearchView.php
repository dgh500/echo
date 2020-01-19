<?php

class OrdersSearchView extends AdminView {
	
	function LoadDefault() {
		parent::__construct(true);
		$this->IncludeJavascript('RequestHandler.js');
		$this->IncludeJavascript('ResponseHandler.js');
		$this->IncludeJavascript('OrdersSearchView.js');
		$this->IncludeCss('admin/css/OrdersSearchView.css.php',false);
		$this->LoadSearchBar();
		$this->LoadOrderArea();
		return $this->mPage;
	}
	
	function LoadSearchBar() {
		$this->mPage .= '
			<div id="searchBarContainer">
				<form method="get" action="' . $this->mViewDir . '/OrdersEditView.php" name="orderSuggestForm" id="orderSuggestForm">
					<strong>Search&nbsp;</strong> 
					<input type="text" name="ordersSearchText" id="ordersSearchText" autocomplete="off" onkeyup="searchSuggest();" /> 
					<input type="hidden" name="id" id="id" />
					on 
					<select name="method" id="method">
						<option value="orderNumber">Order Number</option>
						<option value="postcode">Postcode</option>
						<option value="lastName">Last Name</option>
					</select> 
					<input type="submit" value="Go" />
				</form>
				<div id="suggestions"></div>
			</div>
			';
	}
	
	function LoadOrderArea() {
		$this->mPage .= '<div style="float: left;"><iframe src="' . $this->mViewDir . '/OrdersEditView.php" name="ordersEdit" id="ordersEdit" frameborder="0" border="0"></iframe></div>';
	}

} // End OrdersSearchView


$page = new OrdersSearchView ( );
echo $page->LoadDefault ();

?>