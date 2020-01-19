$(document).ready(function() {
	// VAT Button...
	$("#addVat").click(function() {
		var VatFreePrice = $("#actualPrice").val();
		var VatInclusivePrice = VatFreePrice * 1.175;
		$("#actualPrice").val(VatInclusivePrice.toFixed(2));
	});

	// This makes the description tab focussed on page load
	$("#adminProductViewTabContainer-description").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#descriptionLink").css({backgroundPosition: "100% -150px"});

	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('description','description');
	tabsArray[1] = new Array('pricing','pricing');
	tabsArray[2] = new Array('promotions','promotions');
	tabsArray[3] = new Array('optionsz','options');
	tabsArray[4] = new Array('upgrades','upgrades');
	tabsArray[5] = new Array('crossSell','crossSell');
	tabsArray[6] = new Array('images','images');
	tabsArray[7] = new Array('categories','categories');

	// When clicking a link, switch to the respective tab
	$("#descriptionLink").click(function() {
		showTab(tabsArray,'description','adminProductViewTabContainer');
	 });

	$("#pricingLink").click(function() {
		showTab(tabsArray,'pricing','adminProductViewTabContainer');
	 });

	$("#promotionsLink").click(function() {
		showTab(tabsArray,'promotions','adminProductViewTabContainer');
	 });

	$("#optionszLink").click(function() {
		showTab(tabsArray,'optionsz','adminProductViewTabContainer');
	 });

	$("#upgradesLink").click(function() {
		showTab(tabsArray,'upgrades','adminProductViewTabContainer');
	 });

	$("#crossSellLink").click(function() {
		showTab(tabsArray,'crossSell','adminProductViewTabContainer');
	 });

	$("#imagesLink").click(function() {
		showTab(tabsArray,'images','adminProductViewTabContainer');
	 });

	$("#categoriesLink").click(function() {
		showTab(tabsArray,'categories','adminProductViewTabContainer');
	 });

	// Multibuy Show/Hide
	$("#multibuy").click(function() {
		$("#multibuyTable").toggle();
	});

	// Delete Checks
	$("#deleteProduct").click(function() {
		jConfirm('Are you sure you want to delete this?', 'Delete Product?', function(result) {
			if(result) {
				$('#adminProductForm').append('<input type="hidden" id="deleteProductInput" name="deleteProductInput" value="1" />');
    			$("#adminProductForm").submit();
			} else {
	    		jAlert('Not Deleted', 'Delete Product?');
			}
			return false;
		});
	}); // End delete click

	// Hide 'Add Review' Form on load
	$("#addReviewFormContainer").hide();
	// And show it when asked for
	$("#addReviewLink").click(function() {
		$("#addReviewFormContainer").toggle();
	});

	// Load Review Functionality
	LoadReviewFunctionality();

});

function LoadReviewFunctionality() {
	// Approve review functionality
	$('#pendingReviewsList a').click(function() {
		// Where to display the dialog box
		var co_ords = new Array(50,50);

		// Get review ID
		var pendingReviewId =$(this).attr("id");

		// Display the review
		$.ajax({
			type: "POST",
			url: baseDir + "/ajaxHandlers/AdminReviewAjaxHandler.php",
			data: "ajaxRequest=PENDINGREVIEWDATA&reviewId=" + pendingReviewId,
			success: function(msg) {
				// msg comes comma seperated: NAME,RATING,IP,TEXT
				msgArr = msg.split(',');
				$("#reviewConfirmName").val(msgArr[0]);
				$("#reviewConfirmRating").val(msgArr[1]);
				$("#reviewConfirmIP").val(msgArr[2]);
				$("#reviewConfirmText").val(msgArr[3]);
			}
		});

		// Dialog to approve reviews
		$("#pendingReviewDialog").dialog({
			 autoOpen: false,
			 bgiframe: true,
			 buttons: 	{
							"OK": function() {
								$.post(baseDir + "/ajaxHandlers/AdminReviewAjaxHandler.php",
															   // Data
												   { ajaxRequest: 'APPROVEREVIEW',
												   pendingReviewIdP: pendingReviewId,
												   reviewName: $("#reviewConfirmName").val(),
												   reviewRating: $("#reviewConfirmRating").val(),
												   reviewIP: $("#reviewConfirmIP").val(),
												   reviewText: $("#reviewConfirmText").val()
												   },
															   // Callback
															   function(data) {
																	if(data == 'SUCCESS') {
																		jAlert('REVIEW APPROVED!');
																	} else {
																		jAlert(data);
																	}
															   });
								// Close the dialog
								$(this).dialog("close");
								$(this).dialog("destroy");
							}, // End OK Click
							"Cancel": function() {
								{ $(this).dialog("close"); $(this).dialog("destroy"); }
							}
						},
			height: 300,
			modal: true,
			position: co_ords,
			width: 600,
			title: "Approve Review"
			   });
		$("#pendingReviewDialog").dialog("open");
	}); // End click()
} // End LoadReviewFunctionality

