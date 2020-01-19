// Validates the new customer registration form

function validateForm(thisForm) {
	var title = document.getElementById('title');
	var firstName = document.getElementById('firstName');
	var lastName = document.getElementById('lastName');
	var email = document.getElementById('email');
	var telNo = document.getElementById('telNo');
	var mobNo = document.getElementById('mobNo');
	var password = document.getElementById('password');
	var passwordCheck = document.getElementById('passwordCheck');
	var acceptTerms = document.getElementById('acceptTerms');

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	RemoveAllOutlines();

	// Check for blanks
	if(title.value.length==0) 		 { OutlineElement(title); 		 AddErrorMessage(error,"Title cannot be left blank"); return false;				} else { RemoveOutline(title); }
	if(firstName.value.length==0) 	 { OutlineElement(firstName);	 AddErrorMessage(error,"First name cannot be left blank"); return false;		} else { RemoveOutline(firstName); }
	if(lastName.value.length==0) 	 { OutlineElement(lastName); 	 AddErrorMessage(error,"Last name cannot be left blank"); return false; 		} else { RemoveOutline(lastName); }
	if(email.value.length==0) 		 { OutlineElement(email); 		 AddErrorMessage(error,"Email cannot be left blank");  return false;			} else { RemoveOutline(email); }
	if(telNo.value.length==0) 		 { OutlineElement(telNo); 		 AddErrorMessage(error,"Telephone number cannot be left blank"); return false;	} else { RemoveOutline(telNo); }
	if(password.value.length==0) 	 { OutlineElement(password); 	 AddErrorMessage(error,"Password cannot be left blank"); return false;			} else { RemoveOutline(password); }
	if(passwordCheck.value.length==0){ OutlineElement(passwordCheck);AddErrorMessage(error,"Password check cannot be left blank"); return false; 	} else { RemoveOutline(passwordCheck); }

	// Check Title values
	if(title.value!='Mr' && title.value!='Mrs' && title.value!='Miss' && title.value!='Ms' && title.value!='Dr' && title.value!='Prof' && title.value!='Rev' && title.value!='Lord') {
		OutlineElement(title); AddErrorMessage(error,"Title is invalid"); return false;
	}

	// Check (basic) email address
	if(!echeck(email.value)) {
		OutlineElement(email); AddErrorMessage(error,"Your email address is invalid."); return false;
	}

	// Check the passwords have matched
	if(password.value != passwordCheck.value) {
		OutlineElement(password); OutlineElement(passwordCheck); AddErrorMessage(error,"Your passwords must be the same."); return false;
	}

	// Check the passwords are correct length
	if(password.value.length < 6) {
		OutlineElement(password); OutlineElement(passwordCheck); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}

	// Check terms have been accepted
	if(acceptTerms.checked==false) {
		OutlineElement(acceptTerms); AddErrorMessage(error,"You must accept the terms and conditions."); return false;
	}
	return true;
}

