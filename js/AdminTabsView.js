if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

// JavaScript Document
$(document).ready(function() {
	$("#moreAdminTabsLink").click(function() {
		if($("#moreAdminTabsLink").text() == 'More') {									   
			$("#moreAdminTabs").show();
			$("#moreAdminTabsLink").text('Less');
			$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/ajaxHandlers/AdminMoreLessAjaxHandler.php",
					data: 'action=showMore',
					async: false
				});
		} else {
			$("#moreAdminTabs").hide();
			$("#moreAdminTabsLink").text('More');
			$.ajax({
					type: "POST",
					url: BASE_DIRECTORY + "/ajaxHandlers/AdminMoreLessAjaxHandler.php",
					data: 'action=showLess',
					async: false
				});			
		}	
	});
	
});