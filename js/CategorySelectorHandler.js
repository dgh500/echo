function CategorySelectorHandler(response) {

	var what = response.getElementsByTagName('what')[0].firstChild.nodeValue;

	switch (what) {
		case 'addTopLevel':
			var prefix 				= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
			var targetElement		= response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
			var catId				= response.getElementsByTagName('categoryId')[0].firstChild.nodeValue;

			ToggleCategory(catId,targetElement,prefix);
		break;
		case 'openTopLevel':
			var prefix 				= response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
			var targetElement		= response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
			var subCategoryIdList 	= response.getElementsByTagName('subCategoryId');
			var subCategoryNameList = response.getElementsByTagName('subCategoryName');

			var secondLevelContainer 	= document.getElementById(prefix + "subLevelCategoryContainer");

			// Reset the Div
			secondLevelContainer.innerHTML="";

			// Add each sub category to the div
			for(var i=0;i<subCategoryIdList.length;i++) {
				(function(i) {
				var tempDiv	 = document.createElement("div");
				var tempLink = document.createElement("a");
				var subCatId 	= subCategoryIdList[i].firstChild.nodeValue;
				var subCatName 	= subCategoryNameList[i].firstChild.nodeValue;
				var tempCheck = document.createElement("input");

				// Style the div
				tempDiv.className 	= "categorySelectorViewMenuItem";
				tempDiv.id			= "subLevelCategory" + subCatId;
				tempDiv.name		= "subLevelCategory";
				tempDiv.setAttribute("name","subLevelCategory");

				// Make the link
				tempLink.innerHTML 	= subCatName;
				tempLink.href		= "#";

				var alreadyChecked = document.getElementsByName(prefix + subCatId);
				if(0 != alreadyChecked.length) {
					tempCheck.defaultChecked	= true;	// IE
					tempCheck.checked			= true;	// FF
				}

				// Make the checkbox
				tempCheck.type		= "checkbox";
				tempCheck.id		= prefix + "CHECK" + subCatId;
				tempCheck.name		= prefix + "CHECK" + subCatId;
				tempCheck.style.marginLeft = "-4px";
				tempCheck.style.marginRight = "4px";
				tempCheck.style.width = "auto";
				tempCheck.onclick	= function() { ToggleCategory(subCatId,targetElement,prefix); }

				// Add link to the div
				tempDiv.appendChild(tempCheck);
				tempDiv.appendChild(tempLink);

				// Add the div to the container
				secondLevelContainer.appendChild(tempDiv);
			})(i);}
		break;

		case 'categoryAdd':
			var prefix = response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
			var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
			var categoryId = response.getElementsByTagName('categoryId')[0].firstChild.nodeValue;
			var categoryName = response.getElementsByTagName('categoryName')[0].firstChild.nodeValue;
			var parentCategory = response.getElementsByTagName('parentCategory')[0].firstChild.nodeValue;
			var divToWriteTo = document.getElementById(targetElement);

			var newDiv			= document.createElement("div");
			newDiv.id			= prefix + "categoryContainer" + categoryId;
			newDiv.name			= prefix + "categoryContainer" + categoryId;
			newDiv.className	= "categoryViewOutputProductContainer";

			var newImg			= document.createElement("img");

			var textDiv			= document.createElement("div");
			if(parentCategory=="X") {
				textDiv.innerHTML	= "<strong>" + categoryName + "</strong><br />";
			} else {
				textDiv.innerHTML	= "<strong>" + parentCategory + " > " + categoryName + "</strong><br />";
			}

			newDiv.appendChild(newImg);
			newDiv.appendChild(textDiv);
			divToWriteTo.appendChild(newDiv);
		break;
		case 'categoryRemove':
			var prefix = response.getElementsByTagName('prefix')[0].firstChild.nodeValue;
			var categoryId = response.getElementsByTagName('categoryId')[0].firstChild.nodeValue;
			var oldDiv = document.getElementById(prefix + "categoryContainer" + categoryId);
			var targetElement = response.getElementsByTagName('targetElement')[0].firstChild.nodeValue;
			var divToWriteTo = document.getElementById(targetElement);

			divToWriteTo.removeChild(oldDiv);
		break;
	}
}

function ToggleCategory(subCatId,targetElement,prefix) {
	var alreadyThere 	= document.getElementsByName(prefix + subCatId);
	var divToWriteTo 	= document.getElementById(targetElement);

	if(0 != alreadyThere.length) {
		var oldInput 	= document.getElementById(prefix + subCatId);
		divToWriteTo.removeChild(oldInput);
		MakeRequest(baseDir + "/ajaxHandlers/CategorySelectorAjaxHandler.php?categoryRemove=" + subCatId + "&targetElement=" + targetElement + "&prefix=" + prefix);
	} else {
		var newInput 	= document.createElement("input");
		newInput.name	= prefix + subCatId;
		newInput.id		= prefix + subCatId;
		newInput.type	= "hidden";
		newInput.value	= prefix + subCatId;
		divToWriteTo.appendChild(newInput);
		MakeRequest(baseDir + "/ajaxHandlers/CategorySelectorAjaxHandler.php?categoryAdd=" + subCatId + "&targetElement=" + targetElement + "&prefix=" + prefix);
	}
}