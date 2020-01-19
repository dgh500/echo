$(document).ready(function() {
					
	// This makes the details tab focussed on page load
	$("#adminManufacturerViewTabContainer-details").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#detailsLink").css({backgroundPosition: "100% -150px"});
	
	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','manufacturerDetails');
	tabsArray[1] = new Array('image','manufacturerImage');
	
	// When clicking a link, switch to the respective tab
	$("#detailsLink").click(function() {
		showTab(tabsArray,'details','adminManufacturerViewTabContainer');
	 });

	$("#imageLink").click(function() {
		showTab(tabsArray,'image','adminManufacturerViewTabContainer');
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
	
	function DisableHelp() {
		$("#helpToggle").attr("src",$("#helpToggle").attr("src").replace("_on","_off"));		
		$("[name*='manufacturerDisplay']").hoverIntent(	function(){ null },function(){ null } ); 
		$("[name*='manufacturerDescription']").hoverIntent(	function(){ null },function(){ null });
		$("[name*='manufacturerContent']").hoverIntent(function(){ null; },function(){ null; });
		$("#manufacturerImage").hoverIntent(function(){ null; },function(){ null; }); 
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
		$("#manufacturerDisplay").hoverIntent(
			function() {
				$("#manufacturerDisplayHelp").fadeIn('normal');
			},
			function() {
				$("#manufacturerDisplayHelp").fadeOut('normal');
			}
		);
	
		// Help - Description
		$("[name*='manufacturerDescription']").hoverIntent(
			function() {
				$("#manufacturerDescriptionHelp").fadeIn('normal');
			},
			function() {
				$("#manufacturerDescriptionHelp").fadeOut('normal');
			}
		);
	
		// Help - Content ID
		$("[name*='manufacturerContent']").hoverIntent(
			function() {
				$("#manufacturerContentHelp").fadeIn('normal');
			},
			function() {
				$("#manufacturerContentHelp").fadeOut('normal');
			}
		);
	
		// Help - Image
		$("#manufacturerImage").hoverIntent(
			function() {
				$("#manufacturerImageHelp").fadeIn('normal');
			},
			function() {
				$("#manufacturerImageHelp").fadeOut('normal');
			}
		);
	} // End if enable help

	// Delete Click
	$("#deleteMan").click(function() {
		jConfirm('Are you sure you want to delete this?', 'Delete Manufacturer?', function(result) {
			if(result) {
				$('#manufacturerEditForm').append('<input type="hidden" id="deleteManInput" name="deleteManInput" value="1" />');
    			$("#manufacturerEditForm").submit();	
			} else {
	    		jAlert('Not Deleted', 'Delete Manufacturer?');				
			}
			return false;
		});
	}); // End delete click
	
}); // End document ready
