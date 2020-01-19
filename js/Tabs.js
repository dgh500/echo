// Based on the doxygen/sliding doors way of doing tabs, this function requires: 
/*
 * tabsArray - An array of arrays, where for example tabsArray[0] is an array such that tabsArray[0][0] is the tab name, and tabsArray[0][1] is the prefix to "ContentArea" to display
 * currentTab - The name of the current tab (string)
 * tabSetName - A prefix to each <li> id field
 * Example..
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','catalogueDetails');
	tabsArray[1] = new Array('manufacturers','catalogueManufacturers');
	tabsArray[2] = new Array('estimates','catalogueEstimates');
	...
<li id="adminPackageViewTabContainer-manufacturers"><a href="#" onclick="showTab(tabsArray,'manufacturers','adminPackageViewTabContainer');" id="manufacturersLink">Manufacturers</a></li>
<li id="adminPackageViewTabContainer-estimates"><a href="#" onclick="showTab(tabsArray,'estimates','adminPackageViewTabContainer');" id="estimatesLink">Estimates</a></li>
	...
	In this case adminPackageViewContainer is the tabSetName, currentTab is manufacturers, and tabsArray is the set of all links on the page
 */
function showTab(tabsArray,currentTab,tabSetName) {
	for(var i=0; i<tabsArray.length;i++) {	
		if(currentTab == tabsArray[i][0]) {
			$("#" + tabSetName + "-" + tabsArray[i][0] + "").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFF"});
			$("#" + tabsArray[i][0] + "Link").css({backgroundPosition: "100% -150px"});
			$("#" + tabsArray[i][1] + "ContentArea").css({display: "block"});
		} else {
			$("#" + tabSetName + "-" + tabsArray[i][0] + "").css({backgroundPosition: "left top", borderBottom: "0px"});
			$("#" + tabsArray[i][0] + "Link").css({backgroundPosition: "right top"});
			$("#" + tabsArray[i][1] + "ContentArea").css({display: "none"});			
		}
	}
}