function validateTrack(thisform) {
	var trackEmail = document.getElementById('trackEmail');	
	var trackOrderId = document.getElementById('trackOrderId');	

	// Error
	var error = document.getElementById('error');
	error.style.display="none";
	error.innerHTML = "";
	
	RemoveTrackOutlines();
	
	// Check for blanks
	if(trackEmail.value.length==0)	 { OutlineElement(trackEmail); 	 AddErrorMessage(error,"Email cannot be left blank"); return false;	} else { RemoveOutline(trackEmail); }
	if(trackOrderId.value.length==0)	 { OutlineElement(trackOrderId); 	 AddErrorMessage(error,"Order ID cannot be left blank"); return false;	} else { RemoveOutline(trackOrderId); }

	// Check (basic) email address
	if(!echeck(trackEmail.value)) {
		OutlineElement(trackEmail); AddErrorMessage(error,"Your email address is invalid."); return false;
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

function RemoveTrackOutlines() {
	var trackEmail = document.getElementById('trackEmail');	
	var trackOrderId = document.getElementById('trackOrderId');
	RemoveOutline(trackEmail); 
	RemoveOutline(trackOrderId); 
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
