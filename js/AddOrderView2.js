if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

$(document).ready(function() {

	// Complete Order Button Clicked
	$("#completeOrderButton").click(function() {

		// Validate
		if(ValidateOrderForm()) {
			// Hide the order form and show loading
			$("#basketTab").hide();
			$("#billingTab").hide();
			$("#customerTab").hide();
			$("#completeOrderButton").hide();
			$("#processingOrderLoading").show();

				// Depending on which form you're on, submit that one before processing the order
				$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/formHandlers/AddOrderBillingFormHandler.php",
					data: ($("#addOrderBillingForm").serialize()),
					async: false
				});

				$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/formHandlers/AddOrderCustomerFormHandler.php",
					data: ($("#addOrderCustomerForm").serialize()),
					async: false
				});

				$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/formHandlers/AddOrderBasketFormHandler.php",
					data: ('currentPostage=' + $("#currentPostage").val() + '&postageMethodDropDownMenu=' + $("#postageMethodDropDownMenu").val() + '&countryDropDownMenu=' + $("#postageMethodDropDownMenu").val()),
					async: false
				});

				// Process the Order
				$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/formHandlers/AddOrderView2Handler.php",
					data: ({id : $("#completeOrderButton").attr("id")}),
					async: false,
					success: function(data) {
						$("#processingOrderLoading").html(data);
						//$("#processingOrderLoading").hide();
						//alert('Order Taken');
					}
				});
		} // End ValidateOrderForm
	}); // End complete order button clicked

	// Initialise Tabs
//	if($("#basketLoad").val()==1) {
//		$('#addOrderTabContainer').tabs({ selected: 1 });
//	} else {
		$("#addOrderTabContainer").tabs();
//	}

	// Switch to billing tab
	$("#billingTabLink").click(function() {
		$("#referrerTab").val("billingTab");
		$("#basketEmpty").val(1);

		// Submit Basket & Customer Details Form, redirect to billing once its done
		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderBasketFormHandler.php",
			   	$("#addOrderBasketForm").serialize());

		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderCustomerFormHandler.php",
			   	$("#addOrderCustomerForm").serialize(),
			 	function() {
					$('#addOrderTabContainer').tabs('option', 'selected', 2);
				});
	});

	// Switch to basket tab
	$("#basketTabLink").click(function() {
		$("#referrerTab").val("basketTab");

		// Submit Customer Details & Billing Forms, redirect to billing once its done
		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderBillingFormHandler.php",
			   	$("#addOrderBillingForm").serialize());

		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderCustomerFormHandler.php",
			   	$("#addOrderCustomerForm").serialize(),
			 	function() {
					$('#addOrderTabContainer').tabs('option', 'selected', 1);
				});
	});

	// Switch to customer tab
	$("#customerTabLink").click(function() {
		$("#referrerTab").val("customerTab");
		$("#basketEmpty").val(1);

		// Submit billing Details Form, redirect to billing once its done
		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderBasketFormHandler.php",
			   	$("#addOrderBasketForm").serialize());

		$.post(BASE_DIRECTORY + "/formHandlers/AddOrderBillingFormHandler.php",
			   	$("#addOrderBillingForm").serialize(),
			 	function() {
					$('#addOrderTabContainer').tabs('option', 'selected', 0);
				});

	});

	// AJAX Loader
	$("#loading img").ajaxStart(function(){
		$(this).attr("src",BASE_DIRECTORY + "/wombat7/images/ajaxLoading.gif");
	});
	// AJAX Loader
	$("#loading img").ajaxStop(function(){
		$(this).attr("src",BASE_DIRECTORY + "/wombat7/images/ajaxLoadingComplete.gif");
	});

	// Show Complete order button
	$("#completeOrder").show();

});

// Billing address same as delivery
$("#delivery1").blur(function() {
	$("#billing1").val($("#delivery1").val());
}); // End copy Billing
$("#deliveryPostcode").blur(function() {
	$("#billingPostcode").val($("#deliveryPostcode").val());
}); // End copy Billing

// Look up the addresses
$("#addressSearchText").keyup(function() {
	if($("#addressSearchText").val().length > 2) {
		// Look it up!
		$.ajax({
			type: "POST",
			url: BASE_DIRECTORY + "/ajaxHandlers/AddressSearchSuggestAjaxHandler2.php",
			data: ({method : "postcode", sofar: $("#addressSearchText").val()}),
			async: false,
			success: function(data) {
				$("#suggestions").html(data);
				$("#suggestions").show();
			}
		});
	}
});

