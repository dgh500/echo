//! General response handler, deals with the AJAX response from MacFinderAjaxHandler.php; generally the responseHandler.js file will delegate relevant requests here
// The MacView is currently used in both the product admin view - for adding upgrades, related, similar and package contents. It is also used in the order form for
// adding a product to an order.

if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

function MacViewHandler(response) {
	// Identifier to choose what to do
	var what = response.getElementsByTagName('what')[0].firstChild.nodeValue;
	switch (what) {
		case 'topLevel':
			// Loads the sub level categories for the top-level category selected
			// Also loads any products that are (dubiously) in a top level category
			HandleTopLevel(response);
		break;
		case 'subLevel':
			// Loads the products in the sub-category selected
			HandleSubLevel(response);
		break;
		case 'productAdd':
			// Adds a product to the output div
			HandleProductAdd(response);
		break;
		case 'productRemove':
			// Removes a product from the output div
			HandleProductRemove(response);
		break;
		case 'topLevelPackages':
			// Loads sub-level categories of package
			HandleTopLevelPackages(response);
		break;
		case 'packageAdd':
			// Adds a package to the output div
			HandlePackageAdd(response);
		break;
		case 'packageRemove':
			// Removes a package from the output div
			HandlePackageRemove(response);
		break;
	}
}

// Analyses the form and calculates the total price then writes it to the "orderTotalPrice" input
function CalculateTotalPrice(prefix) {
	// Use all inputs because do calculations based on the IDs
	var allInputs 		= document.getElementsByTagName('input');
	var totalInput 		= document.getElementById('orderTotalPrice');
	var totalPostage	= document.getElementById('orderPostageTotal');
	//var totalAdjustment = document.getElementById('orderAdjustmentTotal');
	var tempCount = 0;
	// If two products have postage on them, the HIGHER one counts - they are NOT added up or anything like that!
	var highestPostage = Array();
	for(var i=0;i<allInputs.length;i++) {
		// Add product (base) prices, and multiply them by the quantity
		if(-1!=allInputs[i].id.indexOf(prefix + 'productPrice')) {
			var productIdArr = allInputs[i].id.split(prefix + 'productPrice');
			var productId = productIdArr[1];
			var productQuantity = allInputs[prefix + 'productQuantity' + productId].value;
			tempCount = tempCount + (parseFloat(allInputs[i].value) * parseFloat(productQuantity));
		}
		// Add package (base) prices, and multiply them by the quantity
		if(-1!=allInputs[i].id.indexOf(prefix + 'packagePrice')) {
			var packageIdArr = allInputs[i].id.split(prefix + 'packagePrice');
			var packageId = packageIdArr[1];
			var packageQuantity = allInputs[prefix + 'packageQuantity' + packageId].value;
			tempCount = tempCount + (parseFloat(allInputs[i].value) * parseFloat(packageQuantity));
		}
		// Add each product's postage to the highestPostage array, so the highest one can be chosen
		if(-1!=allInputs[i].id.indexOf(prefix + 'productPostage')) {
			highestPostage.push(parseFloat(allInputs[i].value));
		}
		// Add each package's postage to the highestPostage array, so the highest one can be chosen
		if(-1!=allInputs[i].id.indexOf(prefix + 'packagePostage')) {
			highestPostage.push(parseFloat(allInputs[i].value));
		}
	} // End for

	// The postage is whatever the highest product postage is
	var highestPostageValue = Math.max.apply(null,highestPostage);
	if(totalPostage.value==0) {
		totalPostage.value = highestPostageValue;
	}
	if(highestPostage.length==0) {
		// If no products/packages selected, then set postage to zero
		totalPostage.value = 0;
	}

	// Add the correct postage to the counting total
	tempCount = tempCount + parseFloat(totalPostage.value);

	// Copy the tempCount to the total price field
	totalInput.value=Math.round(tempCount*100)/100;

	//if(parseInt(totalAdjustment.value) != 0) {
	//	totalInput.value = totalAdjustment.value;
	//}
}

// Resets (empties) the sub catgeory and product views (leaving only the top level)
function ResetSubLevels(prefix) {
	var secondLevelContainer 	= document.getElementById(prefix + "subLevelCategoryContainer");
	var productLevelContainer 	= document.getElementById(prefix + "productLevelCategoryContainer");
	// Reset the Div
	secondLevelContainer.innerHTML="";
	productLevelContainer.innerHTML="";
}

// Resets (empties) the product view (leaving only the top and sub level)
function ResetProductLevel(prefix) {
	var productLevelContainer 	= document.getElementById(prefix + "productLevelCategoryContainer");
	// Reset the Div
	productLevelContainer.innerHTML="";
}

