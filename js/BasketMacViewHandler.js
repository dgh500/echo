if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

// Change Catalogue
$("#catalogueSelection a").click(function() {
	if($(this).attr("id") == 'misc') {
		var co_ords = new Array(100,50);
		/* Put the customer 'lookup' form in here as a dialog, which then copies down to the form on click */
		$("#miscProduct").dialog({
			 autoOpen: false,
			 bgiframe: true,
			 title: 'Misc Product Entry',
			 modal: true,
			 position: co_ords,
			 width: 600,
			 height: 200,
			 buttons: {
				"OK": function() {
					if(!isNaN($("#miscProductPrice").val())) {
						// This creates a product with the right display_name and actual_price, and adds it to the basket
						$.ajax({
							type: "POST",
							url: BASE_DIRECTORY + "/ajaxHandlers/CreateMiscProductAjaxHandler.php",
							data: ({display_name : $("#miscProductName").val(), actual_price: $("#miscProductPrice").val()}),
							async: false,
							success: function(data) {
								// Refresh the basket contents view
								$("#basketContents").load(BASE_DIRECTORY + "/view/BasketContentsView.php?LOAD=1&catalogueIdentifier=1");
							}
						});
						// Remove any errors
						$("#miscProductPrice").css({ border: "1px solid #A5ACB2" });
						$("#miscProductError").html("");
						$("#miscProductName").val("");
						$("#miscProductPrice").val("");
						$(this).dialog("close"); $(this).dialog("destroy");
					} else {
						$("#miscProductPrice").css({ border: "2px solid #F00" });
						$("#miscProductError").html("The amount is not a number!");
					}
				},
				"Cancel": function() {
					$(this).dialog("close"); $(this).dialog("destroy");
				}
			 },
		}); // End .dialog()
		$('#miscProduct').dialog("open");
	} else {
		$.ajax({
		   type: "POST",
		   url: BASE_DIRECTORY + "/view/BasketMacFinderView.php?LOADFINDER=1&catalogueIdentifier=" + $(this).attr("id"),
		   async: false,
		   success: function(data) {
				$("#basketFinder").html(data);
			}
	   });
	}
});

// AJAX Loader
$("#loading img").ajaxStart(function(){
	$(this).attr("src",BASE_DIRECTORY + "/wombat7/images/ajaxLoading.gif");
});
// AJAX Loader
$("#loading img").ajaxStop(function(){
	$(this).attr("src",BASE_DIRECTORY + "/wombat7/images/ajaxLoadingComplete.gif");
});

// Open packages
$("#topLevelPackage").click(function() {
	$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
		   {what: "GetPackageCategories", catalogueId: $("#catalogueId").val()},
		   function(data) {
			   	// Load Subcategories
				$("#subLevelCategoryContainer").html(data);

				// Open Packages Sub Level
				$("a[id^='subLevelCategoryItem']").click(function() {
					$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
						   {what: "GetPackages", categoryId: $(this).attr("name") ,catalogueId: $("#catalogueId").val()},
						   function(data) {
							   	// Load Packages
								$("#productLevelCategoryContainer").html(data);
								PackageClick();
							}) // End function(data)
				}); // End Packages Sub-Level
			}) // End function(data)
}); // End Top Level Click

// Click top level category
$(".topLevelCategoryContainer .macViewMenuItem a").click(function() {
	$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
		   {what: "GetProductCategories", catalogueId: $("#catalogueId").val(), categoryIdentifier: $(this).attr("id") },
		   function(data) {
			   	// Load Subcategories
				$("#subLevelCategoryContainer").html(data);

				/********* Subcategory Click ********/
				$(".macViewMenuItem a").click(function() {
					$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
					   {what: "GetProductCategories", catalogueId: $("#catalogueId").val(), categoryIdentifier: $(this).attr("id") },
					   function(data) {
							$("#productLevelCategoryContainer").html(data);
							ProductClick();
					   });
				});
				/********* End Subcategory Click *********/

				ProductClick();
		   }); // End AJAX Fetch Subcategories
});


