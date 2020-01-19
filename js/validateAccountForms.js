function validateChangePasswordForm(thisForm) {
	var newPassword1 = document.getElementById('newPassword1');	
	var newPassword2 = document.getElementById('newPassword2');	
	var oldPassword = document.getElementById('oldPassword');	
	
	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";
	
	RemoveAllOutlines();
	
	// Check for blanks
	if(newPassword1.value.length==0) 	 { OutlineElement(newPassword1);	AddErrorMessage(error,"New Password 1 cannot be left blank"); return false;	} else { RemoveOutline(newPassword1); }
	if(newPassword2.value.length==0) 	 { OutlineElement(newPassword2);	AddErrorMessage(error,"New Password 2 cannot be left blank"); return false;	} else { RemoveOutline(newPassword2); }
	if(oldPassword.value.length==0) 	 { OutlineElement(oldPassword);	 AddErrorMessage(error,"Old Password cannot be left blank"); return false;	} else { RemoveOutline(oldPassword); }
	
	// Check the passwords have matched
	if(newPassword1.value != newPassword2.value) {
		OutlineElement(newPassword1); OutlineElement(newPassword2); AddErrorMessage(error,"Your password must be the same."); return false;
	}

	// Check the passwords are correct length
	if(newPassword1.value.length < 6) {
		OutlineElement(newPassword1); OutlineElement(newPassword1); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}
	if(newPassword2.value.length < 6) {
		OutlineElement(newPassword2); OutlineElement(newPassword2); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}
	if(oldPassword.value.length < 6) {
		OutlineElement(oldPassword); OutlineElement(oldPassword); AddErrorMessage(error,"Your password must be 6 characters or more."); return false;
	}	
	
	return true;
}

function RemoveAllOutlines() {
	var inputs = document.getElementsByTagName('input');
	for(var i=0;i<inputs.length;i++) {
		if(inputs[i].type=='text') {
			inputs[i].style.border="1px solid #ccc";	
		}
	}
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