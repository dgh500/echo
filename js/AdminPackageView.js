	$(document).ready(function() {

	// This makes the details tab focussed on page load
	$("#adminPackageViewTabContainer-details").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#detailsLink").css({backgroundPosition: "100% -150px"});

	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','details');
	tabsArray[1] = new Array('contents','contents');
	tabsArray[2] = new Array('upgrades','upgrades');
	tabsArray[3] = new Array('image','image');
	
	// When clicking a link, switch to the respective tab
	$("#detailsLink").click(function() {
		showTab(tabsArray,'details','adminPackageViewTabContainer');
	 });

	$("#contentsLink").click(function() {
		showTab(tabsArray,'contents','adminPackageViewTabContainer');
	 });

	$("#upgradesLink").click(function() {
		showTab(tabsArray,'upgrades','adminPackageViewTabContainer');
	 });

	$("#imageLink").click(function() {
		showTab(tabsArray,'image','adminPackageViewTabContainer');
	 });
	
	// Plus button clicked for a product
	$(".plusIcon").click(function() {
		newQty = parseInt($("#PRODUCTQTY" + $(this).attr("id")).val()) + 1;
		$("#PRODUCTQTY" + $(this).attr("id")).val(newQty);
	});
	
	// Plus button clicked for a product
	$(".minusIcon").click(function() {
		newQty = parseInt($("#PRODUCTQTY" + $(this).attr("id")).val()) - 1;
		if(newQty>0) {
			$("#PRODUCTQTY" + $(this).attr("id")).val(newQty);
		}
	});	
	
	// Delete Checks
	$("#deletePackage").click(function() {
		jConfirm('Are you sure you want to delete this?', 'Delete Package?', function(result) {
			if(result) {
				$('#adminPackageForm').append('<input type="hidden" id="deletePackageInput" name="deletePackageInput" value="1" />');
    			$("#adminPackageForm").submit();	
			} else {
	    		jAlert('Not Deleted', 'Delete Package?');				
			}
			return false;
		});
	}); // End delete click	
	
	// Form validation
	$("#adminPackageForm").submit(function() {
		var displayName = $("#displayName").val();
		var description = $("#description").val();	
		var wasPrice = $("#wasPrice").val();	
		var actualPrice = $("#actualPrice").val();	
		var postage = $("#postage").val();	
		
		$("#displayName").css({border: "1px solid #ccc"});
		$("#description").css({border: "1px solid #ccc"});
		$("#wasPrice").css({border: "1px solid #ccc"});
		$("#actualPrice").css({border: "1px solid #ccc"});
		$("#postage").css({border: "1px solid #ccc"});
		
		if(displayName==null||trim(displayName)=="") { 
			$("#displayName").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> The display name cannot be left blank.");
			return false;
		}	
		if(description==null||trim(description)=="") { 
			$("#description").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> The description cannot be left blank.");
			return false;
		}	
		if(wasPrice==null||wasPrice=="") { wasPrice=0; }
		if(actualPrice==null||actualPrice=="") { actualPrice=0; }	
		if(postage==null||postage=="") { postage=0; }
		
		return true;
	});
	
});

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}

function showHide(productId) {
	$("#showHide" + productId).toggle();
}