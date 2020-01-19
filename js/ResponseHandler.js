function ResponseHandler(httpRequest) {
	if (httpRequest.readyState == 4) {
		if (httpRequest.status == 200) {
			var response = httpRequest.responseXML.documentElement;
			/*
			for(var i=0; i<response.childNodes.length; i++) {
				if(null==response.childNodes[i].firstChild) {
					alert(response.childNodes[i+1].nodeName);
					newel=httpRequest.responseXML.createElement(response.childNodes[i+1].nodeName);
					newtext=httpRequest.responseXML.createTextNode("first");
					newel.appendChild(newtext);
					dump(newel);
					response.appendChild(newel);
				}
				//alert(response.childNodes[i].firstChild.nodeValue);
			}*/

			var who = response.getElementsByTagName('who')[0].firstChild.nodeValue;

			switch(who) {
				case 'MacView' :
					MacViewHandler(response);
				break;
				case 'CategorySelector' :
					CategorySelectorHandler(response);
				break;
				case 'OrderSuggest' :
					OrderSuggestHandler(response);
				break;
				case 'AddressSuggest' :
					AddressSuggestHandler(response);
				break;
			}
		} else {
			//alert(httpRequest.responseXML);
        	alert('There was a problem with the request.');
        }
	}
}




