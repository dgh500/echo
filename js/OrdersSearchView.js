function searchSuggest() {
	var str = document.getElementById('ordersSearchText').value;
	var method = document.getElementById('method').value;
	var SuggestBox = document.getElementById('suggestions');
	if(str.length>2) {
		MakeRequest(secureBaseDir + '/ajaxHandlers/OrderSearchSuggestAjaxHandler.php?method=' + method + '&sofar=' + str);
	} else {
		SuggestBox.style.display="none";
	}
}

function clearSuggest() {
	var SuggestBox = document.getElementById('suggestions');
	SuggestBox.style.display="none";
}

function selectSuggestion(selection,orderId) {
	var searchBox = document.getElementById('ordersSearchText');
	var hiddenId  = document.getElementById('id');
	var SuggestBox = document.getElementById('suggestions');
	searchBox.value = selection;
	hiddenId.value = orderId;
	SuggestBox.innerHTML = '';
	SuggestBox.style.display="none";
}

function OrderSuggestHandler(response) {
	var OrderIdList = response.getElementsByTagName('orderId');
	var CreatedList = response.getElementsByTagName('created');

	var OrderList = response.getElementsByTagName('order');
	var PostcodeList = response.getElementsByTagName('postcode');
	var CustomerFirstNameList = response.getElementsByTagName('firstName');
	var CustomerList = response.getElementsByTagName('lastName');

	var SuggestBox = document.getElementById('suggestions');

	SuggestBox.innerHTML = '';
	SuggestBox.style.display="block";

	for(var i=0;i<OrderList.length;i++) {
		SuggestBox.innerHTML += '<a href="#" onClick="selectSuggestion(\'' + OrderList[i].firstChild.nodeValue + '\',\'' + OrderIdList[i].firstChild.nodeValue + '\');" onclick="clearSuggest();">' + OrderList[i].firstChild.nodeValue + ' : ' + CreatedList[i].firstChild.nodeValue + '</a> (ECHO' + OrderIdList[i].firstChild.nodeValue + ')<br />';
	}

	for(var i=0;i<PostcodeList.length;i++) {
		SuggestBox.innerHTML += '<a href="#" onClick="selectSuggestion(\'' + PostcodeList[i].firstChild.nodeValue + '\',\'' + OrderIdList[i].firstChild.nodeValue + '\');" onclick="clearSuggest();">' + PostcodeList[i].firstChild.nodeValue + ' : ' + CreatedList[i].firstChild.nodeValue + '</a> (ECHO' + OrderIdList[i].firstChild.nodeValue + ')<br />';
	}

	for(var i=0;i<CustomerList.length;i++) {
		SuggestBox.innerHTML += '<a href="#" onClick="selectSuggestion(\'' + CustomerList[i].firstChild.nodeValue + '\',\'' + OrderIdList[i].firstChild.nodeValue + '\');" onclick="clearSuggest();">' + CustomerFirstNameList[i].firstChild.nodeValue + ' ' + CustomerList[i].firstChild.nodeValue + ' : ' + CreatedList[i].firstChild.nodeValue + '</a> (ECHO' + OrderIdList[i].firstChild.nodeValue + ')<br />';
	}
}