/*// Make the current input more obvious
$("input").focus(function() {
	$(this).css({ borderRight: "2px solid #A5ACB2", borderTop: "2px solid #A5ACB2", borderBottom: "2px solid #A5ACB2" });
});

$("input").blur(function() {
	$(this).css({ borderRight: "1px solid #A5ACB2", borderTop: "1px solid #A5ACB2", borderBottom: "1px solid #A5ACB2" });
});*/

// Customer Lookup
$("#customerLink").click(function() {
	var co_ords = new Array(100,50);
	/* Put the customer 'lookup' form in here as a dialog, which then copies down to the form on click */
	$("#customerLookupDialog").dialog({
		 autoOpen: false,
		 bgiframe: true,
		 title: 'Customer Lookup',
		 modal: true,
		 position: co_ords,
		 width: 600,
		 height: 400,
		 buttons: {
			"OK": function() {
				// A bit smarter - some old orders have 'John Smith' as the first name - separate using the space.
				var custName = $("#selectedCustomerName").val();
				var exploded = custName.split(' ');
				var custFirstName = exploded[0];
				var custLastName = exploded[1];

				$("#firstName").val(custFirstName);
				$("#lastName").val(custLastName);
				$("#email").val($("#selectedCustomerEmail").val());
				$("#telNo").val($("#selectedCustomerPhone").val());
				$("#delivery1").val($("#line1").val());
				$("#delivery2").val($("#line2").val());
				$("#delivery3").val($("#line3").val());
				$("#county").val($("#selectedCounty").val());
				$("#deliveryPostcode").val($("#selectedPostcode").val());
				$("#billing1").val($("#line1").val());
				$("#billingPostcode").val($("#selectedPostcode").val());

				$(this).dialog("close"); $(this).dialog("destroy");
		 	},
			"Cancel": function() {
				$(this).dialog("close"); $(this).dialog("destroy");
			}
		 },


	});
	$('#customerLookupDialog').dialog("open");
});


function ValidateOrderForm() {
	var requiredFields = new Array('firstName','lastName','telNo','delivery1','delivery2','county','deliveryPostcode','billing1','billingPostcode','referrerId','cardHoldersName','cardNumber','cardVerificationNumber');
	var msg = '';

	// Check the delivery postcode matches the country chosen to send to (Eg. Jersey/UK Mainland is wrong)
	if($("#countryDropDownMenu").val() == 225) {
		switch(CheckPostcodeForIncorrectCountry($("#deliveryPostcode").val())) {
			case 'JERSEY':
				msg = msg + 'You have entered a Jersey delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
			case 'GUERNSEY':
				msg = msg + 'You have entered a Guernsey delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
			case 'ISLEMAN':
				msg = msg + 'You have entered a Isle of Man delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
			case 'SCOT':
				msg = msg + 'You have entered a Scottish Highlands & Islands delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
			case 'NIRELAND':
				msg = msg + 'You have entered a N.Ireland delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
			case 'BFPO':
				msg = msg + 'You have entered a BFPO delivery postcode but chosen UK Mainland as the delivery country.<br>';
			break;
		}
	}

	// Check customer for empty required fields
	$("#addOrderCustomerForm").find("input, select").each(function(i,n) {
		var fieldValue = $(this).val();
		// Is it in the required list?
		for(var i in requiredFields) {
			if($(this).attr("id") == requiredFields[i] && (fieldValue == '' || fieldValue == 'NA')) {
				msg = msg + '<strong>' + $("#" + requiredFields[i] + "Label").html() + '</strong>can\'t be left blank.<br>';
				$(this).css({border: "2px solid #f00"});
			} else if($(this).attr("id") == requiredFields[i]) {
				$(this).css({border: "1px solid #A5ACB2",borderLeft: "2px solid #f00;"});
			}
		}
	});

	// Check billing for empty required fields
	$("#addOrderBillingForm").find("input, select").each(function(i,n) {
		var fieldValue = $(this).val().replace(/^\s+|\s+$/g,"");
		// Is it in the required list?
		for(var i in requiredFields) {
			if($(this).attr("id") == requiredFields[i] && fieldValue == '') {
				msg = msg + '<strong>' + $("#" + requiredFields[i] + "Label").html() + '</strong>can\'t be left blank<br>';
				$(this).css({border: "2px solid #f00"});
			} else if($(this).attr("id") == requiredFields[i]) {
				$(this).css({border: "1px solid #A5ACB2",borderLeft: "2px solid #f00;"});
			}
		}
	});

	// Make sure the billing tab has been visited
	if($("#cardHoldersName").val() == undefined) {
		msg = msg + 'You haven\'t filled in any billing details!<br />';
	}

	// Make sure basket tab has been visited
	/*if($("#basketEmpty").val() == 0) {
		msg = msg + 'You haven\'t put anything in the basket!<br />';
	}*/

	// Check there is something in the basket
	if($("#basketContents").html()=="Your basket is empty.") {
		msg = msg + 'There is nothing in the basket!<br>';
	}

	if(msg != '') {
		jAlert(msg,'Validation Problem');
		return false;
	} else {
		return true;
	}
} // End ValidateOrderForm

