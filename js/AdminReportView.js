// jQuery the AJAX form
$(function() {
	$("#startDate").datepicker({ dateFormat: 'dd/mm/yy' });
	$("#endDate").datepicker({ dateFormat: 'dd/mm/yy' });
});

$(document).ready(function(){
	
	var myDate = new Date();
	$('#endDate').val(myDate.getDate() + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear());
	myDate.setDate(myDate.getDate()-7); // One week in the past by default
	$('#startDate').val(myDate.getDate() + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear());	

	$('#generateReport').click(function(){
		$('#reportResults').hide();
		$.post(	
			   	baseDir + "/ajaxHandlers/AdminReportAjaxHandler.php",
				{	reportType: $('#reportType :selected').val(), 
					catalogue:	$('#catalogue :selected').val(),
					startDate: 	$('#startDate').val(),
					endDate: 	$('#endDate').val(),
				},
				function(data){ 
					$('#reportResults').html(data);
					$('#reportResults').fadeIn('normal');
				}, 
				"text"
				); 
	});
		
	$().ajaxSend(function(){  
		$("#contentLoading").show();  
	});  
  
	$().ajaxStop(function(){  
		$("#contentLoading").hide();  
	});	
}); 



// JavaScript Document
function switchDate(newDate) {
	
	var startDateInput = document.getElementById('startDate');
	var endDateInput = document.getElementById('endDate');
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