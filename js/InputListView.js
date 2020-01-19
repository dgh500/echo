// Toggles whether the text input with id="prefix.elementId" is editable or not. Keeps track of the changes in hidden form fields so that the 'displayed' fields can be set to 
// disabled but still be submitted to the server.
function toggleTextInputEditable(elementId,prefix,formName,tableHeading) {
	// Attribute is the field that is being edited (a text input)
	var attribute		= document.getElementById(prefix + elementId);
	// This is the link that says either 'edit' or 'update'
	var attributeEdit	= document.getElementById(prefix + elementId + 'Edit');
	
	if (!tableHeading) {
		tableHeading = false;
	}
	
	// If the link is not disabled (IE. the field hasnt been deleted)
	if(!attributeEdit.disabled) {
		// If the field is disabled, then have to make it non-disabled (editable)
		if(attribute.disabled) {
			// If this is the first time it has been called, create a new hidden input
			if(!document.getElementById(prefix + "hidden" + elementId)) {
				var optionsDiv = document.getElementById(formName);
				var newInput = document.createElement("input");
				newInput.type = "hidden";
				newInput.name = prefix + "hidden" + elementId;
				newInput.id = prefix + "hidden" + elementId;			
				newInput.value = attribute.value;
				optionsDiv.appendChild(newInput);			
			}
			// Make editable
			attribute.disabled=false;	
			attribute.style.background="#FFFFFF";
			attribute.style.border="1px solid #A5ACB2";
			attributeEdit.innerHTML="Update";
		} else {			
			// Update the hidden field
			var hiddenInput = document.getElementById(prefix + "hidden" + elementId);
			hiddenInput.value = attribute.value;
			
			// Updated
			attribute.disabled=true;	
			attribute.style.background="#EBEBE4";
			attribute.style.border="1px solid #A5ACB2";
			attributeEdit.innerHTML="Edit";
			
			// Used when there is a table heading that uses the edited value (Eg. Products form)
			if(tableHeading) {
				var productAttributeTableHeading = document.getElementById("tableHeading" + elementId);
				productAttributeTableHeading.innerHTML=attribute.value;	
				// Strike through and disables the VALUES of the deleted attribute
				var inputs = document.getElementsByTagName("input");
			}
		}
	}
}

//! Toggles whether a given field is marked as 'deleted' or not.
// elementId is the unique identifier - Eg. a product/package/category/manufacturer etc. ID
// prefix is used to differentiate several instances of this interface within the same form
function toggleDeleteField(elementId,prefix,formName) {
	// This is the text field
	var attribute		= document.getElementById(prefix + elementId);
	// This is the link that says 'Delete' or 'Undelete'
	var deleteLink		= document.getElementById(prefix + elementId + 'Delete');
	// This is the link that says 'Edit' or 'Update'
	var editLink		= document.getElementById(prefix + elementId + 'Edit');
	
	// If the text is delete, then do the delete action, and change the text to 'Un-Delete' and vice versa
	if(deleteLink.innerHTML=="Delete") {
		var answer = confirm("Are you sure you want to delete " + trim(attribute.value) + "?");	
		if(answer) {		
		
			// Create input for PHP to use
			var newInput = document.createElement("input");
			var optionsDiv = document.getElementById(formName);
			newInput.type="hidden";
			newInput.id="DELETE" + prefix + elementId;
			newInput.name="DELETE" + prefix + elementId;
			newInput.value="DELETE" + prefix + elementId;
			optionsDiv.appendChild(newInput);			
		
			// Delete Action
			attribute.style.textDecoration="line-through";
			editLink.style.textDecoration="line-through";
			attribute.style.background="#EBEBE4";
			attribute.style.border="1px solid #A5ACB2";			
			editLink.disabled=true;
			deleteLink.innerHTML="Un-Delete";
			
			// If this is the product edit page, need to change the VALUES of the SKUs as well
			if("adminProductForm" == formName) {
				adminFormToggleDeleted(true,elementId);
			}
		}
	} else {
		var answer = confirm("Are you sure you want to un-delete " + trim(attribute.value) + "?");	
		if(answer) {		
		
			// Un-Delete the form input for PHP
			var oldInput = document.getElementById("DELETE" + prefix + elementId);
			var optionsDiv = document.getElementById(formName);
			optionsDiv.removeChild(oldInput);

			// Un-Delete Action
			attribute.style.textDecoration="none";
			editLink.style.textDecoration="none";
			attribute.style.border="1px solid #A5ACB2";			
			editLink.disabled=false;
			deleteLink.innerHTML="Delete";		
			
			// If this is the product edit page, need to change the VALUES of the SKUs as well
			if("adminProductForm" == formName) {
				adminFormToggleDeleted(false,elementId);
			}
		}
	}
}

// If the form being edited is the admin form, then this function will loop over all inputs, and strike/unstrike those that will be deleted as a result
// of the product attribute being deleted. This is just for visual effect; deleting the product attribute will delete the values anyway
function adminFormToggleDeleted(deleted,elementId) {
	if(deleted) {
		// Strike through and disables the VALUES of the deleted attribute
		var inputs = document.getElementsByTagName("input");
		for(var i=0;i<inputs.length;i++) {
			if(-1 != inputs[i].id.lastIndexOf("PRODUCTATTRIBUTE" + elementId)) {
				inputs[i].style.textDecoration="line-through";
				inputs[i].style.background="#EBEBE4";
				inputs[i].style.border="1px solid #A5ACB2";
				inputs[i].disabled=true;
			}
		}		
	} else {
		// UNStrike through and un-disables the VALUES of the deleted attribute
		var inputs = document.getElementsByTagName("input");
		for(var i=0;i<inputs.length;i++) {
			if(-1 != inputs[i].id.lastIndexOf("PRODUCTATTRIBUTE" + elementId)) {
				var skuDeleteStr = "SKUDELETE" + inputs[i].id.split("PRODUCTATTRIBUTE")[0].split("SKU")[1];
				var skuDelete = document.getElementById(skuDeleteStr);
				// Don't make already-deleted SKUs editable again!
				// This stops 'new' SKU rows from being un-deleted as they don't have Delete links at the right end.
				if(skuDelete.innerHTML=="Delete") {
					inputs[i].style.textDecoration="none";
					inputs[i].style.background="#FFFFFF";
					inputs[i].style.border="1px solid #A5ACB2";
					inputs[i].disabled=false;
				}
			}
		}				
	}
}

function trim(str) {
	return str.replace(/^\s+|\s+$/g, '');	
}
