function changeSortBy(element) {
	if(-1 == window.location.href.indexOf('sortBy')) {
		if(-1 == window.location.href.indexOf('aid')) {
			var redirectBase = window.location.href;
		} else {
			// Remove the affiliate ID (removes everything after /aid)
			var tempStr = window.location.href.split("/aid");
			var redirectBase = tempStr[0];
		}
		// Remove showAll
		if(-1 != window.location.href.indexOf('showAll')) {
			var tempStr = redirectBase.split('/showAll');
			var redirectBase = tempStr[0];
		}
	} else {
		// Remove the previous sortby
		var tempStr = window.location.href.split("/sortBy");
		var redirectBase = tempStr[0];
	}

	switch(element.value) {
		case 'sortByPriceLowest':
			window.location.href = redirectBase + '/sortBy/price/asc';
		break;
		case 'sortByPriceHighest':
			window.location.href = redirectBase + '/sortBy/price/desc';
		break;
		case 'sortByNameAsc':
			window.location.href = redirectBase + '/sortBy/name/asc';
		break;
		case 'sortByNameDesc':
			window.location.href = redirectBase + '/sortBy/name/desc';
		break;
		case 'showAll':
			if(-1 != window.location.href.indexOf('/page')) {
				// Remove any page(s)
				var tempStr = window.location.href.split("/page");
				var redirectBase = tempStr[0];	
			}
			window.location.href = redirectBase + '/showAll';
		break;		
	}
}
/*
Tips and Notes
Note: The indexOf() method is case sensitive!
Note: This method returns -1 if the string value to search for never occurs.*/