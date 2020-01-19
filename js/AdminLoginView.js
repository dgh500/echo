$(document).ready(function() {
	
	$("#adminLoginForm").submit(function() {
		var userName = $("#loginName").val();
		var password = $("#loginPassword").val();
		
		$("#loginName").css({border: "1px solid #A5ACB2"});
		$("#loginPassword").css({border: "1px solid #A5ACB2"});
		
		if(userName==null||trim(userName)=="") { 
			$("#loginName").css({border: "solid 2px #FF0000"});
			$("#errorBox").show();
			$("#errorBox").html("<strong>Error:</strong> The username cannot be left blank.");
			return false;
		}
		if(password==null||trim(password)=="") { 
			$("#loginPassword").css({border: "solid 2px #FF0000"});
			$("#errorBox").show();
			$("#errorBox").html("<strong>Error:</strong> The password cannot be left blank.");
			return false;
		}		
		return true;		
	}); // End validation
	
});

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}


		
