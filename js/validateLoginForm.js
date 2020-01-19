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

	// Check (basic) email address
	if(!echeck(loginEmail.value)) {
		OutlineElement(loginEmail); AddErrorMessage(error,"Your email address is invalid."); return false;
	}

	// Check the passwords are correct length
	if(loginPassword.value.length < 6) {
		OutlineElement(loginPassword); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}
	return true;
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
	var loginEmail = document.getElementById('loginEmail');	
	var loginPassword = document.getElementById('loginPassword');
	RemoveOutline(loginEmail); 
	RemoveOutline(loginPassword); 
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
