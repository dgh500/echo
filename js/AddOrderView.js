$(document).ready(function() {
	
	// Billing the same as delivery address
	$("#same").click(function() {
		$("#billing1").val($("#delivery1").val());
		$("#billingPostcode").val($("#deliveryPostcode").val());
	}); // End copy Billing

	// Card Holder same as delivery
	$("#customerName").blur(function() {
		$("#cardHoldersName").val($("#customerName").val());
	}); // End copy Billing
	
	// Update VAT-Free on change of country
	$("#countryDropDownMenu").change(function() {		
		if(!InEurope($("#countryDropDownMenu :selected").text())) {
			var vatCheck = confirm("Make order VAT-Free?")
			if (vatCheck) {
				MakeVatFree();
			}
		} else {
			var vatCheck = confirm("Make order VAT-Inclusive?")
			if (vatCheck) {
				UnMakeVatFree();
			}
		}
	}); // End VAT Free update
	
	// Validation
	$("#newCustomerOrderForm").submit(function() {
		// Address
		var name 			= $("#customerName");
		var delivery1 		= $("#delivery1");
		var delivery2 		= $("#delivery2");
		var delivery3 		= $("#delivery3");
		var city	 		= $("#city");
		var county	 		= $("#county");
		var deliveryPostcode= $("#deliveryPostcode");
		var billing1		= $("#billing1");
		var billingPostcode = $("#billingPostcode");
		
		// Payment
		var cardType				= $("#cardType");
		var cardNumber				= $("#cardNumber");	
		var validFromMonth			= $("#validFromMonth");
		var validFromYear			= $("#validFromYear");	
		var expiryDateMonth			= $("#expiryDateMonth");
		var expiryDateYear			= $("#expiryDateYear");
		var cardVerificationNumber	= $("#cardVerificationNumber");
		var issueNumber				= $("#issueNumber");
		
		// Contact
		var telephoneNumber	= $("#telephoneNumber");
		var emailAddress	= $("#emailAddress");
		
		// Referrer
		var referrer		= $("#referrer");
		
		// Error
		var error = $("#error");
		error.css({display: "none"});		
		error.html("");
		
		// Check for blanks
		if(name.val().length==0) 			{ OutlineElement(name); 			AddErrorMessage(error,"Name cannot be left blank"); 			} else { RemoveOutline(name); }
		if(delivery1.val().length==0)		{ OutlineElement(delivery1); 		AddErrorMessage(error,"Delivery line 1 cannot be left blank"); 	} else { RemoveOutline(delivery1); }
		if(deliveryPostcode.val().length==0){ OutlineElement(deliveryPostcode); AddErrorMessage(error,"Delivery postcode cannot be left blank");} else { RemoveOutline(deliveryPostcode); }
		if(billing1.val().length==0) 		{ OutlineElement(billing1); 		AddErrorMessage(error,"Billing line 1 cannot be left blank"); 	} else { RemoveOutline(billing1); }
		if(billingPostcode.val().length==0) { OutlineElement(billingPostcode); 	AddErrorMessage(error,"Billing postcode cannot be left blank"); } else { RemoveOutline(billingPostcode); }
		if(telephoneNumber.val().length==0) { OutlineElement(telephoneNumber); 	AddErrorMessage(error,"Telephone number cannot be left blank"); } else { RemoveOutline(telephoneNumber); }
	
		// Sanity check on valid from/expiry dates
	if(!IsValidMonth($("#validFromMonth :selected").val())) { OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from month is invalid"); } else { RemoveOutline(validFromMonth); }
	if(!IsValidMonth($("#expiryDateMonth :selected").val())) { OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry month is invalid"); } else { RemoveOutline(expiryDateMonth); }
	if(!IsValidYear($("#validFromYear :selected").val())) { OutlineElement(validFromYear); AddErrorMessage(error,"Valid from year is invalid");		 } else { RemoveOutline(validFromYear); }
	if(!IsValidYear($("#expiryDateYear :selected").val())) { OutlineElement(expiryDateYear); AddErrorMessage(error,"Expiry year is invalid"); 		 } else { RemoveOutline(expiryDateYear); }

		if(IsInThePast($("#expiryDateMonth :selected").val(),$("#expiryDateYear :selected").val())) { 
			OutlineElement(expiryDateYear); OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry date is in the past");  } 
		else { RemoveOutline(expiryDateYear); RemoveOutline(expiryDateMonth); }
		
		if(IsInTheFuture($("#validFromMonth :selected").val(),$("#validFromYear :selected").val())) { 
			OutlineElement(validFromYear); OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from date is in the future");  
		} else { RemoveOutline(validFromMonth); RemoveOutline(validFromYear); }
	
	
		// Sanity check on card type
		switch($("#cardType :selected").val()) {
			case 'Visa':
			case 'Mastercard':
			case 'Maestro':
			case 'Switch':
			case 'Solo':
			case 'Visa Electron':
				RemoveOutline(cardType);
			break;
			default:
				OutlineElement(cardType);
				alert(cardType);
				AddErrorMessage(error,"Card type isn't valid");
			break;
		}
		
		if($("#referrer :selected").val() == 'NA') {
			OutlineElement(referrer); AddErrorMessage(error,"Choose a referrer");	
		} else { RemoveOutline(referrer); }
		
		// Length checks
		var cardNumberNoSpace 	 = StripSpaces(cardNumber.val());
		var issueNumberNoSpace	 = StripSpaces(issueNumber.val());
		var cvnNoSpace			 = StripSpaces(cardVerificationNumber.val());
		
		if(cardNumberNoSpace.length>19) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number is too long");  	} else { RemoveOutline(cardNumber); }
		if(issueNumberNoSpace.length>2) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number is too long"); 	} else { RemoveOutline(issueNumber); }
		if(cvnNoSpace.length>3) { OutlineElement(cardVerificationNumber); AddErrorMessage(error,"CVN is too long"); 		} else { RemoveOutline(cardVerificationNumber); }
		if(cvnNoSpace.length<3) { OutlineElement(cardVerificationNumber); AddErrorMessage(error,"CVN is too short"); 		} else { RemoveOutline(cardVerificationNumber); }

		// Value checks
		if(parseInt(issueNumber.val())<1) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number must be greater than 1"); } else { RemoveOutline(issueNumber); }
		
		// Check First 4 numbers match card type
		switch(cardType.val()) {
			case 'Maestro':
				if(!IsMaestro(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Maestro");  } else { RemoveOutline(cardNumber); }
			break;
			case 'Mastercard':
				if(!IsMasterCard(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Mastercard");  } else { RemoveOutline(cardNumber); }
			break;
			case 'Solo':
				if(!IsSolo(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Solo");  } else { RemoveOutline(cardNumber); }
			break;
			case 'Switch':
				if(!IsSwitch(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Switch");  } else { RemoveOutline(cardNumber); }
			break;
			case 'Visa':
				if(!IsVisa(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa");  } else { RemoveOutline(cardNumber); }
			break;
			case 'Visa Electron':
				if(!IsVisaElectron(cardNumber.val())) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa Electron");  } else { RemoveOutline(cardNumber); }
			break;
		}
		
		// Telephone check
	if(!IsOnlyNumbers(telephoneNumber.val())) { OutlineElement(telephoneNumber);  AddErrorMessage(error,"Telephone number must only contain numbers");  } else { RemoveOutline(telephoneNumber); }
		
		if(error.get(0).style.display == "none") {
			var answer = confirm("Have you checked the postage charge?")
			if (!answer) {
				return false;
			}
		
			var answer2 = confirm("Ask the customer if they have finished shopping - they can NOT add more items after you click OK!")
			if (!answer2) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;	
		}	
	}); // End Validatation
	
}); // End ready() function

//! Makes the order form VAT-Free
function MakeVatFree() {
	
	// Make all inputs with productPrice in them VAT-Free
	$("input[name*=productPrice]").each(function() {
		$(this).val(Math.round(RemoveVAT($(this).val())*100)/100);
	});
	
	// Make all inputs with packagePrice in them VAT-Free
	$("input[name*=packagePrice]").each(function() {
		$(this).val(Math.round(RemoveVAT($(this).val())*100)/100);
	});
	
	// And the total price of course
	$("#orderTotalPrice").val(Math.round(RemoveVAT($("#orderTotalPrice").val())*100)/100);
	
	// Indicate VAT-Free in a hidden field
	$("#vatFreeOrder").val(1);	
	
}

function UnMakeVatFree() {
	
	// Make all inputs with productPrice in them VAT-Inclusive
	$("input[name*=productPrice]").each(function() {
		$(this).val(Math.round(AddVAT($(this).val())*100)/100);
	});
	
	// Make all inputs with packagePrice in them VAT-Inclusive
	$("input[name*=packagePrice]").each(function() {
		$(this).val(Math.round(AddVAT($(this).val())*100)/100);
	});
	
	// And the total price of course
	$("#orderTotalPrice").val(Math.round(AddVAT($("#orderTotalPrice").val())*100)/100);
	
	// Set the vat free indicator to false
	$("#vatFreeOrder").val(0);	
}

function VAT(price_with_vat) {
	var price_without_vat = ( 100 / ( 100 + 15 ) ) * price_with_vat;
	var tax_paid = price_with_vat - price_without_vat;
	return tax_paid;
} // End VAT

function RemoveVAT(price_with_vat) {
	vat = VAT(price_with_vat);
	vatFreePrice = price_with_vat - vat;
	return vatFreePrice;
} // End VAT

function AddVAT(price_without_vat) {
	var vatToBePaid = ( 15 / 100 ) * price_without_vat;
	var vatInclusivePrice = parseFloat(vatToBePaid) + parseFloat(price_without_vat);
	return vatInclusivePrice;
} // End VAT

/*

( [VAT Rate] / 100 ) * [Original Price] = [Amount of VAT Payable]
for example			
( 17.5 / 100 ) * 72.33 = 12.66 (rounded)
to get the final price add £12.66 to £72.33 to get £84.99
*/

function InEurope(country) {
	var europe = new Array("Albania","Andorra","Armenia","Austria","Azerbaijan","Belarus","Belgium","Bosnia & Herzegovina","Bulgaria","Cyprus","Czech Republic","Denmark","Estonia","Finland","France","Georgia","Germany","Greece","Hungary","Iceland","Ireland","Italy","Latvia","Liechtenstein","Lithuania","Luxembourg","Malta","Moldova","Monaca","Netherlands","Norway","Poland","Portugal","Romania","Russia","San Marino","Slovakia","Slovenia","Spain","Sweden","Switzerland","Ukraine","United Kingdom");
	for(var i=0;i<europe.length;i++) {
		if(-1 != country.indexOf(europe[i])) {
			return true;	
		}
	}
}

function IsOnlyNumbers(str) {
	str = str.split(' ').join('');
	str = str.split('(').join('');
	str = str.split(')').join('');
	var ValidChars = "0123456789";
	var IsNumber=true;
	var Char;
	for(i=0;i<str.length&&IsNumber==true;i++) { 
		Char = str.charAt(i); 
		if(ValidChars.indexOf(Char)==-1) {
        	IsNumber = false;
		}
	}
	return IsNumber;
}

function IsSolo(cardNumber) {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16 && cardNumber.length!=18 && cardNumber.length!=19) { return false; }	
	var firstFour = cardNumber.substr(0,4);
	switch(firstFour) {
		case '6334':
		case '6767':
			return true;
		break;
		default:
			return false;
		break;
	}
}

function IsSwitch(cardNumber) {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16 && cardNumber.length!=18 && cardNumber.length!=19) { return false; }	
	var firstFour = cardNumber.substr(0,4);
	var firstSix = cardNumber.substr(0,6);
	switch(firstFour) {
		case '4903':
		case '4905':
		case '4911':
		case '4936':
		case '6759':
		case '6333':
			return true;
		break;
		case '5641':
			if(firstSix=='564182') { return true; } else { return false; }
		break;
		case '6331':
			if(firstSix=='633110') { return true; } else { return false; }
		break;
		default:
			return false;
		break;
	}
}

function IsVisa(cardNumber) {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16) { return false; }
	var firstOne = cardNumber.substr(0,1);
	if(firstOne=='4') { return true; } else { return false; }
}

function IsVisaElectron(cardNumber) {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16) { return false; }	
	var firstFour = cardNumber.substr(0,4);
	var firstSix = cardNumber.substr(0,6);
	switch(firstFour) {
		case '4917':
		case '4913':
		case '4508':
		case '4844':
			return true;
		break;
		case '4175':
			if(firstSix=='417500') { return true; } else { return false; }
		break;
		default:
			return false;
		break;
	}
}

function IsMasterCard(cardNumber)  {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16) { return false; }
	var firstTwo = cardNumber.substr(0,2);
	switch(firstTwo) {
		case '51':
		case '52':
		case '53':
		case '54':
		case '55':
			return true;
		break;
		default:
			return false;
		break;
	}
}

function IsMaestro(cardNumber) {
	cardNumber = StripSpaces(cardNumber);
	if(cardNumber.length!=16 && cardNumber.length!=18 && cardNumber.length!=19) { return false; }	
	var firstFour = cardNumber.substr(0,4);
	switch(firstFour) {
		case '5020':
		case '5038':
		case '6304':
		case '6759':
		case '6761':
			return true;
		break;
		default:
			return false;
		break;
	}
}

function StripSpaces(str) {
	return str.split(' ').join('');
}

function IsInThePast(month,year) {
	var currentTime = new Date();
	var calendarMonth = currentTime.getMonth() + 1; // Because getMonth starts from zero
	if((parseInt(month,10)<parseInt(calendarMonth,10) && parseInt(year,10)==parseInt(currentTime.getFullYear(),10)) || (parseInt(year,10)<parseInt(currentTime.getFullYear(),10))) {
		return true;																   
	} else {
		return false;	
	}
}

function IsInTheFuture(month,year) {
	var currentTime = new Date();
	var calendarMonth = currentTime.getMonth() + 1; // Because getMonth starts from zero
	if(parseInt(month,10)>parseInt(calendarMonth,10) && parseInt(year,10)==parseInt(currentTime.getFullYear(),10) || (parseInt(year,10)>parseInt(currentTime.getFullYear(),10))) {
		return true;																   
	} else {
		return false;	
	}
}

function IsValidYear(year) {
	if(parseInt(year,10)>1990 && parseInt(year,10)<2050) {
		return true;	
	} else {
		return false;	
	}
}

function IsValidMonth(month) {
	switch(month) {
		case '01':
		case '02':
		case '03':
		case '04':
		case '05':
		case '06':
		case '07':
		case '08':
		case '09':
		case '10':
		case '11':
		case '12':
			return true;
		break;
		default:
			return false;
		break;
	}
}

function OutlineElement(element) {
	element.css({border: "2px solid #F00"});	
}

function RemoveOutline(element) {
	element.css({border: "1px solid #A5ACB2"});	
}

function AddErrorMessage(element,message) {
	element.html(element.html() + message + '<br />');
	element.css({display: "block"});
}