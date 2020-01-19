$(document).ready(function() {
					
	// This makes the details tab focussed on page load
	$("#adminTagViewTabContainer-details").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#detailsLink").css({backgroundPosition: "100% -150px"});
	
	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','tagDetails');
	tabsArray[1] = new Array('image','tagImage');
	
	// When clicking a link, switch to the respective tab
	$("#detailsLink").click(function() {
		showTab(tabsArray,'details','adminTagViewTabContainer');
	 });

	$("#imageLink").click(function() {
		showTab(tabsArray,'image','adminTagViewTabContainer');
	 });

	// Load / Unload help
	$("#helpToggle").toggle(
		function() {
			LoadHelp();
		},
		function() {
			DisableHelp();
		}
	);
	
	// Delete Click
	$("#deleteTag").click(function() {
		jConfirm('Are you sure you want to delete this?', 'Delete Tag?', function(result) {
			if(result) {
				$('#tagEditForm').append('<input type="hidden" id="deleteTagInput" name="deleteTagInput" value="1" />');
    			$("#tagEditForm").submit();	
			} else {
	    		jAlert('Not Deleted', 'Delete Tag?');				
			}
			return false;
		});
	}); // End delete click
	
	function DisableHelp() {
		$("#helpToggle").attr("src",$("#helpToggle").attr("src").replace("_on","_off"));		
		$("[name*='tagDescription']").hoverIntent(	function(){ null },function(){ null });
		$("#tagImage").hoverIntent(function(){ null; },function(){ null; }); 
	}
	
	function LoadHelp() {
		$("#helpToggle").attr("src",$("#helpToggle").attr("src").replace("_off","_on"));
		
		// Config for sensitive hover - see http://cherne.net/brian/resources/jquery.hoverIntent.html
		var config = {    
			 sensitivity: 1, // number = sensitivity threshold (must be 1 or higher)    
			 interval: 5000, // number = milliseconds for onMouseOver polling interval    
			 timeout: 5000, // number = milliseconds delay before onMouseOut    
		};
		
		// Help - Display
		$("#tagDescription").hoverIntent(
			function() {
				$("#tagDescriptionHelp").fadeIn('normal');
			},
			function() {
				$("#tagDescriptionHelp").fadeOut('normal');
			}
		);
	
		// Help - Description
		$("#tagImage").hoverIntent(
			function() {
				$("#tagImageHelp").fadeIn('normal');
			},
			function() {
				$("#tagImageHelp").fadeOut('normal');
			}
		);
	} // End if enable help


});