function CheckPostcodeForIncorrectCountry(postcodeValue) {
	// Trim whitespace
	postcodeValue = postcodeValue.toUpperCase();

	// Build array for each exception
	var Jersey 		= new Array('JE');
	var Guernsey 	= new Array('GY');
	var IsleOfMan 	= new Array('IM');
	var NIreland 	= new Array('BT');
	var BFPO	 	= new Array('BFPO');

	// Highlands 	- FK17 - FK99, G83, IV1 - IV28, IV33 - IV39, KW, PA21 - PA33, PA35 - PA40, PH18 - PH26, PH30, PH31-PH42
	// Islands 		- HS1-9, IV30-51, IV55-56, KA27, KA28, KW15-17, PA20, PA34, PA41-48, PA60-78, PH42-44, ZE1-3
	var ScottishIslandsAndHighlandsFourLetter = new Array(
		'FK18','FK19','FK20','FK21','FK22','FK23','FK24','FK25','FK26','FK27','FK28','FK29','FK30','FK31','FK32','FK33','FK34','FK35','FK36','FK37','FK38','FK39','FK40','FK41','FK42','FK43','FK44','FK45','FK46','FK47','FK48','FK49','FK50','FK51','FK52','FK53','FK54','FK55','FK56','FK57','FK58','FK59','FK60','FK61','FK62','FK63','FK64','FK65','FK66','FK67','FK68','FK69','FK70','FK71','FK72','FK73','FK74','FK75','FK76','FK77','FK78','FK79','FK80','FK81','FK82','FK83','FK84','FK85','FK86','FK87','FK88','FK89','FK90','FK91','FK92','FK93','FK94','FK95','FK96','FK97','FK98','FK99',
		'IV1','IV2','IV3','IV4','IV5','IV6','IV7','IV8','IV9','IV10','IV11','IV12','IV13','IV14','IV15','IV16','IV17','IV18','IV19','IV20','IV21','IV22','IV23','IV24','IV25','IV26','IV27','IV28',
		'IV33', 'IV34', 'IV35', 'IV36', 'IV37', 'IV38', 'IV39',
		'PA21', 'PA22', 'PA23', 'PA24', 'PA25', 'PA26', 'PA27', 'PA28', 'PA29', 'PA30', 'PA31', 'PA32', 'PA33',
		'PA35', 'PA36', 'PA37', 'PA38', 'PA39', 'PA40',
		'PH18', 'PH19', 'PH20', 'PH21', 'PH22', 'PH23', 'PH24', 'PH25', 'PH26',
		'PH30', 'PH31', 'PH32', 'PH33', 'PH34', 'PH35', 'PH36', 'PH37', 'PH38', 'PH39', 'PH40', 'PH41', 'PH42',
		'IV30', 'IV31', 'IV32', 'IV33', 'IV34', 'IV35', 'IV36', 'IV37', 'IV38', 'IV39', 'IV40', 'IV41', 'IV42', 'IV43', 'IV44', 'IV45', 'IV46', 'IV47', 'IV48', 'IV49', 'IV50', 'IV51', 'IV55', 'IV56',
		'KA27','KA28',
		'KW15','KW16','KW17',
		'PA20','PA34', 'PA41', 'PA42', 'PA43', 'PA44', 'PA45', 'PA46', 'PA47', 'PA48',
		'PH42', 'PH43', 'PH44', 'PH60', 'PH61', 'PH62', 'PH63', 'PH64', 'PH65', 'PH66', 'PH67', 'PH68', 'PH69', 'PH70', 'PH71', 'PH72', 'PH73', 'PH74', 'PH75', 'PH76', 'PH77', 'PH78'
		);

	var ScottishIslandsAndHighlandsThreeLetter = new Array('G83', 'HS1', 'HS2', 'HS3', 'HS4', 'HS5', 'HS6', 'HS7', 'HS8', 'HS9', 'ZE1', 'ZE2', 'ZE3');
	var ScottishIslandsAndHighlandsTwoLetter = new Array('KW');

	//*** Jersey ***//
	jerseyTest = postcodeValue[0] + postcodeValue[1];
	for(var i=0; i<Jersey.length;i++) {
		if(jerseyTest == Jersey[i]) {
			return 'JERSEY';
		}
	}

	//*** Guernsey ***//
	var guernseyTest = postcodeValue[0] + postcodeValue[1];
	for(var i=0; i<Guernsey.length;i++) {
		if(guernseyTest == Guernsey[i]) {
			return 'GUERNSEY';
		}
	}

	//*** Isle Man ***//
	var iomTest = postcodeValue[0] + postcodeValue[1];
	for(var i=0; i<IsleOfMan.length;i++) {
		if(iomTest == IsleOfMan[i]) {
			return 'ISLEMAN';
		}
	}

	//*** N.Ireland ***//
	var nIreTest = postcodeValue[0] + postcodeValue[1];
	for(var i=0; i<NIreland.length;i++) {
		if(nIreTest == NIreland[i]) {
			return 'NIRELAND';
		}
	}

	//*** BFPO ***//
	var bfpoTest = postcodeValue[0] + postcodeValue[1] + postcodeValue[2] + postcodeValue[3];
	for(var i=0; i<BFPO.length;i++) {
		if(bfpoTest == BFPO[i]) {
			return 'BFPO';
		}
	}

	//*** Scottish Islands & Highlands ***//
	// 4-letter tests
	var scotFourTest = postcodeValue[0] + postcodeValue[1] + postcodeValue[2] + postcodeValue[3];
	for(var i=0; i<ScottishIslandsAndHighlandsFourLetter.length;i++) {
		if(scotFourTest == ScottishIslandsAndHighlandsFourLetter[i]) {
			return 'SCOT';
		}
	}

	// 3-letter tests
	var scotThreeTest = postcodeValue[0] + postcodeValue[1] + postcodeValue[2];
	for(var i=0; i<ScottishIslandsAndHighlandsThreeLetter.length;i++) {
		if(scotThreeTest == ScottishIslandsAndHighlandsThreeLetter[i]) {
			return 'SCOT';
		}
	}

	// 2-letter tests
	var scotTwoTest = postcodeValue[0] + postcodeValue[1];
	for(var i=0; i<ScottishIslandsAndHighlandsTwoLetter.length;i++) {
		if(scotTwoTest == ScottishIslandsAndHighlandsTwoLetter[i]) {
			return 'SCOT';
		}
	}

	// Return UK if not matched any exceptions
	return 'UK';
}


function selectSuggestion(selection,addressId,line1val,line2val,line3val,countyVal,postcodeVal,nameVal,emailVal,phoneVal) {
	var searchBox = document.getElementById('addressSearchText');
	var hiddenId  = document.getElementById('id');
	var line1	  = document.getElementById('line1');
	var line2	  = document.getElementById('line2');
	var line3	  = document.getElementById('line3');
	var county	  = document.getElementById('selectedCounty');
	var deliveryPostcode  = document.getElementById('selectedPostcode');
	var selectedCustomerName = document.getElementById('selectedCustomerName');
	var selectedCustomerEmail = document.getElementById('selectedCustomerEmail');
	var selectedCustomerPhone = document.getElementById('selectedCustomerPhone');
	var SuggestBox = document.getElementById('suggestions');

	searchBox.value = selection;
	hiddenId.value = addressId;
	line1.value = line1val;
	line2.value = line2val;
	line3.value = line3val;
	county.value = countyVal;
	deliveryPostcode.value = postcodeVal;
	selectedCustomerName.value = nameVal;
	selectedCustomerEmail.value = emailVal;
	selectedCustomerPhone.value = phoneVal;

	SuggestBox.innerHTML = '';
	SuggestBox.style.display="none";
}
