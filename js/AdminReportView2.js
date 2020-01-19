if(-1==location.protocol.indexOf('https')) {
	var BASE_DIRECTORY = baseDir;
} else {
	var BASE_DIRECTORY = secureBaseDir;
}

$(document).ready(function(){

	// On catalogue click, load the catalogues categories and manufacturers
	$('#catalogueGo').click(EventChooseCatalogue);

	// Hide options afterwards
	$("#catalogueChoice").change(function() {
		$('#categoryManufacturerContainer').hide();
		$('#productContainer').hide();
		$('#dateRangeContainer').hide();
		$('#generateReportContainer').hide();
	});

	// AJAX Loader
	$("#loading img").ajaxStart(function(){
		$(this).attr("src",BASE_DIRECTORY + "/admin/images/reportAjaxLoading.gif");
	});
	// AJAX Loader
	$("#loading img").ajaxStop(function(){
		$(this).attr("src",BASE_DIRECTORY + "/admin/images/reportAjaxLoadingComplete.gif");
	});

});	// End document ready 

function EventGenerateReport() {
	$.ajax({	
			type: 	"POST",
			url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
			data: 	'event=generateReport&' + $("#dateAndProductsForm").serialize(),
			async: 	false,
			success: function(data) {
				// Show Response
				$("#reportGraphContainer").show();
				$("#legendContainer").show();	
				$("#reportTotalContainer").show();	
				var dataArray = data.split('DATASPLIT');
				$("#reportGraphContainer").append(dataArray[0]);
				$("#reportTotalContainer").html(dataArray[1]);
				
			}
		});	
}

function EventChooseDateRange() {
	$.ajax({	
			type: 	"POST",
			url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
			data: 	'event=dateRangeChosen&' + $("#dateRangeForm").serialize(),
			async: 	false,
			success: function(data) {
				// Show Response
				$("#generateReportContainer").html(data);
				$("#generateReportContainer").show();
				// Bind the event
				$("#generateReportContainer").click(EventGenerateReport);
				// Bind to 'hide newer' options
				$("#startDate, #endDate").change(function() {
					$('#generateReportContainer').hide();
				});			
				$("#reportDateRange a").click(function() {
					$('#generateReportContainer').hide();
					$('#reportGraphContainer').hide();
					$('#legendContainer').hide();
					$('#reportTotalContainer').hide();
				});
			}
		});	
}

function EventChooseProduct() {
	$.ajax({	
			type: 	"POST",
			url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
			data: 	'event=productChosen&' + $("#productChoiceForm").serialize(),
			async: 	false,
			success: function(data) {
				$("#dateRangeContainer").html(data);
				// Bind the event to the new elements
				$('#dateRangeChoiceGo').click(EventChooseDateRange);
				// Initialise the date pickers
				$("#startDate").datepicker({ dateFormat: 'dd/mm/yy' });
				$("#endDate").datepicker({ dateFormat: 'dd/mm/yy' });
				var myDate = new Date();
				$('#endDate').val(myDate.getDate() + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear());
				myDate.setDate(myDate.getDate()-7); // One week in the past by default
				$('#startDate').val(myDate.getDate() + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear());					
				$("#dateRangeContainer").show();	
				// Bind to 'hide newer' options
				$("#productChoice").change(function() {
					$('#dateRangeContainer').hide();
					$('#generateReportContainer').hide();
					$('#reportGraphContainer').hide();
					$('#legendContainer').hide();
					$('#reportTotalContainer').hide();
				});				
			} // End success function
		});	// End .ajax
}

function EventChooseCategoryManufacturer() {
	$.ajax({	
			type: 	"POST",
			url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
			data: 	'event=categoryManufacturerChosen&' + $("#categoryManufacturerForm").serialize(),
			async: 	false,
			success: function(data) {
				$("#productContainer").html(data);
				$("#productContainer").show();
				// Bind the event to the new elements
				$('#productChoiceGo').click(EventChooseProduct); 
				// Bind to 'hide newer' options
				$("#categoryChoice, #manufacturerChoice").change(function() {
					$('#productContainer').hide();
					$('#dateRangeContainer').hide();
					$('#generateReportContainer').hide();
					$('#reportGraphContainer').hide();
					$('#legendContainer').hide();
					$('#reportTotalContainer').hide();
				});				
			}
		});	
}

