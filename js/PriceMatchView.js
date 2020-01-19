function validatePriceMatch(thisform)
{
	var productName = document.getElementById('productName');
	var personName 	= document.getElementById('personName');
	var personTel	= document.getElementById('personTel');
	var personEmail	= document.getElementById('personEmail');
	var competitorsPrice = document.getElementById('competitorsPrice');	
	var ourPrice 	= document.getElementById('ourPrice');	
	var whereSeen 	= document.getElementById('whereSeen');		
	
	productName.style.border="";
	personName.style.border="";
	personTel.style.border="";
	personEmail.style.border="";
	competitorsPrice.style.border="";
	ourPrice.style.border="";
	whereSeen.style.border="";

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";

	// Check for blanks
	if(productName.value.length==0) { OutlineElement(productName); AddErrorMessage(error,"Please enter a product."); return false; } else { RemoveOutline(productName); }
	if(personName.value.length==0) { OutlineElement(personName); AddErrorMessage(error,"Please enter your name."); return false; } else { RemoveOutline(personName); }	
	if(personTel.value.length==0) { OutlineElement(personTel); AddErrorMessage(error,"Please enter your telephone number."); return false; } else { RemoveOutline(personTel); }
	if(personEmail.value.length==0) { OutlineElement(personEmail); AddErrorMessage(error,"Please enter your email address."); return false; } else { RemoveOutline(personEmail); }
	if(competitorsPrice.value.length==0) { OutlineElement(competitorsPrice); AddErrorMessage(error,"Please enter our competitors price."); return false; } else { RemoveOutline(competitorsPrice); }
	if(ourPrice.value.length==0) { OutlineElement(ourPrice); AddErrorMessage(error,"Please enter our price."); return false; } else { RemoveOutline(ourPrice); }
	if(whereSeen.value.length==0) { OutlineElement(whereSeen); AddErrorMessage(error,"Please enter where you have seen this."); return false; } else { RemoveOutline(whereSeen); }

	return true;
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