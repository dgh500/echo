function validateForm(thisform)
{
	var displayName = document.getElementById('displayName');
	var description = document.getElementById('description');
	var wasPrice	= document.getElementById('wasPrice');
	var upgradePrice= document.getElementById('upgradePrice');	
	var actualPrice = document.getElementById('actualPrice');
	var postage 	= document.getElementById('postage');
	var weight 		= document.getElementById('weight');
	
	displayName.style.border="";

	if(displayName.value==null||displayName.value=="") { 
		document.getElementById("displayName").style.border="solid 2px #FF0000";
		document.getElementById("errorBox").style.border="solid 2px #FF0000";
		document.getElementById("errorBox").innerHTML="Error: The display name cannot be left blank.";
		return false;
	}
	if(wasPrice.value==null||wasPrice.value=="") { wasPrice.value=0; }
	if(actualPrice.value==null||actualPrice.value=="") { actualPrice.value=0; }	
	if(upgradePrice.value==null||upgradePrice.value=="") { upgradePrice.value=0; }
	if(postage.value==null||postage.value=="") { postage.value=0; }
	if(weight.value==null||weight.value=="") { weight.value=0; }
	
	return true;
}

function checkDelete() {
	var answer = confirm("Are you sure you want to delete this?")
	if (!answer) {
		return false;
	}

	var answer2 = confirm("Really sure?")
	if (!answer2) {
		return false;
	}
}