function validateForgottenPasswordForm() {
	var email 				= document.getElementById('email');
	var emailConfirmation 	= document.getElementById('emailConfirmation');

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	// Check for blanks
	if(email.value.length==0) { OutlineElement(email); AddErrorMessage(error,"Email cannot be left blank"); return false; } else { RemoveOutline(email); }
	if(emailConfirmation.value.length==0) { OutlineElement(emailConfirmation); AddErrorMessage(error,"Email confirmation cannot be left blank"); return false; } else { RemoveOutline(emailConfirmation); }

	// Check the passwords have matched
	if(email.value != emailConfirmation.value) {
		OutlineElement(email); OutlineElement(emailConfirmation); AddErrorMessage(error,"Your email addresses must be the same."); return false;
	}

	// Check (basic) email address
	if(!echeck(email.value)) {
		OutlineElement(email); AddErrorMessage(error,"Your email address is invalid."); return false;
	}

	return true;
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


function AddErrorMessage(element,message) {
	element.innerHTML += message + '<br />';
	element.style.display = "block";
}

function OutlineElement(element) {
	element.style.border="2px solid #F00";	
}

function RemoveOutline(element) {
	element.style.border="1px solid #A5ACB2";	
}

function RemoveLoginOutlines() {
	var email 				= document.getElementById('email');
	var emailConfirmation 	= document.getElementById('emailConfirmation');
	RemoveOutline(email); 
	RemoveOutline(emailConfirmation); 
}
