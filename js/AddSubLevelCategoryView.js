$(document).ready(function() {
	
	// Form validation
	$("#addSubLevelCategoryForm").submit(function() {
		var displayName = $("#displayName").val();
		
		$("#displayName").css({border: "1px solid #ccc"});
		
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
