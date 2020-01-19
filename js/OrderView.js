// Handles Javascript functionality for OrderView

if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

$(document).ready(function() {
				   
	// Hide the change items dialog on loading
	$("#editOrderItemsContainer").hide();

	// Remove Postage
	$("#removePostage").click(function() {
		$.ajax({
			type: "POST",
			url: BASE_DIRECTORY + "/ajaxHandlers/AdminPostageResetAjaxHandler.php",
			data: ('orderId=' + $("#orderId").val()),			
			async: false,
			success: function(data) {
				$("#totalPostageContainer").html("0.00");
				location.reload(true);
			}
		});
	});

	// On edit icon click open a dialog to change the contents
	$(".editOrderItemIcon").click(function() {
		// Get the order item ID								   
		var id = $(this).attr("id");
		var co_ords = new Array(50,50);
		// Store the item ID
		$("#editOrderItemsForm").append('<input type="hidden" name="orderItemId" id="orderItemId" value=" ' + id + '" />');
		
		// Initialise the dialog
		$('#editOrderItemsContainer').dialog({ 
											 autoOpen: false,
											 bgiframe: true,
											 buttons: { "OK": function() {
												 // Make the changes with an AJAX call
												var sageCode 	= $("#sageCodeChangeHidden").val(); 		// The New Sage Code
												var displayName = $("#sageCodeChangeDisplayHidden").val(); 	// The New Display Name
												var price 		= $("#sageCodePriceChangeHidden").val(); 	// The New Price
												
												$.post("../../../ajaxHandlers/SageCodeChangeAjaxHandler.php", 
													   { sageCodeP: sageCode, displayNameP: displayName, priceP: price, orderItemP: id });
												 
												// Copy Down to User
											 	$('#orderItem'+id).html($("#sageCodeChangeDisplayHidden").val());
												$('#unitPriceColumn'+id).html("&pound;" + $("#sageCodePriceChangeHidden").val());
												
												// Close the dialog
												$(this).dialog("close");
												$(this).dialog("destroy");
											 }, "Cancel": function() { $(this).dialog("close"); $(this).dialog("destroy"); } },
											 height: 300,
											 modal: true,
											 position: co_ords,
											 width: 600
											 });
		// Open the dialog
		$("#sageCodeChange").val($("orderItemId").val());
		$('#editOrderItemsContainer').dialog("open");
	}); // End Click
	
	// Sage Code Suggest
	$("#sageCodeChange").keyup(function() {
		// Only perform lookup over 3 characters
		if($("#sageCodeChange").val().length>2) {
			$.get("../../../ajaxHandlers/SageCodeLookupAjaxHandler.php?sofar=" + $("#sageCodeChange").val() + "", function(data) {
				$("#sageCodeLookupArea").html(data);
				
				// Sage Code Suggest Click 
				$(".sageCodeSuggestion a").click(function() {
					$("#sageCodeChange").val($(this).text());
					$("#sageCodeChangeDisplayHidden").val($(this).attr("class"));
					
					// Decode the sage code and price from the ID
					var encodedSageCodeAndPrice = $(this).attr("id");
					arr = encodedSageCodeAndPrice.split('SAGECODE');
					arr2 = arr[1].split('PRICE');
					var price 	 = arr2[1];
					var sageCode = arr[0];
					
					$("#sageCodeChangeHidden").val(sageCode);
					$("#sageCodePriceChangeHidden").val(price);
					
				});
				
			});
		}
	});
		
}); // End document ready

// Handles changes of the status of an order
function OrderStatusEventHandler(changedValue) {
	
	// The div containing the 'reason' drop down menu
	var reasonForCancelContainer = document.getElementById('reasonForCancelContainer');
	
	// Values 6 and 7 are cancelled by merchant/user
	if(parseInt(changedValue)==3 || parseInt(changedValue)==4) {
		reasonForCancelContainer.style.display="block";
	} else {
		reasonForCancelContainer.style.display="none";		
	}
}

// Handles changes of the status of an order
function OrderStatusEventOtherHandler(changedValue) {
	
	// The div containing the 'reason' drop down menu
	var reasonForCancelOtherContainer = document.getElementById('reasonForCancelOtherContainer');
	
	if(changedValue=='otherCancel') {
		reasonForCancelOtherContainer.style.display="block";
	} else {
		reasonForCancelOtherContainer.style.display="none";		
	}
}

// Handles clicking of the 'Update' button
function UpdateOrderButtonHandler() {
	
	// Select menu with the reasons for cancelling an order
	var reasonStatus = document.getElementById('reasonForCancel');
	var orderStatus  = document.getElementById('orderStatus');
	reasonStatus.style.border="1px solid #CCC";
	
	// Check that the user has chosen a reason for cancelling the order
	if(reasonStatus.options[reasonStatus.selectedIndex].value=='NULL' && orderStatus.options[orderStatus.selectedIndex].value=='7') {
		reasonStatus.style.border="2px solid #F00";
		alert('You must select a reason for cancelling the order.');
		return false;
	} else {
		return true;
	}
}