$(document).ready(function() {
	
	// User experience tips
	$("#feedbackText").focus(function() {
		$("#feedbackText").val("");
	});
	
	// Form validation
	$("#feedbackForm").submit(function() {
		var feedbackName = $("#feedbackName").val();
		var feedbackEmail = $("#feedbackEmail").val();	
		var feedbackText = $("#feedbackText").val();	
		
		$("#feedbackName").css({border: "1px solid #ccc"});
		$("#feedbackEmail").css({border: "1px solid #ccc"});
		$("#feedbackText").css({border: "1px solid #ccc"});
		
		if(feedbackName==null||trim(feedbackName)=="") { 
			$("#feedbackName").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> Please enter your name.");
			return false;
		}	

		if(feedbackEmail==null||trim(feedbackEmail)=="") { 
			$("#feedbackEmail").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> Please enter your email.");
			return false;
		}	

		if(feedbackText==null||trim(feedbackText)=="") { 
			$("#feedbackText").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> Please enter your text.");
			return false;
		}

		return true;
	});
	
});

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}