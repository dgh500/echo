if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

function searchSuggest() {
	var str = document.getElementById('addressSearchText').value;
	var method = document.getElementById('method').value;
	var SuggestBox = document.getElementById('suggestions');
	if(str.length>2) {
		MakeRequest(BASE_DIRECTORY + '/ajaxHandlers/AddressSearchSuggestAjaxHandler.php?method=' + method + '&sofar=' + str);
	} else {
		SuggestBox.style.display="none";	
	}
}

function clearSuggest() {
	var SuggestBox = document.getElementById('suggestions');
	SuggestBox.style.display="none";	
}

function selectSuggestion(selection,addressId,line1val,line2val,line3val,countyVal,postcodeVal,nameVal,emailVal,phoneVal) {
	var searchBox = document.getElementById('addressSearchText');
	var hiddenId  = document.getElementById('id');
	var line1	  = document.getElementById('line1');
	var line2	  = document.getElementById('line2');	
	var line3	  = document.getElementById('line3');
	var county	  = document.getElementById('selectedCounty');	
	var deliveryPostcode  = document.getElementById('selectedPostcode');
	var selectedCustomerName = document.getElementById('selectedCustomerName');
	var selectedCustomerEmail = document.getElementById('selectedCustomerEmail');
	var selectedCustomerPhone = document.getElementById('selectedCustomerPhone');
	var SuggestBox = document.getElementById('suggestions');
	
	searchBox.value = selection;
	hiddenId.value = addressId;
	line1.value = line1val;
	line2.value = line2val;
	line3.value = line3val;
	county.value = countyVal;
	deliveryPostcode.value = postcodeVal;
	selectedCustomerName.value = nameVal;
	selectedCustomerEmail.value = emailVal;
	selectedCustomerPhone.value = phoneVal;
	
	SuggestBox.innerHTML = '';
	SuggestBox.style.display="none";
}

function AddressSuggestHandler(response) {
	var AddressIdList = response.getElementsByTagName('addressId');	
	var AddressLine1List = response.getElementsByTagName('line1');	
	var AddressLine2List = response.getElementsByTagName('line2');	
	var AddressLine3List = response.getElementsByTagName('line3');	
	var CountyList = response.getElementsByTagName('county');
	var PostcodeList = response.getElementsByTagName('postcode');
	var CustomerNameList = response.getElementsByTagName('customerName');
	var CustomerEmailList = response.getElementsByTagName('customerEmail');
	var CustomerPhoneList = response.getElementsByTagName('customerPhone');
	var SuggestBox = document.getElementById('suggestions');
	
	SuggestBox.innerHTML = '';
	SuggestBox.style.display="block";
	
	for(var i=0;i<PostcodeList.length;i++) {
		SuggestBox.innerHTML += '<a href="#" onclick="selectSuggestion(\'' + PostcodeList[i].firstChild.nodeValue + '\',\'' + AddressIdList[i].firstChild.nodeValue + '\',\'' + AddressLine1List[i].firstChild.nodeValue + '\',\'' + AddressLine2List[i].firstChild.nodeValue + '\',\'' + AddressLine3List[i].firstChild.nodeValue + '\',\'' + CountyList[i].firstChild.nodeValue + '\',\'' + PostcodeList[i].firstChild.nodeValue + '\', \'' + CustomerNameList[i].firstChild.nodeValue + '\', \'' + CustomerEmailList[i].firstChild.nodeValue + '\', \'' + CustomerPhoneList[i].firstChild.nodeValue + '\')">' + PostcodeList[i].firstChild.nodeValue + ' (' + AddressLine1List[i].firstChild.nodeValue.substr(0,20) + '...)</a><br />';
	}
}

function fillInFields() {
	// Parent fields
	var currentFrame = parent.document.getElementById('addressSearchContainer');
	var delivery1 = parent.document.getElementById('delivery1');
	var delivery2 = parent.document.getElementById('delivery2');
	var delivery3 = parent.document.getElementById('delivery3');
	var county = parent.document.getElementById('county');
	var deliveryPostcode = parent.document.getElementById('deliveryPostcode');
	var customerName = parent.document.getElementById('customerName');
	var customerEmail = parent.document.getElementById('emailAddress');
	var customerPhone = parent.document.getElementById('telephoneNumber');
	
	// Hidden fields
	var hiddenId  = document.getElementById('id');
	var selecteddelivery1 = document.getElementById('line1');
	var selecteddelivery2 = document.getElementById('line2');	
	var selecteddelivery3 = document.getElementById('line3');
	var selectedCounty = document.getElementById('selectedCounty');
	var selectedPostcode = document.getElementById('selectedPostcode');
	var selectedName = document.getElementById('selectedCustomerName');
	var selectedEmail = document.getElementById('selectedCustomerEmail');
	var selectedPhone = document.getElementById('selectedCustomerPhone');
	
	// Copy values accross
	delivery1.value = selecteddelivery1.value;
	delivery2.value = selecteddelivery2.value;
	delivery3.value = selecteddelivery3.value;
	county.value = selectedCounty.value;
	deliveryPostcode.value = selectedPostcode.value;
	customerName.value = selectedName.value;
	customerEmail.value = selectedEmail.value;
	customerPhone.value = selectedPhone.value;
	currentFrame.style.display="none";
}



