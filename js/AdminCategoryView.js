$(document).ready(function() {

	// Form validation
	$("#adminCategoryForm").submit(function() {
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
	

	// Delete Checks
	$("#deleteButton").click(function() {
		jConfirm('Are you sure you want to delete this?', 'Delete Category?', function(result) {
			if(result) {
				// You have to add this input as the delete is an input type="button" not type="submit"
				$('#adminCategoryForm').append('<input type="hidden" id="deleteCategoryInput" name="deleteCategoryInput" value="1" />');
    			$("#adminCategoryForm").submit();	
			} else {
	    		jAlert('Not Deleted', 'Delete Category?');				
			}
			return false;
		});
	}); // End delete click

}); // End ready

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}