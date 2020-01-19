$(document).ready(function() {
	
	$("#brochureRequestForm").submit(function() {
		var name 		= $("#catReqName");
		var address1 	= $("#catReqAddress1");
		var county		= $("#catReqCounty");
		var postcode	= $("#catReqPostcode");	
		
		name.css({border: "1px solid #A5ACB2"});
		address1.css({border: "1px solid #A5ACB2"});	
		county.css({border: "1px solid #A5ACB2"});
		postcode.css({border: "1px solid #A5ACB2"});
		
		// Error
		var error = $("#error");
		error.css({display: "none"});
		error.html("");
	
		// Check for blanks
		if(name.val().length==0) 	{ OutlineElement(name); 	AddErrorMessage(error,"Please enter your name."); 	return false; } else { RemoveOutline(name); }
		if(address1.val().length==0){ OutlineElement(address1); AddErrorMessage(error,"Please enter your address.");return false; } else { RemoveOutline(address1); }	
		if(county.val().length==0) 	{ OutlineElement(county); 	AddErrorMessage(error,"Please enter your county"); 	return false; } else { RemoveOutline(county); }
		if(postcode.val().length==0){ OutlineElement(postcode); AddErrorMessage(error,"Please enter your postcode");return false; } else { RemoveOutline(postcode); }
	
		return true;
	});
	
});

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