$(document).ready(function() {
	
	// Form validation
	$("#addTopLevelCategoryForm").submit(function() {
		var displayName = $("#displayName").val();
		var description = $("#description").val();	
		
		$("#displayName").css({border: "1px solid #ccc"});
		$("#description").css({border: "1px solid #ccc"});
		
		if(displayName==null||trim(displayName)=="") { 
			$("#displayName").css({border: "solid 2px #FF0000"});
			$("#errorBox").css({border: "solid 2px #FF0000"});
			$("#errorBox").html("<strong>Error:</strong> The display name cannot be left blank.");
			return false;
		}	
		return true;
	});
	
});

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
