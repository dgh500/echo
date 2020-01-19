function validateForm(thisform) {
	var allSelects 	= document.getElementsByTagName('select');
	var errorBox	= document.getElementById('errorBox'); 
	errorBox.innerHTML = '';
	for(var i=0;i<allSelects.length;i++) {
		if('NA' == allSelects[i].value) {
			var optionMissing = allSelects[i].options[allSelects[i].selectedIndex].text;
			errorBox.innerHTML = 'Please choose a ' + optionMissing + '.';
			allSelects[i].style.border="2px solid #f00";
			return false;
		} else {
			allSelects[i].style.border="1px solid #CCC";			
		}
	}
	
	// check for multibuy
	
	
	return true;
}

function switchAdditionalImage(fileName) {
	var mainProductImage = document.getElementById('mainProductImage');
	mainProductImage.src=fileName;
}