function ProductClick() {
/********* Product Click *********/
$(".macViewProductMenuItem a").click(function() {
	// Get the product ID
	var productIdentifier = $(this).attr("name");
	// Post the request
	$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
	   {what: "ProductClick", productId: productIdentifier},
		function(data) {
			// Add the data
			$("#productOptionsContent").html(data);
			var productName 		= $("#productName").val();
			var productPrice 		= $("#productPrice").val();
			var productIdentifier	= $("#productId").val();

			// If product has options then load them
			if(parseInt($("#showProductOptions").val()) == 1) {
				// Options Display
				var co_ords = new Array(100,50);
				productPrice = parseFloat(productPrice).toFixed(2);
				// Initialise the dialog
				$('#productOptionsContainer').dialog({
				 autoOpen: false,
				 bgiframe: true,
				 title: productName + ' - &pound;' + productPrice,
				 buttons: { "OK": function() {
					 var numOfAttributes = $("#attributeCount" + productIdentifier).val();
					 var SKUValCode = '';
					 for(var i=0;i<numOfAttributes;i++) {
						SKUValCode = SKUValCode + $("#productAttributes" + productIdentifier + "LOOP" + i + " option:selected").val() + ",";
					 }
					 SKUValCode = SKUValCode.substr(0,(SKUValCode.length-1));

					// Now we have the attribute value ID, we can find the actual SKU it represents
					$.ajax({
						   type: "POST",
						   url: BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
						   data: "what=GetSKU&skuAttrId=" + SKUValCode + "&productIdentifier=" + productIdentifier,
						   async: false,
						   success: function(data) {
										SKUCode = data;
									}
						   });
					// If the user has chosen a non-existant combination of values, then say so - otherwise add it to the basket
					if(SKUCode == 'NO_SKU_EXISTS') {
						// Make a dialog informing the user of what IS available
						$("#allProductSkus").show();
					} else {
						// Post the product into the basket
						$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
							  {what: "AddSku", skuIdentifier: SKUCode},
							  function(data) {
								$("#basketContents").load(BASE_DIRECTORY + "/view/BasketContentsView.php?LOAD=1&catalogueIdentifier=1");
							});
						// Close the dialog
						$(this).dialog("close");
						$(this).dialog("destroy");
					}
				 }, "Cancel": function() { $(this).dialog("close"); $(this).dialog("destroy"); } },
				 modal: true,
				 position: co_ords,
				 width: 600
				 });
				// Open the dialog
				$('#productOptionsContainer').dialog("open");
			} else {
				// Refresh the basket
				$("#basketContents").load(BASE_DIRECTORY + "/view/BasketContentsView.php?LOAD=1&catalogueIdentifier=1");
			}
	});
}); /********* End Product Click *********/
}