// Product Attribute Toggle Disabled
function productAttributeDisabled(productAttributeId) {
	if($("#PRODUCTATTRIBUTEEDIT" + productAttributeId).css('textDecoration') == 'line-through') {
		return true;
	} else {
		return false;
	}
}

/*function productAttributeDisabled(productAttributeId) {
	var productAttribute = document.getElementById("PRODUCTATTRIBUTEEDIT" + productAttributeId);
	if(productAttribute.style.textDecoration=="line-through") {
		return true;
	} else {
		return false;
	}
	//return productAttribute.disabled;
}*/

function toggleDeleteSkuRow(skuId,prefix) {
	if(prefix===undefined) { prefix=''; }
	var sageCode = document.getElementById(prefix + "SKU" + skuId + "SAGECODE");
	var inputs = document.getElementsByTagName("input");

	if(!sageCode.disabled) {
		for(var i=0;i<inputs.length;i++) {
			if(-1 != inputs[i].id.lastIndexOf(prefix + "SKU" + skuId)) {
				inputs[i].style.textDecoration="line-through";
				inputs[i].style.background="#EBEBE4";
				inputs[i].style.border="1px solid #A5ACB2";
				inputs[i].disabled=true;
			}
		}
		var deleteLink = document.getElementById(prefix + "SKUDELETE" + skuId);
		deleteLink.innerHTML = "Un-Delete";
	} else {
		// Loop over inputs
		for(var i=0;i<inputs.length;i++) {
			// Matches anything in the SKU row of the form
			if(-1 != inputs[i].id.lastIndexOf(prefix + "SKU" + skuId)) {
				// Matches PRODUCTATTRIBUTE fields in the form
				if(-1 != inputs[i].id.lastIndexOf("PRODUCTATTRIBUTE")) {
					// Checks that the product attribute hasnt been disabled, to avoid re-enabling a product attribute that is marked as deleted
					if(!productAttributeDisabled(inputs[i].id.split("PRODUCTATTRIBUTE")[1])) {
						inputs[i].style.textDecoration="none";
						inputs[i].style.background="#FFFFFF";
						inputs[i].style.border="1px solid #A5ACB2";
						inputs[i].disabled=false;
					}
				} else {
					// Sage code and actual price
					inputs[i].style.textDecoration="none";
					inputs[i].style.background="#FFFFFF";
					inputs[i].style.border="1px solid #A5ACB2";
					inputs[i].disabled=false;
				}
			}
		}
		var deleteLink = document.getElementById(prefix + "SKUDELETE" + skuId);
		deleteLink.innerHTML = "Delete";
	}
}

function toggleDeleteMultibuyRow(rowId,prefix) {
	if(prefix===undefined) { prefix=''; }
	var tableRowToDelete = document.getElementById(prefix + 'MultibuyRow' + rowId);
	var quantityToDisable = document.getElementById(prefix + 'QuantityInput' + rowId);
	var unitPriceToDisable = document.getElementById(prefix + 'UnitPriceInput' + rowId);
	var deleteButton = document.getElementById(prefix + 'MultibuyDeleteButton' + rowId);

	if(quantityToDisable.disabled == false) {
		quantityToDisable.disabled = true;
		unitPriceToDisable.disabled = true;
		deleteButton.innerHTML = "Un-Delete";
	} else {
		quantityToDisable.disabled = false;
		unitPriceToDisable.disabled = false;
		deleteButton.innerHTML = "Delete";
	}
}

function addMultibuyRow() {
    // Check to see if the counter has been initialized
    if (undefined===addMultibuyRow.called) {
        // It has not... perform the initilization
        addMultibuyRow.called = 0;
    } else {
		addMultibuyRow.called++;
	}

	var multibuyTable = document.getElementById("multibuyTable");

	var lastRow = document.getElementById("multibuyTableLastRow");
	var newRow = document.createElement("TR");

	newRow.id = "newMultibuyRow" + addMultibuyRow.called;
	newRow.name = "newMultibuyRow" + addMultibuyRow.called;

	var quantityCell = document.createElement("TD");
	var unitPriceCell = document.createElement("TD");
	var deleteCell = document.createElement("TD");

	var newQuantityInput = document.createElement("input");
	var newUnitPriceInput = document.createElement("input");

	newQuantityInput.type = "text";
	newQuantityInput.name = "newQuantityInput" + addMultibuyRow.called;
	newQuantityInput.id = "newQuantityInput" + addMultibuyRow.called;

	newUnitPriceInput.type = "text";
	newUnitPriceInput.name = "newUnitPriceInput" + addMultibuyRow.called;
	newUnitPriceInput.id = "newUnitPriceInput" + addMultibuyRow.called;

	var newDeleteButton = document.createElement("a");

	newDeleteButton.innerHTML = "Delete";
	newDeleteButton.href = "#";
	newDeleteButton.name = "newMultibuyDeleteButton" + addMultibuyRow.called;
	newDeleteButton.id = "newMultibuyDeleteButton" + addMultibuyRow.called;

	(function(count){
	  newDeleteButton.onclick = function() { toggleDeleteMultibuyRow(count,'new'); };
	})(addMultibuyRow.called);

	quantityCell.appendChild(newQuantityInput);
	unitPriceCell.appendChild(newUnitPriceInput);
	deleteCell.appendChild(newDeleteButton);

	newRow.appendChild(quantityCell);
	newRow.appendChild(unitPriceCell);
	newRow.appendChild(deleteCell);

	multibuyTable.insertBefore(newRow,lastRow);

}