function EventChooseCatalogue() {
	$.ajax({	
		type: 	"POST",
		url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
		data: 	'event=catalogueChosen&' + $("#catalogueChoiceForm").serialize(),
		async: 	false,
		success: function(data) {
			$("#categoryManufacturerContainer").html(data);
			$("#categoryManufacturerContainer").show();
			// Bind the event to the new elements
			$('#categoryManufacturerGo').click(EventChooseCategoryManufacturer); 
			$('#categoryChoice').blur(EventChangeCategory);
			//$('#manufacturerChoice').blur(EventChangeManufacturer);
		}
	});	 
}

function EventChangeManufacturer() {
	$.ajax({	
		type: 	"POST",
		url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
		data: 	'event=changeManufacturer&newManufacturer=' + $("#manufacturerChoice").val() + '&catalogueList=' + $("#catalogueList").val(),
		async: 	false,
		success: function(data) {
			$("#categoryChoice").html(data);
		}
	});	 
}

function EventChangeCategory() {
	$.ajax({	
		type: 	"POST",
		url: 	BASE_DIRECTORY + "/ajaxHandlers/AdminReportViewAjaxHandler2.php",
		data: 	'event=changeCategory&newCategory=' + $("#categoryChoice").val() + '&catalogueList=' + $("#catalogueList").val(),
		async: 	false,
		success: function(data) {
			$("#manufacturerChoice").html(data);
		}
	});	 
}

// JavaScript Document
function switchDate(newDate) {
	
	var startDateInput = document.getElementById('startDate');
	var endDateInput   = document.getElementById('endDate');
	var myDate = new Date();
	
	switch(newDate) {
		case 'lastWeek':
				endDate = '';
				endDate = endDate + myDate.getDate() + '/';
				endDate = endDate + (myDate.getMonth()+1) + '/';
				endDate = endDate + myDate.getFullYear();
				endDateInput.text = endDate;
				endDateInput.value = endDate;
			myDate.setDate(myDate.getDate()-7);
				startDate = '';
				startDate = startDate + myDate.getDate() + '/';
				startDate = startDate + (myDate.getMonth()+1) + '/';
				startDate = startDate + myDate.getFullYear();
				startDateInput.text = startDate;
				startDateInput.value = startDate;			
		break;
		case 'lastMonth':
				endDate = '';
				endDate = endDate + myDate.getDate() + '/';
				endDate = endDate + (myDate.getMonth()+1) + '/';
				endDate = endDate + myDate.getFullYear();
				endDateInput.text = endDate;
				endDateInput.value = endDate;		
			myDate.setDate(myDate.getDate()-30);
				startDate = '';
				startDate = startDate + myDate.getDate() + '/';
				startDate = startDate + (myDate.getMonth()+1) + '/';
				startDate = startDate + myDate.getFullYear();
				startDateInput.text = startDate;
				startDateInput.value = startDate;			
		break;
		case 'lastYear':
				endDate = '';
				endDate = endDate + myDate.getDate() + '/';
				endDate = endDate + (myDate.getMonth()+1) + '/';
				endDate = endDate + myDate.getFullYear();
				endDateInput.text = endDate;
				endDateInput.value = endDate;		
			myDate.setDate(myDate.getDate()-365);
				startDate = '';
				startDate = startDate + myDate.getDate() + '/';
				startDate = startDate + (myDate.getMonth()+1) + '/';
				startDate = startDate + myDate.getFullYear();
				startDateInput.text = startDate;
				startDateInput.value = startDate;		
		break;	
		case 'allTime':
				endDate = '';
				endDate = endDate + myDate.getDate() + '/';
				endDate = endDate + (myDate.getMonth()+1) + '/';
				endDate = endDate + myDate.getFullYear();
				endDateInput.text = endDate;
				endDateInput.value = endDate;
			startTime = new Date("January 1, 2008");
				startDate = '';
				startDate = startDate + startTime.getDate() + '/';
				startDate = startDate + (startTime.getMonth()+1) + '/';
				startDate = startDate + startTime.getFullYear();
				startDateInput.text = startDate;
				startDateInput.value = startDate;			
		break;			
	}
}