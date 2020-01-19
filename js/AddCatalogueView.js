$(document).ready(function() {

	// When the form is submitted, perform validation
	$("#addCatalogueForm").submit(function() {
		// Get the display name
		var displayName = $("#displayName").val();
		// Reset the border
		$("#displayName").css({border: "1px solid #ccc"});
		// If the display name is null then provide an error message
		if(displayName==null||displayName=="") { 
			$("#displayName").css({border: "solid 2px #ff0000"});
			$("#errorBox").css({border: "solid 2px #ff0000",width: "701px",marginTop: "10px"});
			$("#errorBox").html("<strong>Error:</strong> The display name cannot be left blank.");		
			return false;
		}
		return true;
   }); // End addCatalogueForm submit function
	
	
});