$(document).ready(function() {

	// This makes the details tab focussed on page load
	$("#adminContentViewTabContainer-description").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#descriptionLink").css({backgroundPosition: "100% -150px"});
	
	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('description','contentDescription');
	tabsArray[1] = new Array('image','contentImage');
	
	// When clicking a link, switch to the respective tab
	$("#descriptionLink").click(function() {
		showTab(tabsArray,'description','adminContentViewTabContainer');
	 });

	$("#imageLink").click(function() {
		showTab(tabsArray,'image','adminContentViewTabContainer');
	 });

	// Enable the show/hide content categories
	/*$("#contentNavList").each(
		$(this).bind(
					 "click",
					 function() {
						null;
					 })
					);*/
	
});

function toggleVisible(contentTypeId) {
	var permanentContent = document.getElementById('contentStatus' + contentTypeId);
	
	if(permanentContent.style.display=='block') {
		permanentContent.style.display='none';
	} else {
		permanentContent.style.display='block';		
	}
}