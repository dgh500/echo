function updatePostageMethod() {
	countryDropDownForm.submit();
}

$(document).ready(function() {
/*	$("#GoogleCheckout").click(function() {
		var reply = prompt("At the moment we are testing our Google Checkout integration.","");
		if(reply=='scubapr0') {
			$("#googleCheckoutForm").submit();
		} else {
			return false;
		}
	});

	// Allow them to claim a free t-shirt
	$("#claimFreeOffer").click(function() {
		$("#freeOfferDialog").dialog({
			 autoOpen: false,
			 bgiframe: true,
			 title: 'Claim Your Free T-Shirt',
			 modal: true,
			 width: 450,
			 height: 270,
			 buttons: {
				"OK": function() {
					if(ValidateFreebieForm()) {
						$("#claimFreebieForm").submit();
						$(this).dialog("close"); $(this).dialog("destroy");
					}
				},
				"Cancel": function() {
					$(this).dialog("close"); $(this).dialog("destroy");
				}
			 }
		});
		$('#freeOfferDialog').dialog("open");
	});

	// Prompt them if they haven't got their free t-shirt
	$("#proceedToCheckoutButtonOffer").click(function() {

		$("#areYouSureDialog").dialog({
			 bgiframe: true,
			 title: 'Free T-Shirt Special Offer',
			 modal: true,
			 width: 450,
			 height: 225,
			 buttons: {
				"Claim Your FREE T-Shirt": function() {
					// Let them claim
					$(this).dialog("close"); $(this).dialog("destroy");
				},
				"Proceed To Checkout": function() {
					// Proceed to checkout
					$("#proceedToCheckoutForm").submit();
					$(this).dialog("close"); $(this).dialog("destroy");
				}
			 } // End Buttons
		}); // End Dialog
		return false;
	}); // End Click*/

});

function ValidateFreebieForm() {
	$("#errorBox").html('');
	var retVal = true;
	$("select[name^='skuAttribute'] option:selected").each(function() {
    	if(isNaN($(this).val())) {
			$("#errorBox").html('Please choose a ' + $(this).text() + '.');
			retVal = false;
		}
    });
	return retVal;
} // End ValidateFreebieForm
