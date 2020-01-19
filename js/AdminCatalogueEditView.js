$(document).ready(function() {
					
	// This makes the details tab focussed on page load
	$("#adminCatalogueViewTabContainer-details").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#detailsLink").css({backgroundPosition: "100% -150px"});
	
	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','catalogueDetails');
	tabsArray[1] = new Array('manufacturers','catalogueManufacturers');
	tabsArray[2] = new Array('tags','catalogueTags');
	tabsArray[3] = new Array('estimates','catalogueEstimates');
	
	// When clicking a link, switch to the respective tab
	$("#detailsLink").click(function() {
		showTab(tabsArray,'details','adminCatalogueViewTabContainer');
	 });

	$("#manufacturersLink").click(function() {
		showTab(tabsArray,'manufacturers','adminCatalogueViewTabContainer');
	 });

	$("#tagsLink").click(function() {
		showTab(tabsArray,'tags','adminCatalogueViewTabContainer');
	 });

	$("#estimatesLink").click(function() {
		showTab(tabsArray,'estimates','adminCatalogueViewTabContainer');
	 });

});