function validateBilling() {
	var cardHoldersName = document.getElementById('cardHoldersName');
	var cardNumber 		= document.getElementById('cardNumber');
	var cardType 		= document.getElementById('cardType');
	var validFromMonth 	= document.getElementById('validFromMonth');
	var validFromYear 	= document.getElementById('validFromYear');
	var expiryDateMonth = document.getElementById('expiryDateMonth');
	var expiryDateYear 	= document.getElementById('expiryDateYear');
	var issueNumber 	= document.getElementById('issueNumber');
	var cvn 			= document.getElementById('cvn');

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	RemoveBillingOutlines();

	// Check for blanks
	if(cardHoldersName.value.length==0) { OutlineElement(cardHoldersName); 	AddErrorMessage(error,"Card holders name cannot be left blank"); return false;	} else { RemoveOutline(cardHoldersName); }
	if(cardNumber.value.length==0) { OutlineElement(cardNumber); 	AddErrorMessage(error,"Card number cannot be left blank"); return false;	} else { RemoveOutline(cardNumber); }
	if(cvn.value.length==0) { OutlineElement(cvn); 	AddErrorMessage(error,"CVN cannot be left blank"); return false;	} else { RemoveOutline(cvn); }

	// Sanity check on valid from/expiry dates
	if(!IsValidMonth(validFromMonth.value)) { OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from month is invalid");	 return false; } else { RemoveOutline(validFromMonth); }
	if(!IsValidMonth(expiryDateMonth.value)) { OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry month is invalid"); return false;	 } else { RemoveOutline(expiryDateMonth); }
	if(!IsValidYear(validFromYear.value)) { OutlineElement(validFromYear); AddErrorMessage(error,"Valid from year is invalid");	 return false;	 } else { RemoveOutline(validFromYear); }
	if(!IsValidYear(expiryDateYear.value)) { OutlineElement(expiryDateYear); AddErrorMessage(error,"Expiry year is invalid"); return false;	 } else { RemoveOutline(expiryDateYear); }

	if(IsInThePast(expiryDateMonth.value,expiryDateYear.value)) {
		OutlineElement(expiryDateYear); OutlineElement(expiryDateMonth); AddErrorMessage(error,"Expiry date is in the past"); return false; }
	else { RemoveOutline(expiryDateYear); RemoveOutline(expiryDateMonth); }

	if(IsInTheFuture(validFromMonth.value,validFromYear.value)) {
		OutlineElement(validFromYear); OutlineElement(validFromMonth); AddErrorMessage(error,"Valid from date is in the future"); return false;
	} else { RemoveOutline(validFromMonth); RemoveOutline(validFromYear); }

	// Sanity check on card type
	switch(cardType.value) {
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
			AddErrorMessage(error,"Card type isn't valid.");
			return false;
		break;
	}

	// Length checks
	var cardNumberNoSpace 	 = StripSpaces(cardNumber.value);
	var issueNumberNoSpace	 = StripSpaces(issueNumber.value);
	var cvnNoSpace			 = StripSpaces(cvn.value);
	if(cardNumberNoSpace.length>19) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number is too long");   return false;	} else { RemoveOutline(cardNumber); }
	if(issueNumberNoSpace.length>2) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number is too long");  return false;	} else { RemoveOutline(issueNumber); }
	if(cvnNoSpace.length>3) { OutlineElement(cvn); AddErrorMessage(error,"CVN is too long"); 	 return false;	} else { RemoveOutline(cvn); }
	if(cvnNoSpace.length<3) { OutlineElement(cvn); AddErrorMessage(error,"CVN is too short");  return false;		} else { RemoveOutline(cvn); }


	// Value checks
	if(parseInt(issueNumber.value)<1) { OutlineElement(issueNumber); AddErrorMessage(error,"Issue number must be greater than 1");  return false; } else { RemoveOutline(issueNumber); }

	// Check First 4 numbers match card type
	switch(cardType.value) {
		case 'Maestro':
			if(!IsMaestro(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Maestro");  return false;  } else { RemoveOutline(cardNumber); }
		break;
		case 'Mastercard':
			if(!IsMasterCard(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Mastercard");  return false;  } else { RemoveOutline(cardNumber); }
		break;
		case 'Solo':
			if(!IsSolo(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Solo");  return false;  } else { RemoveOutline(cardNumber); }
		break;
		case 'Switch':
			if(!IsSwitch(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Switch");  return false;  } else { RemoveOutline(cardNumber); }
		break;
		case 'Visa':
			if(!IsVisa(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa");  return false; } else { RemoveOutline(cardNumber); }
		break;
		case 'Visa Electron':
		if(!IsVisaElectron(cardNumber.value)) { OutlineElement(cardNumber);  AddErrorMessage(error,"Card number isn't a Visa Electron");  return false;  } else { RemoveOutline(cardNumber); }
		break;
	}

	return true;
}

function AddErrorMessage(element,message) {
	element.innerHTML += message + '<br />';
	element.style.display = "block";
}

function RemoveBillingOutlines() {
	var cardHoldersName = document.getElementById('cardHoldersName');
	var cardNumber = document.getElementById('cardNumber');
	var cardType = document.getElementById('cardType');
	var validFromMonth = document.getElementById('validFromMonth');
	var validFromYear = document.getElementById('validFromYear');
	var expiryDateMonth = document.getElementById('expiryDateMonth');
	var expiryDateYear = document.getElementById('expiryDateYear');
	var issueNumber = document.getElementById('issueNumber');
	var cvn = document.getElementById('cvn');

	RemoveOutline(cardHoldersName);
	RemoveOutline(cardNumber);
	RemoveOutline(cardType);
	RemoveOutline(validFromMonth);
	RemoveOutline(validFromYear);
	RemoveOutline(expiryDateMonth);
	RemoveOutline(expiryDateYear);
	RemoveOutline(issueNumber);
	RemoveOutline(cvn);
}

function validateDelivery() {
	var address1 = document.getElementById('address1');
	var address2 = document.getElementById('address2');
	var address3 = document.getElementById('address3');
	var county = document.getElementById('county');
	var postCode = document.getElementById('postCode');
	var bAddress1 = document.getElementById('bAddress1');
	var bAddress2 = document.getElementById('bAddress2');
	var bAddress3 = document.getElementById('bAddress3');
	var bCounty = document.getElementById('bCounty');
	var bPostCode = document.getElementById('bPostCode');
	var referrer = document.getElementById('referrer');
	var deliveryCountry = document.getElementById('country');

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	RemoveAddressOutlines();

	// Check for incorrect postcode/delivery country details
	if(CheckPostcodeForIncorrectCountry(postCode.value) != 'UK' && parseInt(deliveryCountry.value) == 225) {
		// They have chosen UK Mainland instead of Jersey/Guernsey/Isle Man/Scottish Highlands & Offshore Islands
		var returnValue = CheckPostcodeForIncorrectCountry(postCode.value);
		switch(returnValue) {
			case 'JERSEY':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected a Jersey postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>Jersey</strong>.");
				return false;
			break;
			case 'GUERNSEY':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected a Guernsey postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>Guernsey</strong>.");
				return false;
			break;
			case 'ISLEMAN':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected an Isle Man postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>Isle Man</strong>.");
				return false;
			break;
			case 'SCOT':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected a Scottish Highlands &amp; Islands postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>Scottish Highlands &amp; Islands</strong>.");
				return false;
			break;
			case 'NIRELAND':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected a Northern Ireland postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>Northern Ireland</strong>.");
				return false;
			break;
			case 'BFPO':
				OutlineElement(postCode);
				AddErrorMessage(error,"Our system has detected a BFPO postcode, however you have chosen UK Mainland as your delivery country, please <a href=\"" + baseDir + "/basket\"><strong>click here</strong></a> to return to your basket and change your delivery country to <strong>BFPO</strong>.");
				return false;
			break;
		}
	}

	// Check for blanks
	if(address1.value.length==0) 	 { OutlineElement(address1); 	 AddErrorMessage(error,"Address Line 1 cannot be left blank"); return false;	} else { RemoveOutline(address1); }
	if(postCode.value.length==0) 	 { OutlineElement(postCode);	 AddErrorMessage(error,"Postcode cannot be left blank"); return false;	} else { RemoveOutline(postCode); }
	if(bAddress1.value.length==0) 	 { OutlineElement(bAddress1); 	 AddErrorMessage(error,"Address Line 1 cannot be left blank"); return false;	} else { RemoveOutline(bAddress1); }
	if(bPostCode.value.length==0) 	 { OutlineElement(bPostCode);	 AddErrorMessage(error,"Postcode cannot be left blank"); return false;	} else { RemoveOutline(bPostCode); }

	if(referrer.value == 'NA') {
		OutlineElement(referrer);
		AddErrorMessage(error,"Please tell us where you heard about us");
		return false;
	} else {
		RemoveOutline(address1);
	}

	return true;
} // End validateDelivery

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

function RemoveAddressOutlines() {
	var address1 = document.getElementById('address1');
	var postCode = document.getElementById('postCode');
	var bAddress1 = document.getElementById('bAddress1');
	var bPostCode = document.getElementById('bPostCode');
	RemoveOutline(address1);
	RemoveOutline(postCode);
	RemoveOutline(bAddress1);
	RemoveOutline(bPostCode);
}

function toggleBillingCopy() {
	var address1 = document.getElementById('address1');
	var address2 = document.getElementById('address2');
	var address3 = document.getElementById('address3');
	var county = document.getElementById('county');
	var postCode = document.getElementById('postCode');
	var bAddress1 = document.getElementById('bAddress1');
	var bAddress2 = document.getElementById('bAddress2');
	var bAddress3 = document.getElementById('bAddress3');
	var bCounty = document.getElementById('bCounty');
	var bPostCode = document.getElementById('bPostCode');

	if(bAddress1.value != address1.value) {
		bAddress1.value = address1.value;
		bAddress2.value = address2.value;
		bAddress3.value = address3.value;
		bCounty.value = county.value;
		bPostCode.value = postCode.value;
	} else {
		bAddress1.value = '';
		bAddress2.value = '';
		bAddress3.value = '';
		bCounty.value = '';
		bPostCode.value = '';
	}
}

function validateLoginForm() {
	var loginEmail = document.getElementById('loginEmail');
	var loginPassword = document.getElementById('loginPassword');

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	RemoveLoginOutlines();

	// Check for blanks
	if(loginEmail.value.length==0)	 { OutlineElement(loginEmail); 	 AddErrorMessage(error,"Email cannot be left blank"); return false;	} else { RemoveOutline(loginEmail); }
	if(loginPassword.value.length==0)	 { OutlineElement(loginPassword); 	 AddErrorMessage(error,"Password cannot be left blank"); return false;	} else { RemoveOutline(loginPassword); }

	if(-1==loginEmail.value.indexOf('@')) {
		OutlineElement(loginEmail); AddErrorMessage(error,"Your email address is invalid."); return false;
	}

	// Check the passwords are correct length
	if(loginPassword.value.length < 6) {
		OutlineElement(loginPassword); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}
}

function RemoveLoginOutlines() {
	var loginEmail = document.getElementById('loginEmail');
	var loginPassword = document.getElementById('loginPassword');
	RemoveOutline(loginEmail);
	RemoveOutline(loginPassword);
}

function RemoveAllOutlines() {
	var title = document.getElementById('title');
	var firstName = document.getElementById('firstName');
	var lastName = document.getElementById('lastName');
	var email = document.getElementById('email');
	var telNo = document.getElementById('telNo');
	var mobNo = document.getElementById('mobNo');
	var password = document.getElementById('password');
	var passwordCheck = document.getElementById('passwordCheck');
	RemoveOutline(title);
	RemoveOutline(firstName);
	RemoveOutline(lastName);
	RemoveOutline(email);
	RemoveOutline(telNo);
	RemoveOutline(mobNo);
	RemoveOutline(password);
	RemoveOutline(passwordCheck);
}

function OutlineElement(element) {
	element.style.border="2px solid #F00";
}

function RemoveOutline(element) {
	element.style.border="1px solid #A5ACB2";
}

function echeck(str) {
	var at="@"
	var dot="."
	var lat=str.indexOf(at)
	var lstr=str.length
	var ldot=str.indexOf(dot)
	if (str.indexOf(at)==-1){
	   return false
	}
	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
	   return false
	}
	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
	    return false
	}
	if (str.indexOf(at,(lat+1))!=-1){
		return false
	}
	if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false
	}
	if (str.indexOf(dot,(lat+2))==-1){
	    return false
	}
	if (str.indexOf(" ")!=-1){
		return false
	}
	return true
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
	if(parseInt(year)>1990 && parseInt(year)<2050 || year == 'NA') {
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
		case 'NA':
			return true;
		break;
		default:
			return false;
		break;
	}
}