/*
XML Structure of subCategoryList:
<subCategoryList>
	<subCategory>
		<subCategoryId>{ID}</subCategoryId>
		<subCategoryName>{NAME}</subCategoryName>
	</subCategory>
<subCategoryList>
The argument supplied is the subCategory node
*/
function AddSubCategory(subCategory,prefix,targetElement,style) {
	var secondLevelContainer = document.getElementById(prefix + "subLevelCategoryContainer");
	var tempDiv	 = document.createElement("div");
	var tempLink = document.createElement("a");

	// Style the div
	tempDiv.className 	= "macViewMenuItem";
	// Make the link
	tempLink.href		= "#";

	for(var i=0;i<subCategory.childNodes.length;i++) {
		var currentNode 	= subCategory.childNodes[i];
		var currentNodeName = currentNode.nodeName;
		switch(currentNodeName) {
			case 'subCategoryId':
				var subCatId = currentNode.firstChild.nodeValue;
				tempDiv.id = "subLevelCategory" + subCatId;
				tempDiv.name = "subLevelCategory" + subCatId;
				tempLink.onclick = function() { MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?subCategory=" + subCatId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style); FocusSubLevel(subCatId,prefix); }
			break;
			case 'subCategoryName':
				var subCatName = currentNode.firstChild.nodeValue;
				tempLink.innerHTML 	= subCatName;
			break;
		}
	}
	// Add link to the div
	tempDiv.appendChild(tempLink);
	// Add the div to the container
	secondLevelContainer.appendChild(tempDiv);
}

/*
XML Structure of packageCategoryList:
<packageCategoryList>
	<packageCategory>
		<categoryId>{ID}</categoryId>
		<categoryName>{NAME}</categoryName>
	</packageCategory>
<packageCategoryList>
The argument supplied is the subCategory node
*/
function AddPackageCategory(packageCategory,prefix,targetElement,style) {
	var secondLevelContainer = document.getElementById(prefix + "subLevelCategoryContainer");
	var tempDiv	 = document.createElement("div");
	var tempLink = document.createElement("a");

	// Style the div
	tempDiv.className 	= "macViewMenuItem";
	// Make the link
	tempLink.href		= "#";

	for(var i=0;i<packageCategory.childNodes.length;i++) {
		var currentNode 	= packageCategory.childNodes[i];
		var currentNodeName = currentNode.nodeName;
		switch(currentNodeName) {
			case 'categoryId':
				var packageCategoryId = currentNode.firstChild.nodeValue;
				tempDiv.id = "subLevelCategory" + packageCategoryId;
				tempDiv.name = "subLevelCategory" + packageCategoryId;
				tempLink.onclick = function() { MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?subCategory=" + packageCategoryId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style); FocusSubLevel(packageCategoryId,prefix); }
			break;
			case 'categoryName':
				var packageCategoryName = currentNode.firstChild.nodeValue;
				tempLink.innerHTML 	= packageCategoryName;
			break;
		}
	}

	// Add link to the div
	tempDiv.appendChild(tempLink);
	// Add the div to the container
	secondLevelContainer.appendChild(tempDiv);
}

/*
XML Structure of productList:
<productList>
	<product>
		<productId>{ID}</productId>
		<productName>{NAME}</productName>
	</product>
<productList>
The argument supplied is the product node
*/
function AddProduct(product,prefix,targetElement,style,level) {
	var container = document.getElementById(prefix + level);
	var tempDiv	 = document.createElement("div");
	var tempLink = document.createElement("a");
	var tempCheck = document.createElement("input");
	// Style the div
	tempDiv.className = "macViewProductMenuItem";
	// Make the checkbox
	tempCheck.type = "checkbox";
	tempCheck.style.marginLeft = "-4px";
	tempCheck.style.marginRight = "4px";
	tempCheck.style.width = "auto";
	// Link Prelims
	tempLink.href		= "#";
	tempLink.style.display = "block";

	for(var i=0;i<product.childNodes.length;i++) {
		var currentNode = product.childNodes[i];
		var currentNodeName = currentNode.nodeName;
		switch(currentNodeName) {
			case 'productId':
				var productId = currentNode.firstChild.nodeValue;;
				tempCheck.id = prefix + "CHECK" + productId;
				tempCheck.name = prefix + "CHECK" + productId;
				tempCheck.onclick	= function() { ToggleVisibleInTargetElement(productId,targetElement,prefix,style); }
				var alreadyAnUpgrade = document.getElementsByName(prefix + productId);
				if(0 != alreadyAnUpgrade.length) {
					tempCheck.defaultChecked	= true;	// IE
					tempCheck.checked			= true;	// FF
				}
				tempLink.onclick	= function() {
					ToggleProductChecked(productId,prefix);
					ToggleVisibleInTargetElement(productId,targetElement,prefix,style);
					}
			break;
			case 'productName':
				var productName = currentNode.firstChild.nodeValue;
				tempLink.innerHTML = productName;
			break;
		}
	}
	// Add link to the div
	tempDiv.appendChild(tempCheck);
	tempDiv.appendChild(tempLink);

	// Add the div to the container
	container.appendChild(tempDiv);
}

/*
XML Structure of packageList:
<packageList>
	<package>
		<packageId>{ID}</packageId>
		<packageName>{NAME}</packageName>
	</package>
<packageList>
The argument supplied is the product node
*/
function AddPackage(package,prefix,targetElement,style,level) {
	var container = document.getElementById(prefix + level);
	var tempDiv	 = document.createElement("div");
	var tempLink = document.createElement("a");
	var tempCheck = document.createElement("input");
	// Style the div
	tempDiv.className = "macViewProductMenuItem";
	// Make the checkbox
	tempCheck.type = "checkbox";
	tempCheck.style.marginLeft = "-4px";
	tempCheck.style.marginRight = "4px";
	tempCheck.style.width = "auto";
	// Link Prelims
	tempLink.href		= "#";
	tempLink.style.display = "block";

	for(var i=0;i<package.childNodes.length;i++) {
		var currentNode = package.childNodes[i];
		var currentNodeName = currentNode.nodeName;
		switch(currentNodeName) {
			case 'packageId':
				var packageId = currentNode.firstChild.nodeValue;;
				tempCheck.id = prefix + "CHECK" + packageId;
				tempCheck.name = prefix + "CHECK" + packageId;
				tempCheck.onclick	= function() { ToggleVisiblePackageInTargetElement(packageId,targetElement,prefix,style); }
				var alreadyVisible = document.getElementsByName(prefix + packageId);
				if(0 != alreadyVisible.length) {
					tempCheck.defaultChecked	= true;	// IE
					tempCheck.checked			= true;	// FF
				}
				tempLink.onclick	= function() { TogglePackageChecked(packageId,prefix); ToggleVisiblePackageInTargetElement(packageId,targetElement,prefix,style); }
			break;
			case 'packageName':
				var packageName = currentNode.firstChild.nodeValue;
				tempLink.innerHTML = packageName;
			break;
		}
	}
	// Add link to the div
	tempDiv.appendChild(tempCheck);
	tempDiv.appendChild(tempLink);

	// Add the div to the container
	container.appendChild(tempDiv);
}

// Load the sub level categories, and any (dubious) mid-level products
function HandleTopLevel(response) {
	var prefix 				= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var style 				= response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var targetElement		= response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var subCategoryList		= response.getElementsByTagName('subCategoryList')[0];
	var productList			= response.getElementsByTagName('productList')[0];

	// Reset the sub-level sections
	ResetSubLevels(prefix);
	// Add each sub category to the div
	for(var i=0;i<subCategoryList.childNodes.length;i++) {
		(function(i) { // Begin closure for variable i
			AddSubCategory(subCategoryList.childNodes[i],prefix,targetElement,style);
		})(i); // End closure for variable i
	} // End for

	// Add each product to the div
	for(var i=0;i<productList.childNodes.length;i++) {
		(function(i) {
			AddProduct(productList.childNodes[i],prefix,targetElement,style,'subLevelCategoryContainer');
		})(i);
	}
}

function HandleTopLevelPackages(response) {
	var prefix 					= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var style 					= response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var targetElement			= response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var packageCategoryList		= response.getElementsByTagName('packageCategoryList')[0];

	var secondLevelContainer 	= document.getElementById(prefix + "subLevelCategoryContainer");
	var productLevelContainer 	= document.getElementById(prefix + "productLevelCategoryContainer");

	// Reset the Div
	secondLevelContainer.innerHTML="";
	productLevelContainer.innerHTML="";

	for(var i=0;i<packageCategoryList.childNodes.length;i++) {
		(function(i) {
			AddPackageCategory(packageCategoryList.childNodes[i],prefix,targetElement,style);
		})(i);
	}
}

// Load either products or packages as required in the third column. Only productList or packageList can exist (nothing belongs to both)
function HandleSubLevel(response) {
	var prefix 	= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var style 	= response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var productList = response.getElementsByTagName('productList')[0];
	var packageList = response.getElementsByTagName('packageList')[0];

	// Reset the Div
	ResetProductLevel(prefix);

	// Add each product to the div
	for(var i=0;i<productList.childNodes.length;i++) {
		(function(i) {
			AddProduct(productList.childNodes[i],prefix,targetElement,style,'productLevelCategoryContainer');
		})(i);
	}

	for(var i=0;i<packageList.childNodes.length;i++) {
		(function(i) {
			AddPackage(packageList.childNodes[i],prefix,targetElement,style,'productLevelCategoryContainer');
		})(i);
	}
}

// Makes a container for AddDuplicatePackageContent() to use
function MakeDuplicateContentContainer(counter,packageId,packageName) {
	var dupeContentDiv   = document.createElement("div");
	var packageNumber 	= document.createElement("div");
	dupeContentDiv.id    = "package" + packageId + "dupeContentDiv" + counter;
	dupeContentDiv.name  = "package" + packageId + "dupeContentDiv" + counter;
	packageNumber.className = "packageNumber";
	packageNumber.innerHTML = packageName + " " + counter;
	dupeContentDiv.appendChild(packageNumber);
	return dupeContentDiv;
}

/*
Rationale of the counter "static" variable: Used to add/remove packages, at the end of this function call it should be one more than the number of
packages on the page, Eg. If there are 6 packages on the page, then .counter should be 7. This means when it is called again, it "starts" on the
next value, and is incremeneted afterwards. It is also used as the ID for the DIV containing the package, so it needs to be right!
Update: Now an array indexed by the package ID

XML Structure of packageContentsList:
<packageContentsList>
	<packageContent>
		<.. product details ..>
	</packageContent>
	<packageContent>
		<.. product details ..>
	</packageContent>
</packageContentsList>
*/
function AddDuplicatePackageContent(packageContentsList,newDiv,quantity,packageId,packageName) {
	if(typeof(AddDuplicatePackageContent.CounterArray) == 'undefined') {
		AddDuplicatePackageContent.CounterArray = new Array();
	}
	if(typeof(AddDuplicatePackageContent.CounterArray[packageId])=='undefined') {
		AddDuplicatePackageContent.CounterArray[packageId] = 2;
	}
	if (typeof AddDuplicatePackageContent.previousQuantity == 'undefined' ) {
		AddDuplicatePackageContent.previousQuantity = new Array();
	}

	// First call, just need to add whatever number of packages
	if (typeof AddDuplicatePackageContent.previousQuantity[packageId] == 'undefined' ) {
		AddDuplicatePackageContent.previousQuantity[packageId] = quantity;
		var difference = parseInt(quantity)-1;
		if(difference>0) {
			for(var j=0;j<difference;j++) {
				var dupeContentDiv = MakeDuplicateContentContainer(AddDuplicatePackageContent.CounterArray[packageId],packageId,packageName);
				AddDuplicatePackageContent.CounterArray[packageId]++;
				for(var i=0;i<packageContentsList.childNodes.length;i++) {
					AddPackageContent(packageContentsList.childNodes[i],dupeContentDiv,(AddDuplicatePackageContent.CounterArray[packageId]-1),packageId);
				}
				newDiv.appendChild(dupeContentDiv);
			}
		}
	} else {
		var difference = Math.abs((parseInt(quantity) - parseInt(AddDuplicatePackageContent.previousQuantity[packageId])));
		if(parseInt(quantity) > parseInt(AddDuplicatePackageContent.previousQuantity[packageId])) {
			// Add some packages
			for(var j=0;j<difference;j++) {
				var dupeContentDiv = MakeDuplicateContentContainer(AddDuplicatePackageContent.CounterArray[packageId],packageId,packageName);
				AddDuplicatePackageContent.CounterArray[packageId]++;
				for(var i=0;i<packageContentsList.childNodes.length;i++) {
					AddPackageContent(packageContentsList.childNodes[i],dupeContentDiv,(AddDuplicatePackageContent.CounterArray[packageId]-1),packageId);
				}
				newDiv.appendChild(dupeContentDiv);
			}
		} else {
			// Subtract some packages
			var count = AddDuplicatePackageContent.previousQuantity[packageId];
			for(var i=0;i<difference;i++) {
				var toDelete = document.getElementById("package" + packageId + "dupeContentDiv" + count);
				newDiv.removeChild(toDelete);
				count--;
			}
		AddDuplicatePackageContent.CounterArray[packageId] = (count+1);
		}
	}
	AddDuplicatePackageContent.previousQuantity[packageId] = quantity;
}


// Makes a container for AddDuplicatePackageContent() to use
function MakeDuplicateProductContainer(counter,productId,productName) {
	var dupeContentDiv   = document.createElement("div");
	dupeContentDiv.id    = "product" + productId + "dupeContentDiv" + counter;
	dupeContentDiv.name  = "product" + productId + "dupeContentDiv" + counter;
	return dupeContentDiv;
}

function AddDuplicateProductAttributesAndUpgrades(dupeProductDiv,productCounter,productId,attributeList,upgradeList) {
	var attributesContainer = document.createElement("div");
	attributesContainer.className="packageContentAttributesContainer";
	for(var i=0;i<attributeList.childNodes.length;i++) {
		var singleAttributeContainer = AddAttributeToProduct(attributeList.childNodes[i],productId,productCounter);
		attributesContainer.appendChild(singleAttributeContainer);
	}
	dupeProductDiv.appendChild(attributesContainer);
	var upgradesContainer = document.createElement("div");
	upgradesContainer.className = "packageContentUpgradesContainer";
	if(upgradeList.length>0) {
		upgradesContainer.innerHTML = "<strong>Upgrades:</strong> ";
	}
	for(var i=0;i<upgradeList.childNodes.length;i++) {
		var singleUpgradeContainer = AddUpgradeToProduct(upgradeList.childNodes[i],productId,productCounter);
		upgradesContainer.appendChild(singleUpgradeContainer);
	}
	dupeProductDiv.appendChild(upgradesContainer);
}

function AddDuplicateProduct(newDiv,quantity,productId,productName,attributesList,upgradesList) {
	if(typeof(AddDuplicateProduct.CounterArray) == 'undefined') {
		AddDuplicateProduct.CounterArray = new Array();
	}
	if(typeof(AddDuplicateProduct.CounterArray[productId])=='undefined') {
		AddDuplicateProduct.CounterArray[productId] = 2;
	}
	if (typeof AddDuplicateProduct.previousQuantity == 'undefined' ) {
		AddDuplicateProduct.previousQuantity = new Array();
	}

	// First call, just need to add whatever number of products
	if (typeof AddDuplicateProduct.previousQuantity[productId] == 'undefined' ) {
		AddDuplicateProduct.previousQuantity[productId] = quantity;
		var difference = parseInt(quantity)-1;
		if(difference>0) {
			for(var j=0;j<difference;j++) {
				var dupeProductDiv = MakeDuplicateProductContainer(AddDuplicateProduct.CounterArray[productId],productId,productName);
				if(attributesList.childNodes.length>0 || upgradesList.childNodes.length>0) {
					dupeProductDiv.className = "attributesAndUpgradesContainerProduct";
				}
				AddDuplicateProduct.CounterArray[productId]++;
				AddDuplicateProductAttributesAndUpgrades(dupeProductDiv,(AddDuplicateProduct.CounterArray[productId]-1),productId,attributesList,upgradesList);
				newDiv.appendChild(dupeProductDiv);
			}
		}
	} else {
		var difference = Math.abs((parseInt(quantity) - parseInt(AddDuplicateProduct.previousQuantity[productId])));
		if(parseInt(quantity) > parseInt(AddDuplicateProduct.previousQuantity[productId])) {
			// Add some packages
			for(var j=0;j<difference;j++) {
				var dupeProductDiv = MakeDuplicateProductContainer(AddDuplicateProduct.CounterArray[productId],productId,productName);
				if(attributesList.childNodes.length>0 || upgradesList.childNodes.length>0) {
					dupeProductDiv.className = "attributesAndUpgradesContainerProduct";
				}
				AddDuplicateProduct.CounterArray[productId]++;
				AddDuplicateProductAttributesAndUpgrades(dupeProductDiv,(AddDuplicateProduct.CounterArray[productId]-1),productId,attributesList,upgradesList);
				newDiv.appendChild(dupeProductDiv);
			}
		} else {
			// Subtract some packages
			var count = AddDuplicateProduct.previousQuantity[productId];
			for(var i=0;i<difference;i++) {
				var toDelete = document.getElementById("product" + productId + "dupeContentDiv" + count);
				newDiv.removeChild(toDelete);
				count--;
			}
		AddDuplicateProduct.CounterArray[productId] = (count+1);
		}
	}
	AddDuplicateProduct.previousQuantity[productId] = quantity;
}

function AddAttributeToPackage(attribute,productId,packageCounter,packageId) {
	var singleAttributeContainer = document.createElement("div");
	singleAttributeContainer.className = "packageContentSingleAttributeContainer";
	var selectList 	= document.createElement("select");
	selectList.className="packageContentDropDown";
	for(var k=0;k<attribute.childNodes.length;k++) {
		var currentNode	= attribute.childNodes[k];
		switch(currentNode.nodeName) {
			case "attributeId":
				attributeId = currentNode.firstChild.nodeValue;
				selectList.id 	= "packageContentAttribute" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + attributeId + "attributeId";
				selectList.name = "packageContentAttribute" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + attributeId + "attributeId";
			break;
			case "attributeName":
				var label 		= document.createElement("label");
				label.innerHTML = currentNode.firstChild.nodeValue + ": ";
				label.className = "packageContentLabel";
			break;
			case "skuAttribute":
				var option 	= document.createElement("option");
				var skuAttributeId = currentNode.firstChild.nextSibling.firstChild.nodeValue;
				option.text = currentNode.firstChild.firstChild.nodeValue;
				option.id 	= "packageContentAttribute" + skuAttributeId;
				option.name = "packageContentAttribute" + skuAttributeId;
				option.value= skuAttributeId;
				try
				{ selectList.add(option,null); } 	// Standards
				catch(ex)			// IE
				{ selectList.add(option); }
			break;
		} // End switch
	} // End for
	singleAttributeContainer.appendChild(label);
	singleAttributeContainer.appendChild(selectList);
	return singleAttributeContainer;
}

/* XML Structure of productUpgrade
<productUpgrade>
	<upgradeId>1455</upgradeId>
	<upgradeName>testvalues</upgradeName>
	<upgradePrice>10.0</upgradePrice>
</productUpgrade>
*/
function AddUpgradeToPackage(productUpgrade,productId,packageCounter,packageId) {
	var label 				= document.createElement("label");
	var upgradePriceInput 	= document.createElement("input");
	var checkbox 			= document.createElement("input");
	var singleUpgradeContainer = document.createElement("div");

	if(typeof(packageId)=="undefined") { packageId = 1; }

	upgradePriceInput.type="hidden"
	label.className="packageContentUpgradeLabel";

	// Loop over upgrade
	for(var k=0;k<productUpgrade.childNodes.length;k++) {
		var currentNode = productUpgrade.childNodes[k];
		switch(currentNode.nodeName) {
			case 'upgradeName':
				upgradeName = currentNode.firstChild.nodeValue;
				label.innerHTML += upgradeName;
			break;
			case 'upgradeId':
				upgradeId = currentNode.firstChild.nodeValue;
				(function(upgradeId,productId) {
					checkbox.type="checkbox";
					checkbox.id 	= "packageContentUgradeCheckbox" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + upgradeId + "upgradeId";
					checkbox.name 	= "packageContentUgradeCheckbox" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + upgradeId + "upgradeId";
					checkbox.onchange = function() { TogglePackageUpgrade(productId,upgradeId,packageId,packageCounter); }
					checkbox.className="packageContentCheckbox";
					upgradePriceInput.id = "packageContentUgradePrice" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + upgradeId + "upgradeId";
					upgradePriceInput.name = "packageContentUgradePrice" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + upgradeId + "upgradeId";
				})(upgradeId,productId);
			break;
			case 'upgradePrice':
				upgradePrice = currentNode.firstChild.nodeValue;
				label.innerHTML += " £" + upgradePrice;
				upgradePriceInput.value=upgradePrice;
			break;
			case 'attribute':
				var attribute = currentNode;
				var singleAttribute = AddAttributeToPackageUpgrade(attribute,upgradeId,packageCounter,packageId);
				singleUpgradeContainer.appendChild(singleAttribute);		// No need to check this exists - wouldnt get here if it didnt
			break;
		} // End switch
	} // End for
	singleUpgradeContainer.appendChild(checkbox);
	singleUpgradeContainer.appendChild(label);
	singleUpgradeContainer.appendChild(upgradePriceInput);
	return singleUpgradeContainer;
}

function AddAttributeToPackageUpgrade(attribute,productId,packageCounter,packageId) {
	var singleAttributeContainer = document.createElement("div");
	var selectList 	= document.createElement("select");
	for(var k=0;k<attribute.childNodes.length;k++) {
		var currentNode	= attribute.childNodes[k];
		switch(currentNode.nodeName) {
			case "attributeId":
				attributeId = currentNode.firstChild.nodeValue;
				selectList.id 	= "packageContentAttribute" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + attributeId + "attributeId";
				selectList.name = "packageContentAttribute" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + attributeId + "attributeId";
			break;
			case "attributeName":
				var label 		= document.createElement("label");
				label.innerHTML = currentNode.firstChild.nodeValue + ": ";
				label.className = "packageContentLabel";
			break;
			case "skuAttribute":
				var option 	= document.createElement("option");
				var skuAttributeId = currentNode.firstChild.nextSibling.firstChild.nodeValue;
				option.text = currentNode.firstChild.firstChild.nodeValue;
				option.id 	= "packageContentAttribute" + skuAttributeId;
				option.name = "packageContentAttribute" + skuAttributeId;
				option.value= skuAttributeId;
				try
				{ selectList.add(option,null); } 	// Standards
				catch(ex)			// IE
				{ selectList.add(option); }
			break;
		} // End switch
	} // End for
	singleAttributeContainer.appendChild(label);
	singleAttributeContainer.appendChild(selectList);
	return singleAttributeContainer;
}

function OpenAttributeContainer(productId) {
	if (typeof OpenAttributeContainer.counter == 'undefined' ) {
        // It has not... perform the initilization
        OpenAttributeContainer.counter=1;
    } else {
		OpenAttributeContainer.counter++;
	}
	var attributesContainer = document.createElement("div");
	attributesContainer.id = productId + "attributesContainer" + OpenAttributeContainer.counter;
	attributesContainer.name = productId + "attributesContainer" + OpenAttributeContainer.counter;
	attributesContainer.className = "packageContentAttributesContainer";
	return attributesContainer;
}

function OpenUpgradeContainer(productId) {
	if (typeof OpenUpgradeContainer.counter == 'undefined' ) {
        // It has not... perform the initilization
        OpenUpgradeContainer.counter=1;
    } else {
		OpenUpgradeContainer.counter++;
	}
	var upgradesContainer = document.createElement("div");
	var heading = document.createElement("div");
	upgradesContainer.id 	= productId + "upgradesContainer" + OpenUpgradeContainer.counter;
	upgradesContainer.name 	= productId + "upgradesContainer" + OpenUpgradeContainer.counter;
	upgradesContainer.className = "packageContentUpgradesContainer";
	heading.innerHTML 	= "<strong>Upgrades:</strong> ";
	heading.className	= "upgradeHeading";
	upgradesContainer.appendChild(heading);
	return upgradesContainer;
}

/*
XML Structure of packageContent
<packageContent>
	<productId></productId>
	<productName></productName>
	<productActualPrice></productActualPrice>
	<productPostage></productPostage>
	<productUpgradePrice></productUpgradePrice>
	<attribute>...</attribute> (MANY)
	<productUpgrade>...</productUpgrade> (MANY)
</packageContent>
*/
function AddPackageContent(packageContent,newDiv,packageCounter,packageId) {
	var attributesAndUpgradesContainer = document.createElement("div");
	if (typeof AddPackageContent.counter == 'undefined' ) {
        // It has not... perform the initilization
        AddPackageContent.counter=1;
    } else {
		AddPackageContent.counter++;
	}
	if(AddPackageContent.counter%2==0) {
		attributesAndUpgradesContainer.className="attributesAndUpgradesContainer2";
	} else {
		attributesAndUpgradesContainer.className="attributesAndUpgradesContainer";
	}
	// These counters are used to differentiate when the same product is added twice
	OpenAttributeContainer.counter=0;
	OpenUpgradeContainer.counter=0;

	var productId='';
	for(var i=0;i<packageContent.childNodes.length;i++) {
		var currentNode = packageContent.childNodes[i];
		switch(currentNode.nodeName) {
			case 'productName':
				var productName = currentNode.firstChild.nodeValue;
				var productNameContainer = document.createElement("div");
				var productNameLink = document.createElement("a");
				productNameLink.innerHTML = productName;
				productNameLink.href = "#";
				productNameLink.onclick	= function() { ShowHideUpgrades(productId + 'upgradesContainer' + packageCounter); }
				productNameContainer.appendChild(productNameLink);// = '<a href="#">' + productName + '</a>';
				productNameContainer.className="packageContentProductName";
				attributesAndUpgradesContainer.appendChild(productNameContainer);
			break;
			case 'productId':
				productId = currentNode.firstChild.nodeValue;
				if(HasAttributes(packageContent)) {
					attributesContainer = OpenAttributeContainer(productId);
				}
				if(HasUpgrades(packageContent)) {
					upgradesContainer = OpenUpgradeContainer(productId);
				}
			break;
			case 'attribute':
				var attribute = currentNode;
				var singleAttribute = AddAttributeToPackage(attribute,productId,packageCounter,packageId);
				attributesContainer.appendChild(singleAttribute);		// No need to check this exists - wouldnt get here if it didnt
			break; // End case "attribute"
			case 'productUpgrade':
				var productUpgrade = currentNode;
				var singleUpgrade = AddUpgradeToPackage(productUpgrade,productId,packageCounter,packageId);
				upgradesContainer.appendChild(singleUpgrade);
			break; // End case "productUpgrade"
		}
	}
	if(HasAttributes(packageContent)) {
		attributesAndUpgradesContainer.appendChild(attributesContainer);
	}
	if(HasUpgrades(packageContent)) {
		attributesAndUpgradesContainer.appendChild(upgradesContainer);
	}
	newDiv.appendChild(attributesAndUpgradesContainer);
}

function ShowHideUpgrades(elementId) {
	var elementToHide = document.getElementById(elementId);
	if(elementToHide.style.display == "block") {
		elementToHide.style.display = "none";
	} else {
		elementToHide.style.display = "block";
	}

}

function HandlePackageAdd(response) {
	// "Pass-through" variables
	var prefix 			= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var style 			= response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var targetElement 	= response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var divToWriteTo 	= document.getElementById(targetElement);

	// Package details
	var packageId 		= response.getElementsByTagName('packageId')[0].firstChild.nodeValue;
	var packageName 	= response.getElementsByTagName('packageName')[0].firstChild.nodeValue;
	var packagePrice 	= response.getElementsByTagName('packagePrice')[0].firstChild.nodeValue;
	var packagePostage 	= response.getElementsByTagName('packagePostage')[0].firstChild.nodeValue;

	switch(style) {
		case "orderForm":
		// Create the elements needed to display the package
		var newDiv					= document.createElement("div");
		var pound					= document.createElement("div");
		var packageDetails			= document.createElement("div");
		var newProductInput 		= document.createElement("input");
		var newPriceInput  			= document.createElement("input");
		var quantity	    		= document.createElement("input");
		var originalProductPrice	= document.createElement("input");
		var productPostage 			= document.createElement("input");
		var packageContentsList = response.getElementsByTagName('packageContentsList')[0];

		// Name & style the container
		newDiv.id			= prefix + "productContainer" + packageId;
		newDiv.name			= prefix + "productContainer" + packageId;
		newDiv.className	= "macViewOutputProductContainerOrderForm";

		// Style the input with the package name in it
		newProductInput.type = "text";
		newProductInput.className = "packageName";
		newProductInput.name = prefix + "packageName" + packageId;
		newProductInput.id	 = prefix + "packageName" + packageId;
		newProductInput.value = packageName;

		// Style the input with the package price in it
		newPriceInput.type		= "text";
		newPriceInput.className	= "packagePrice";
		newPriceInput.id = prefix + "packagePrice" + packageId;
		newPriceInput.name = prefix + "packagePrice" + packageId;
		newPriceInput.value=packagePrice;
		newPriceInput.onchange = function() { CalculateTotalPrice(prefix); }

		// Name & assign the postage for this package
		productPostage.type	= "hidden";
		productPostage.id = prefix + "packagePostage" + packageId;
		productPostage.name = prefix + "packagePostage" + packageId;
		productPostage.value=packagePostage;

		// Name & assign the quantity input field
		quantity.type="text";
		quantity.className="packageQuantity";
		quantity.name = prefix + "packageQuantity" + packageId;
		quantity.id	 = prefix + "packageQuantity" + packageId;
		quantity.value=1;
		quantity.onchange = function() {
			if(quantity.value!=0) {
				AddDuplicatePackageContent(packageContentsList,newDiv,quantity.value,packageId,packageName); CalculateTotalPrice(prefix);
			}
		}

		// Add the pound symbol
		pound.innerHTML = "£";
		pound.className = "poundSymbol";

		// Add all of the created elements to the container
		packageDetails.appendChild(newProductInput);
		packageDetails.appendChild(quantity);
		packageDetails.appendChild(productPostage);
		packageDetails.appendChild(pound);
		packageDetails.appendChild(newPriceInput);
		newDiv.appendChild(packageDetails);

		var packageNumber = document.createElement("div");
		var upDown = document.createElement("div");
		upDown.className = "packageNumberUpDown";
		packageNumber.className = "packageNumber";
		packageNumber.innerHTML = packageName + " 1";
		packageNumber.appendChild(upDown);
		newDiv.appendChild(packageNumber);
		for(var i=0;i<packageContentsList.childNodes.length;i++) {
			AddPackageContent(packageContentsList.childNodes[i],newDiv,1,packageId);
		}
		divToWriteTo.appendChild(newDiv);
		CalculateTotalPrice(prefix);
	break; // End case "orderForm"
	} // End switch
}

/* XML Structure of productUpgrade
<productUpgrade>
	<upgradeId>1455</upgradeId>
	<upgradeName>testvalues</upgradeName>
	<upgradePrice>10.0</upgradePrice>
</productUpgrade>
*/
function AddUpgradeToProduct(productUpgrade,productId,productCounter) {
	var label 				= document.createElement("label");
	var upgradePriceInput 	= document.createElement("input");
	var checkbox 			= document.createElement("input");
	var singleUpgradeContainer = document.createElement("div");

	upgradePriceInput.type="hidden"
	label.className="packageContentUpgradeLabel";

	// Loop over upgrade
	for(var k=0;k<productUpgrade.childNodes.length;k++) {
		var currentNode = productUpgrade.childNodes[k];
		switch(currentNode.nodeName) {
			case 'upgradeName':
				upgradeName = currentNode.firstChild.nodeValue;
				label.innerHTML += upgradeName;
			break;
			case 'upgradeId':
				upgradeId = currentNode.firstChild.nodeValue;
				(function(upgradeId,productId,productCounter) {
					checkbox.type="checkbox";
					checkbox.id 	= "productUgradeCheckbox" + productCounter + "productCounter" + productId + "productId" + upgradeId + "upgradeId";
					checkbox.name 	= "productUgradeCheckbox" + productCounter + "productCounter" + productId + "productId" + upgradeId + "upgradeId";
					checkbox.onchange = function() { ToggleUpgrade(upgradeId,productId,productCounter); }
					checkbox.className="packageContentCheckbox";
					upgradePriceInput.id 	= "productUgradePrice" + productCounter + "productCounter" + productId + "productId" + upgradeId + "upgradeId";
					upgradePriceInput.name 	= "productUgradePrice" + productCounter + "productCounter" + productId + "productId" + upgradeId + "upgradeId";
				})(upgradeId,productId,productCounter);
			break;
			case 'upgradePrice':
				upgradePrice = currentNode.firstChild.nodeValue;
				label.innerHTML += " £" + upgradePrice;
				upgradePriceInput.value=upgradePrice;
			break;
		} // End switch
	} // End for
	singleUpgradeContainer.appendChild(checkbox);
	singleUpgradeContainer.appendChild(label);
	singleUpgradeContainer.appendChild(upgradePriceInput);
	return singleUpgradeContainer;
}


/*
XML Structure of attribute
<attribute>
	<attributeName></attributeName>
	<attributeId></attributeId>
	<skuAttributesList>
		<skuAttribute>...</skuAttribute>...
	</skuAttributesList>
</attribute>
*/
function AddAttributeToProduct(attribute,productId,productCounter) {
	/*				var attributeValueMenu = document.createElement("select");
				attributeValueMenu.id = "productAttributeDropDown" + attributeList[i].childNodes[1].firstChild.nodeValue;
				attributeValueMenu.name = "productAttributeDropDown" + attributeList[i].childNodes[1].firstChild.nodeValue;
				attributeValueMenu.style.display="inline";
				attributeValueMenu.style.marginRight="10px";
				attributeValueMenu.style.marginTop="5px";
				attributeValueMenu.style.width="75px";
				// Label the drop down menu
				var attributeValueLabel = document.createElement("label");
				var labeltext = document.createTextNode(attributeList[i].childNodes[0].firstChild.nodeValue + ": ");
				attributeValueLabel.style.width="75px";
				attributeValueLabel.style.display="inline";
				attributeValueLabel.style.clear="none";
				attributeValueLabel.style.marginRight="5px";
				attributeValueLabel.style.marginTop="6px";
				attributeValueLabel.appendChild(labeltext);

				// Loop over attribute values
				for(var j=0;j<attributeList[i].childNodes.length;j++) {
					switch(attributeList[i].childNodes[j].firstChild.parentNode.nodeName) {
						case 'skuAttribute':
							var option 		= document.createElement("option");
							option.text 	= attributeList[i].childNodes[j].firstChild.parentNode.firstChild.firstChild.nodeValue;
							option.id		= "productAttribute" + attributeList[i].childNodes[j].firstChild.nextSibling.firstChild.nodeValue;
							option.name		= "productAttribute" + attributeList[i].childNodes[j].firstChild.nextSibling.firstChild.nodeValue;
							option.value	= "productAttribute" + attributeList[i].childNodes[j].firstChild.nextSibling.firstChild.nodeValue;
							attributeValueMenu.add(option,null);
						break;
					}
				}
				newDiv.appendChild(attributeValueLabel);
				newDiv.appendChild(attributeValueMenu);
*/
	var singleAttributeContainer = document.createElement("div");
	singleAttributeContainer.className = "productSingleAttributeContainer";
	var selectList 	= document.createElement("select");
	selectList.className="productAttributeDropDown";
	for(var k=0;k<attribute.childNodes.length;k++) {
		var currentNode	= attribute.childNodes[k];
		switch(currentNode.nodeName) {
			case "attributeId":
				attributeId = currentNode.firstChild.nodeValue;
				selectList.id 	= "productAttribute" + productCounter + "productCounter" + productId + "productId" + attributeId + "attributeId";
				selectList.name = "productAttribute" + productCounter + "productCounter" + productId + "productId" + attributeId + "attributeId";
			break;
			case "attributeName":
				var label 		= document.createElement("label");
				label.innerHTML = currentNode.firstChild.nodeValue + ": ";
				label.className = "productAttributeLabel";
			break;
			case "skuAttributesList":
				for(var i=0;i<currentNode.childNodes.length;i++) {
					var option = AddSkuToDropDown(currentNode.childNodes[i]);
					try
					{ selectList.add(option,null); } 	// Standards
					catch(ex)			// IE
					{ selectList.add(option); }
				}
			break;
		} // End switch
	} // End for
	singleAttributeContainer.appendChild(label);
	singleAttributeContainer.appendChild(selectList);
	return singleAttributeContainer;
}

/*
XML Structure for SKU
<skuAttribute>
	<skuAttributeValue></skuAttributeValue>
	<skuAttributeId></skuAttributeId>
</skuAttribute>
*/
function AddSkuToDropDown(sku) {
	var option 	= document.createElement("option");
	for(var i=0;i<sku.childNodes.length;i++) {
		currentNode = sku.childNodes[i];
		switch(currentNode.nodeName) {
			case 'skuAttributeValue':
				var skuAttributeValue = currentNode.firstChild.nodeValue;
				option.text = skuAttributeValue;
			break;
			case 'skuAttributeId':
				var skuAttributeId = currentNode.firstChild.nodeValue;
				option.id 	= "productAttributeSku" + skuAttributeId;
				option.name = "productAttributeSku" + skuAttributeId;
				option.value = skuAttributeId;
			break;
		}
	}
	return option;
}


function OpenProductAttributesWindow(productId) {
	window.open(BASE_DIRECTORY + '/view/ProductAttributeCombinationsView.php?productId=' + productId,'productAttributeWindow','menubar=1,resizeable=1,width=650,height=300');
}

function HandleProductAdd(response) {
	var prefix = response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var style = response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var productId = response.getElementsByTagName('productId')[0].firstChild.nodeValue;
	var productName = response.getElementsByTagName('productName')[0].firstChild.nodeValue;
	var productPrice = response.getElementsByTagName('productPrice')[0].firstChild.nodeValue;
	var productPostageValue = response.getElementsByTagName('productPostage')[0].firstChild.nodeValue;
	var productDescription = response.getElementsByTagName('productDescription')[0].firstChild.nodeValue;

	var attributeList	= response.getElementsByTagName('attributeList')[0];
	var upgradeList = response.getElementsByTagName('upgradeList')[0];

	var divToWriteTo = document.getElementById(targetElement);
	switch(style) {
		case 'default':
			var newDiv			= document.createElement("div");
			var newImg			= document.createElement("img");
			var textDiv			= document.createElement("div");

			newDiv.id			= prefix + "productContainer" + productId;
			newDiv.name			= prefix + "productContainer" + productId;
			newDiv.className	= "macViewOutputProductContainer";

			textDiv.innerHTML	= "<strong>" + productName + "</strong><br />";
			textDiv.innerHTML	+= productDescription + "<br />";

			newImg.src			= BASE_DIRECTORY + "/uploadImages/small/product" + productId + "image1.jpeg";

			newDiv.appendChild(newImg);
			newDiv.appendChild(textDiv);
			divToWriteTo.appendChild(newDiv);
		break;
		case 'packageUpgrade':
			var newDiv			= document.createElement("div");
			var upgradeLabel	= document.createElement("div");
			var upgradePrice	= document.createElement("input");

			newDiv.id			= prefix + "productContainer" + productId;
			newDiv.name			= prefix + "productContainer" + productId;

			upgradePrice.id		= prefix + "productUpgradePrice" + productId;
			upgradePrice.name	= prefix + "productUpgradePrice" + productId;
			upgradePrice.className = "price";
			upgradePrice.value = "0.00";

			upgradeLabel.innerHTML	= productName + "<br />";

			newDiv.appendChild(upgradePrice);
			newDiv.appendChild(upgradeLabel);
			divToWriteTo.appendChild(newDiv);
		break;
		case 'orderForm':
			var newDiv			= document.createElement("div");
			var pound			= document.createElement("div");
			var productDetailsContainer = document.createElement("div");
			var newProductInput = document.createElement("input");
			var newPriceInput   = document.createElement("input");
			var quantity	    = document.createElement("input");
			var originalProductPrice = document.createElement("input");
			var productPostage = document.createElement("input");
			var extraInfoBar	= document.createElement("div");
			var attributesAndUpgradesContainer = document.createElement("div");
			var removeProduct	= document.createElement("span");

			attributesAndUpgradesContainer.className="attributesAndUpgradesContainerProduct";

			if(attributeList.childNodes.length>0) {
				attributesContainer = OpenAttributeContainer(productId);
			}
			if(upgradeList.childNodes.length>0) {
				upgradesContainer = OpenUpgradeContainer(productId);
			}

			newDiv.id			= prefix + "productContainer" + productId;
			newDiv.name			= prefix + "productContainer" + productId;
			newDiv.className	= "macViewOutputProductContainerOrderForm";

			newProductInput.type = "text";
			newProductInput.className = "productName";
			newProductInput.name = prefix + "productName" + productId;
			newProductInput.id	 = prefix + "productName" + productId;
			newProductInput.value = productName;

			newPriceInput.type		= "text";
			newPriceInput.className	= "productPrice";
			newPriceInput.id = prefix + "productPrice" + productId;
			newPriceInput.name = prefix + "productPrice" + productId;
			newPriceInput.value=productPrice;
			newPriceInput.onchange = function() { CalculateTotalPrice(prefix); }

			productPostage.type	= "hidden";
			productPostage.id = prefix + "productPostage" + productId;
			productPostage.name = prefix + "productPostage" + productId;
			productPostage.value=productPostageValue;

			quantity.type="text";
			quantity.className="productQuantity";
			quantity.name = prefix + "productQuantity" + productId;
			quantity.id	 = prefix + "productQuantity" + productId;
			quantity.value=1;

			quantity.onchange = function() {
				if(quantity.value!=0) {
					AddDuplicateProduct(newDiv,quantity.value,productId,productName,attributeList,upgradeList); CalculateTotalPrice(prefix);
				}
			}

			pound.innerHTML = "£";
			pound.className = "poundSymbol";
			removeProduct.innerHTML = '<a href="#" onClick="ToggleVisibleInTargetElement(\'' + productId + '\',\'' + targetElement + '\',\'' + prefix + '\',\'' + style + '\'); ToggleProductChecked(\'' + productId + '\',\'' + prefix + '\')">X</a>';
			removeProduct.style.fontWeight="bold";
			removeProduct.style.color="#FF0000";
			removeProduct.style.marginTop="12px";
			removeProduct.style.marginLeft="4px";

			if(attributeList.childNodes.length>0) {
				extraInfoBar.className = "productExtraInfoBar";
				extraInfoBar.innerHTML = '<a href="#" onClick="OpenProductAttributesWindow(' + productId + ')">View Attribute Combinations</a>';
			}

			productDetailsContainer.appendChild(newProductInput);
			productDetailsContainer.appendChild(quantity);
			productDetailsContainer.appendChild(productPostage);
			productDetailsContainer.appendChild(pound);
			productDetailsContainer.appendChild(newPriceInput);
			productDetailsContainer.appendChild(removeProduct);
			productDetailsContainer.appendChild(extraInfoBar);
			newDiv.appendChild(productDetailsContainer);

			// Loop over whole attributes
			for(var i=0;i<attributeList.childNodes.length;i++) {
				var singleAttributeContainer = AddAttributeToProduct(attributeList.childNodes[i],productId,1);
				attributesContainer.appendChild(singleAttributeContainer);
			} // End attribute display

			// Loop over upgrades
			for(var i=0;i<upgradeList.childNodes.length;i++) {
				var singleUpgradeContainer = AddUpgradeToProduct(upgradeList.childNodes[i],productId,1);
				upgradesContainer.appendChild(singleUpgradeContainer);
			} // End attribute display

			if(attributeList.childNodes.length>0) {
				attributesAndUpgradesContainer.appendChild(attributesContainer);
			}
			if(upgradeList.childNodes.length>0) {
				attributesAndUpgradesContainer.appendChild(upgradesContainer);
			}
			if(upgradeList.childNodes.length>0 || attributeList.childNodes.length>0) {
				newDiv.appendChild(attributesAndUpgradesContainer);
			}
			divToWriteTo.appendChild(newDiv);
			CalculateTotalPrice(prefix);
		break;
	}
}

/****** MAKE FOCUSED FUNCTIONS ******/
//! Makes the top level category categoryId focussed
function FocusTopLevel(categoryId,prefix) {
	var allDivs = document.getElementsByTagName('div');
	for(var i=0;i<allDivs.length;i++) {
		if(-1!=allDivs[i].id.indexOf('topLevelCategory' + categoryId)) {
				allDivs[i].style.backgroundColor="#C0D2EC";
				allDivs[i].style.backgroundImage="url(" + BASE_DIRECTORY + "/wombat7/dtree/img/folderopenBlue.gif)";
				allDivs[i].style.backgroundRepeat="no-repeat";
				allDivs[i].style.fontWeight="bold";
		} else {
			if(-1!=allDivs[i].id.indexOf('topLevelCategory')) {
				allDivs[i].style.backgroundColor="#FFFFFF";
				allDivs[i].style.backgroundImage="url(" + BASE_DIRECTORY + "/wombat7/dtree/img/folder.gif)";
				allDivs[i].style.backgroundRepeat="no-repeat";
				allDivs[i].style.fontWeight="normal";
			}
		}
	}
}

// Changes the sub level that is currently focused by setting all others to non-focused, then changing the current to focused
// Depends on the element names/ids being consistent - probably good to make these parameters?
function FocusSubLevel(subcategoryId,prefix) {
	var allDivs = document.getElementsByTagName('div');
	for(var i=0;i<allDivs.length;i++) {
		if(-1!=allDivs[i].id.indexOf('subLevelCategory' + subcategoryId)) {
				allDivs[i].style.backgroundColor="#C0D2EC";
				allDivs[i].style.backgroundImage="url(" + BASE_DIRECTORY + "/wombat7/dtree/img/folderopenBlue.gif)";
				allDivs[i].style.backgroundRepeat="no-repeat";
				allDivs[i].style.fontWeight="bold";
		} else {
			if(-1!=allDivs[i].id.indexOf('subLevelCategory')) {
				allDivs[i].style.backgroundColor="#FFFFFF";
				allDivs[i].style.backgroundImage="url(" + BASE_DIRECTORY + "/wombat7/dtree/img/folder.gif)";
				allDivs[i].style.backgroundRepeat="no-repeat";
				allDivs[i].style.fontWeight="normal";
			}
		}
	}
}


/****** BOOLEAN RETURN FUNCTIONS ******/
function HasAttributes(product) {
	for(var i=0;i<product.childNodes.length;i++) {
		var currentNode = product.childNodes[i];
		if(currentNode.nodeName=="attribute") {
			return true;
		}
	}
return false;
}

function HasUpgrades(product) {
	for(var i=0;i<product.childNodes.length;i++) {
		var currentNode = product.childNodes[i];
		if(currentNode.nodeName=="productUpgrade") {
			return true;
		}
	}
return false;
}

/****** REMOVE FUNCTIONS ******/
function HandleProductRemove(response) {
	var prefix = response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var productId = response.getElementsByTagName('productId')[0].firstChild.nodeValue;
	var oldDiv = document.getElementById(prefix + "productContainer" + productId);
	var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var style = response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var divToWriteTo = document.getElementById(targetElement);
	divToWriteTo.removeChild(oldDiv);
	if(style=='orderForm') {
		CalculateTotalPrice(prefix);
	}
}

function HandlePackageRemove(response) {
	var prefix = response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
	var packageId = response.getElementsByTagName('packageId')[0].firstChild.nodeValue;
	var oldDiv = document.getElementById(prefix + "productContainer" + packageId);
	var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
	var style = response.getElementsByTagName('style')[0].firstChild.nodeValue;
	var divToWriteTo = document.getElementById(targetElement);
	divToWriteTo.removeChild(oldDiv);
	if(style=='orderForm') {
		CalculateTotalPrice(prefix);
	}
}

/****** TOGGLE FUNCTIONS ******/
// Toggles whether an upgrade is selected (and it's price added to the total) depending on whether the checkbox with ID "productUpgrade{PRODUCTID}" is checked
function ToggleUpgrade(upgradeProductId,productId,productCounter) {
	var upgradeCheckbox = document.getElementById("productUgradeCheckbox" + productCounter + "productCounter" + productId + "productId" + upgradeProductId + "upgradeId");
	var totalPrice 		= document.getElementById('orderTotalPrice');
	var upgradePrice 	= document.getElementById("productUgradePrice" + productCounter + "productCounter" + productId + "productId" + upgradeProductId + "upgradeId").value;
	var tempCount 		= 0;
	if(upgradeCheckbox.checked) {
		tempCount = parseFloat(totalPrice.value) + parseFloat(upgradePrice);
	} else {
		tempCount = parseFloat(totalPrice.value) - parseFloat(upgradePrice);
	}
	totalPrice.value=Math.round(tempCount*100)/100;
}

// Toggles whether an upgrade is selected (and its price added to the total) for a package, depending on whether the checkbox with ID "packageContentUpgrade{PRODUCTID}" is checked
// Rationale: the upgrades for a package need a totally unique identifier, as it is possible for the same upgrade to occur twice in a package, but for different products; this is why both productId and packageUpgradeId are used
function TogglePackageUpgrade(productId,packageUpgradeId,packageId,packageCounter) {
	var upgradeCheckbox = document.getElementById("packageContentUgradeCheckbox" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + packageUpgradeId + "upgradeId");
	var totalPrice		= document.getElementById('orderTotalPrice');
	var upgradePrice	= document.getElementById("packageContentUgradePrice" + packageId + "packageId" + packageCounter + "packageCounter" + productId + "productId" + packageUpgradeId + "upgradeId").value;
	var tempCount = 0;
	if(upgradeCheckbox.checked) {
		tempCount = parseFloat(totalPrice.value) + parseFloat(upgradePrice);
	} else {
		tempCount = parseFloat(totalPrice.value) - parseFloat(upgradePrice);
	}
	totalPrice.value=Math.round(tempCount*100)/100;
}

//! Toggles whether the checkbox next to a product is checked or not
function ToggleProductChecked(prodId,prefix) {
	tempCheck = document.getElementById(prefix + "CHECK" + prodId);
	if(tempCheck.checked) {
		tempCheck.checked=false;
	} else {
		tempCheck.checked=true;
	}
}

//! Toggles whether the checkbox next to a package is checked or not
function TogglePackageChecked(packageId,prefix) {
	tempCheck = document.getElementById(prefix + "CHECK" + packageId);
	if(tempCheck.checked) {
		tempCheck.checked=false;
	} else {
		tempCheck.checked=true;
	}
}

// Toggles whether a package is visible in the target element (Difference between product and package currently is just the AJAX request)
function ToggleVisiblePackageInTargetElement(packageId,targetElement,prefix,style) {
	var alreadyThere 	= document.getElementsByName(prefix + packageId);
	var divToWriteTo 	= document.getElementById(targetElement);
	if(0 != alreadyThere.length) {
		var oldInput 	= document.getElementById(prefix + packageId);
		divToWriteTo.removeChild(oldInput);
		MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?packageRemove=" + packageId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style);
	} else {
		var newInput 	= document.createElement("input");
		newInput.name	= prefix + packageId;
		newInput.id		= prefix + packageId;
		newInput.type	= "hidden";
		newInput.value	= prefix + packageId;
		divToWriteTo.appendChild(newInput);
		MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?packageAdd=" + packageId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style);
	}
}

//! Toggles whether the product is displayed in the targetElement (generally a DIV)
function ToggleVisibleInTargetElement(productId,targetElement,prefix,style) {
	var alreadyThere 	= document.getElementsByName(prefix + productId);
	var divToWriteTo 	= document.getElementById(targetElement);
	if(0 != alreadyThere.length) {
//		alert(prefix + productId);
		var oldInput 	= document.getElementById(prefix + productId);
		divToWriteTo.removeChild(oldInput);
		MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?productRemove=" + productId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style);
	} else {
		var newInput 	= document.createElement("input");
		newInput.name	= prefix + productId;
		newInput.id		= prefix + productId;
		newInput.type	= "hidden";
		newInput.value	= prefix + productId;
		divToWriteTo.appendChild(newInput);
		MakeRequest(BASE_DIRECTORY + "/ajaxHandlers/MacFinderAjaxHandler.php?productAdd=" + productId + "&targetElement=" + targetElement + "&prefix=" + prefix + "&style=" + style);
	}
}