// Global variable used by addSkuRow() to keep track of how many times the function has been called (=> what the row should be prefixed by)
function addSkuRow(columns) {
	var attributeColumns = columns-3;
	var attributesTable	= document.getElementById("attributesTable");
	var lastRow = document.getElementById("lastRow");
	var row = document.createElement("TR");

	var productAttributeIds = document.getElementById("productAttributeIds");
	var productAttributeIdsArray = productAttributeIds.value.split(",");

    // Check to see if the counter has been initialized
    if (undefined===addSkuRow.called) {
        // It has not... perform the initilization
        addSkuRow.called = 0;
    } else {
		addSkuRow.called++;
	}

	for(var i=0;i<attributeColumns;i++) {
		var tempTd = document.createElement("TD");
			var tempTdInput = document.createElement("input");
			tempTdInput.id="NEWSKU" + addSkuRow.called + "PRODUCTATTRIBUTE" + productAttributeIdsArray[i];
			tempTdInput.name="NEWSKU" + addSkuRow.called + "PRODUCTATTRIBUTE" + productAttributeIdsArray[i];
			if(productAttributeDisabled(productAttributeIdsArray[i])) {
				tempTdInput.disabled=true;
				tempTdInput.style.background="#EBEBE4";
				tempTdInput.style.border="1px solid #A5ACB2";
			}
		tempTd.appendChild(tempTdInput);
		row.appendChild(tempTd);
	}

	var td4 = document.createElement("TD");
	var actualPrice = document.createElement("input");
	actualPrice.id="NEWSKU" + addSkuRow.called + "PRICE";
	actualPrice.name="NEWSKU" + addSkuRow.called + "PRICE";
	actualPrice.value="0.0";
	td4.appendChild(actualPrice);

	var td5 = document.createElement("TD");
	var sageCode = document.createElement("input");
	sageCode.id="NEWSKU" + addSkuRow.called + "SAGECODE";
	sageCode.name="NEWSKU" + addSkuRow.called + "SAGECODE";
	td5.appendChild(sageCode);

	var td7 = document.createElement("TD");
	var qtyInput = document.createElement("input");
	qtyInput.id="NEWSKU" + addSkuRow.called + "QTY";
	qtyInput.name="NEWSKU" + addSkuRow.called + "QTY";
	qtyInput.style.width="30px";
	qtyInput.maxLength="2";
	td7.appendChild(qtyInput);

	var td6 = document.createElement("TD");
	var deleteLink = document.createElement("a");
	deleteLink.innerHTML="Delete";
	deleteLink.href="#";
	deleteLink.id="NEWSKUDELETE" + addSkuRow.called;
	deleteLink.name="NEWSKUDELETE" + addSkuRow.called;

	(function(count){
	  deleteLink.onclick = function() { toggleDeleteSkuRow(count,'NEW'); };
	})(addSkuRow.called);

	td6.appendChild(deleteLink);

	row.appendChild(td4);
	row.appendChild(td5);
	row.appendChild(td7);
	row.appendChild(td6);

	attributesTable.insertBefore(row,lastRow);
}

function copyDown(productAttributeId) {
	var inputs = document.getElementsByTagName("input");
	var lock=0;
	var topValue;
	for(var i=0;i<inputs.length;i++) {
		if(-1 != inputs[i].id.lastIndexOf("PRODUCTATTRIBUTE" + productAttributeId)) {
			if(lock) {
				inputs[i].value=topValue;
			} else {
				topValue = inputs[i].value;
				lock = 1;
			}
		}
	}
}

function copyDownPrice() {
	var inputs = document.getElementsByTagName("input");
	var lock=0;
	var topValue;
	for(var i=0;i<inputs.length;i++) {
		if(-1 != inputs[i].id.lastIndexOf("PRICE")) {
			if(lock) {
				inputs[i].value=topValue;
			} else {
				topValue = inputs[i].value;
				lock = 1;
			}
		}
	}
}

function copyDownSageCode() {
	var inputs = document.getElementsByTagName("input");
	var lock=0;
	var topValue;
	for(var i=0;i<inputs.length;i++) {
		if(-1 != inputs[i].id.lastIndexOf("SAGECODE")) {
			if(lock) {
				inputs[i].value=topValue;
			} else {
				topValue = inputs[i].value;
				lock = 1;
			}
		}
	}
}

function copyDownQty() {
	var inputs = document.getElementsByTagName("input");
	var lock=0;
	var topValue;
	for(var i=0;i<inputs.length;i++) {
		if(-1 != inputs[i].id.lastIndexOf("QTY")) {
			if(lock) {
				inputs[i].value=topValue;
			} else {
				topValue = inputs[i].value;
				lock = 1;
			}
		}
	}
}

function trim(str) {
	return str.replace(/^\s+|\s+$/g, '');
}