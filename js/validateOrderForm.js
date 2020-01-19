// Validates the order form

function validateForm(thisForm) {
	
	// Address
	var name 			= document.getElementById('customerName');
	var delivery1 		= document.getElementById('delivery1');
	var delivery2 		= document.getElementById('delivery2');
	var delivery3 		= document.getElementById('delivery3');
	var city	 		= document.getElementById('city');
	var county	 		= document.getElementById('county');
	var deliveryPostcode= document.getElementById('deliveryPostcode');
	var billing1		= document.getElementById('billing1');
	var billingPostcode = document.getElementById('billingPostcode');
	
	// Payment
	var cardType				= document.getElementById('cardType');
	var cardNumber				= document.getElementById('cardNumber');	
	var validFromMonth			= document.getElementById('validFromMonth');
	var validFromYear			= document.getElementById('validFromYear');	
	var expiryDateMonth			= document.getElementById('expiryDateMonth');
	var expiryDateYear			= document.getElementById('expiryDateYear');
	var cardVerificationNumber	= document.getElementById('cardVerificationNumber');
	var issueNumber				= document.getElementById('issueNumber');
	
	// Contact
	var telephoneNumber	= document.getElementById('telephoneNumber');
	var emailAddress	= document.getElementById('emailAddress');
	
	// Referrer
	var referrer		= document.getElementById('referrer');
	
	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";
	
	// Check for blanks
	if(name.value.length==0) 			{ OutlineElement(name); 			AddErrorMessage(error,"Name cannot be left blank"); 			} else { RemoveOutline(name); }
	if(delivery1.value.length==0)		{ OutlineElement(delivery1); 		AddErrorMessage(error,"Delivery line 1 cannot be left blank"); 	} else { RemoveOutline(delivery1); }
	if(deliveryPostcode.value.length==0){ OutlineElement(deliveryPostcode); AddErrorMessage(error,"Delivery postcode cannot be left blank");} else { RemoveOutline(deliveryPostcode); }
	if(billing1.value.length==0) 		{ OutlineElement(billing1); 		AddErrorMessage(error,"Billing line 1 cannot be left blank"); 	} else { RemoveOutline(billing1); }
	if(billingPostcode.value.length==0) { OutlineElement(billingPostcode); 	AddErrorMessage(error,"Billing postcode cannot be left blank"); } else { RemoveOutline(billingPostcode); }
	if(telephoneNumber.value.length==0) { OutlineElement(telephoneNumber); 	AddErrorMessage(error,"Telephone number cannot be left blank"); } else { RemoveOutline(telephoneNumber); }

	// Sanity check on valid from/expiry dates
	if(!IsValidMonth(validFromMonth[validFromMonth.selectedIndex].value)) { OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from month is invalid");	 } else { RemoveOutline(validFromMonth); }
	if(!IsValidMonth(expiryDateMonth[expiryDateMonth.selectedIndex].value)) { OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry month is invalid");	 } else { RemoveOutline(expiryDateMonth); }
	if(!IsValidYear(validFromYear[validFromYear.selectedIndex].value)) { OutlineElement(validFromYear); AddErrorMessage(error,"Valid from year is invalid");		 } else { RemoveOutline(validFromYear); }
	if(!IsValidYear(expiryDateYear[expiryDateYear.selectedIndex].value)) { OutlineElement(expiryDateYear); AddErrorMessage(error,"Expiry year is invalid"); 		 } else { RemoveOutline(expiryDateYear); }
	
	if(IsInThePast(expiryDateMonth[expiryDateMonth.selectedIndex].value,expiryDateYear[expiryDateYear.selectedIndex].value)) { 
		OutlineElement(expiryDateYear); OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry date is in the past");  } 
	else { RemoveOutline(expiryDateYear); RemoveOutline(expiryDateMonth); }
	
	if(IsInTheFuture(validFromMonth[validFromMonth.selectedIndex].value,validFromYear[validFromYear.selectedIndex].value)) { 
		OutlineElement(validFromYear); OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from date is in the future");  
	} else { RemoveOutline(validFromMonth); RemoveOutline(validFromYear); }


	// Sanity check on card type
	switch(cardType[cardType.selectedIndex].value) {
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
	
	if(referrer[referrer.selectedIndex].value == 'NA') {
		OutlineElement(referrer); AddErrorMessage(error,"Choose a referrer");	
	} else { RemoveOutline(referrer); }
	
	// Length checks
	var cardNumberNoSpace 	 = StripSpaces(cardNumber.value);
	var issueNumberNoSpace	 = StripSpaces(issueNumber.value);
	var cvnNoSpace			 = StripSpaces(cardVerificationNumber.value);
	if(cardNumberNoSpace.length>19) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number is too long");  	} else { RemoveOutline(cardNumber); }
	if(issueNumberNoSpace.length>2) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number is too long"); 	} else { RemoveOutline(issueNumber); }
	if(cvnNoSpace.length>3) { OutlineElement(cardVerificationNumber); AddErrorMessage(error,"CVN is too long"); 		} else { RemoveOutline(cardVerificationNumber); }
	if(cvnNoSpace.length<3) { OutlineElement(cardVerificationNumber); AddErrorMessage(error,"CVN is too short"); 		} else { RemoveOutline(cardVerificationNumber); }

	// Value checks
	if(parseInt(issueNumber.value)<1) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number must be greater than 1"); } else { RemoveOutline(issueNumber); }
	
	// Check First 4 numbers match card type
	switch(cardType.value) {
		case 'Maestro':
			if(!IsMaestro(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Maestro");  } else { RemoveOutline(cardNumber); }
		break;
		case 'Mastercard':
			if(!IsMasterCard(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Mastercard");  } else { RemoveOutline(cardNumber); }
		break;
		case 'Solo':
			if(!IsSolo(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Solo");  } else { RemoveOutline(cardNumber); }
		break;
		case 'Switch':
			if(!IsSwitch(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Switch");  } else { RemoveOutline(cardNumber); }
		break;
		case 'Visa':
			if(!IsVisa(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa");  } else { RemoveOutline(cardNumber); }
		break;
		case 'Visa Electron':
			if(!IsVisaElectron(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa Electron");  } else { RemoveOutline(cardNumber); }
		break;
	}
	
	// Telephone check
if(!IsOnlyNumbers(telephoneNumber.value)) { OutlineElement(telephoneNumber);  AddErrorMessage(error,"Telephone number must only contain numbers");  } else { RemoveOutline(telephoneNumber); }
	
	if(error.style.display == "none") {
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
	element.style.border="2px solid #F00";	
}

function RemoveOutline(element) {
	element.style.border="1px solid #A5ACB2";	
}

function AddErrorMessage(element,message) {
	element.innerHTML += message + '<br />';
	element.style.display = "block";
}