function PackageClick() {
// Package Click...
$(".macViewProductMenuItem a").click(function() {
	// Package name, ID, price...
	var packageName  = $(this).text();
	var packageIdentifier = $(this).attr("name");
	var packagePrice = $("#packagePrice" + packageIdentifier).val();

	$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
		   {what: "GetPackageItems", packageId: packageIdentifier},
			function(data) {
				$("#packageOptionsContent").html(data);

				// When you click a package item, show any upgrades
				$("#optionsContainer a").click(function() {
					// Current package price
					var packagePrice = $("#packagePrice" + packageIdentifier).val();
					// Reset the temp to current - work on this one when 'clicking about'
					var tempPackagePrice = packagePrice;
					// Package item ID
					var packageProductIdentifier = $(this).attr("id");
					// Request the upgrades
					$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
							{what: "GetPackageUpgrades", packageId: $(this).attr("name"), packageProductId: packageProductIdentifier},
							function(data) {
								// Show upgrade options
								$("#upgradesContainer").html(data);
								$("#upgradesContainer").show();

								// When you close the package window
								$("#packageCloseLink").click(function() {
									// Hide the upgrades box
									$("#upgradesContainer").hide();

									// Store the current package price after upgrades have been added on
									$("#packagePrice" + packageIdentifier).val(tempPackagePrice);
									// Store the price that has been paid to upgrade this product - this is removed next time the upgrade
									/// windows is opened for this product, to avoid spiralling upgrade cost
									$("#packageProductUpgradePrice" + packageProductIdentifier).val(upgradePrice);
								}); // End click package close icon

								// When you click an upgrade...
								$("#upgradesContainer a.packageUpgradeItem").click(function() {
									if(undefined===window.upgradePrice){
										window.upgradePrice = 0;
									}
									// Overwrite previous values for this package product
									var previousUpgradePrice = $("#packageProductUpgradePrice" + packageProductIdentifier).val();


									// The upgraded price is...
									upgradeId = $(this).attr("id");
									$("#packageProductId" + packageProductIdentifier).val(upgradeId);
									upgradePrice = $("#upgradePrice" + upgradeId).val();
									tempPackagePrice = parseFloat(packagePrice) + parseFloat(upgradePrice) - parseFloat(previousUpgradePrice);
									tempPackagePrice = parseFloat(tempPackagePrice).toFixed(2);

									// Reset the dialog title to include the correct price
									$('#packageOptionsContainer').dialog('option', 'title', packageName + ' - &pound;' + tempPackagePrice);

									// Change the display of whats in the package
									$("#" + $(this).attr("name")).html($(this).text());
									$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
										   {what: "GetUpgradeOptions", upgradeIdentifier: upgradeId},
										   function(data) {
												$("#productOptionsFor" + packageProductIdentifier).html(data);
											});

									// Replace the old SKU with the new one...
								}); // End click upgrade
					}) // End click option
				}); // End click packageitem

				// Options Display
				var co_ords = new Array(100,50);
				packagePrice = parseFloat(packagePrice).toFixed(2);
				// Initialise the dialog
				$('#packageOptionsContainer').dialog({
					 autoOpen: false,
					 bgiframe: true,
					 title: packageName + ' - &pound;' + packagePrice,
					 buttons: { "OK": function() {
						// The SKU Array
						var skuList = '';

						// Reset the prices
						var packagePrice = undefined;
						var packageId = $("#packageIdentifier").val();
						// For each product, get the correct SKU for it
						$.each(
							$("input[id^='packageProductId']"),
							function(iobjIndex,objValue) {
								var productId = objValue.value;
								var SKUCode;
								// If the product has no attributes then the SKU is in the <input id="productSku$PRODUCTID"> value
								if($("#productSku" + productId).val()) {
									SKUCode = $("#productSku" + productId).val();
								} else {
								/*
									Otherwise the attribute value ID (NOT SKU ID) is in the ID of the
									<option> within the <select id="productAttributes$PRODUCTID"> list
								*/
									var numAttributes = $("#attributeCount" + productId).val();
									var SKUValCode = '';
									for(var i=0;i<numAttributes;i++) {
										SKUValCode = SKUValCode + $("#productAttributes" + productId + "LOOP" + i + " option:selected").val() + ",";
									}
									SKUValCode = SKUValCode.substr(0,(SKUValCode.length-1));
									// Now we have the attribute value ID, we can find the actual SKU it represents
									$.ajax({
										   type: "POST",
										   url: BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
										   data: "what=GetSKU&skuAttrId=" + SKUValCode + "&productIdentifier=" + productId,
										   async: false,
										   success: function(data) {
														SKUCode = data;
													}
										   });
								}
								if(SKUCode == 'NO_SKU_EXISTS') {
									// Make a dialog informing the user of what IS available
									alert(productId);
								} else {
									// Add the SKU Code the SKUs Array
									skuList = skuList + SKUCode + ",";
								}
						}); // End foreach
						skuList = skuList.substr(0,(skuList.length-1));

						// Post the package & its contents to the basket
						$.post(BASE_DIRECTORY + "/ajaxHandlers/BasketMacViewAjaxHandler.php",
								  {what: "AddPackage", packageIdentifier: packageId, skuCSVList: skuList},
								  function(data) {
									$("#basketContents").load(BASE_DIRECTORY + "/view/BasketContentsView.php?LOAD=1&catalogueIdentifier=1");
								});

						// Close the dialog
						$(this).dialog("close");
						$(this).dialog("destroy");
					 }, "Cancel": function() { $(this).dialog("close"); $(this).dialog("destroy"); } },
					 modal: true,
					 position: co_ords,
					 width: 600
					 });
				// Open the dialog
				$('#packageOptionsContainer').dialog("open");
			}); // End function(data)
}); // End Click
}