$(document).ready(function() {
	
	$(".monthlyOverview").click(function() {
		
		// Show/Hide content areas
		$("#affiliateAreaDetailsContainer").hide();												 
		$("#affiliateAreaHelpContainer").hide();												 
		$("#affiliateAreaProductBreakdownContainer").hide();	
		$("#affiliateAreaMonthlyOverviewContainer").show();
		
		// Show/Hide menu links
		$("#affiliateMonthlyOverview").show();
		$("#affiliateViewDetails").hide();
		$("#affiliateProductBreakdown").hide();		
		$("#affiliateHelp").hide();
		$("#affiliateLogout").hide();
	});

	$(".viewDetails").click(function() {
		// Show/Hide content areas
		$("#affiliateAreaDetailsContainer").show();												 
		$("#affiliateAreaHelpContainer").hide();												 
		$("#affiliateAreaProductBreakdownContainer").hide();	
		$("#affiliateAreaMonthlyOverviewContainer").hide();
		
		// Show/Hide menu links
		$("#affiliateMonthlyOverview").hide();
		$("#affiliateViewDetails").show();
		$("#affiliateProductBreakdown").hide();		
		$("#affiliateHelp").hide();
		$("#affiliateLogout").hide();
	});

	$(".productBreakdown").click(function() {
		// Show/Hide content areas
		$("#affiliateAreaDetailsContainer").hide();												 
		$("#affiliateAreaHelpContainer").hide();												 
		$("#affiliateAreaProductBreakdownContainer").show();	
		$("#affiliateAreaMonthlyOverviewContainer").hide();
		
		// Show/Hide menu links
		$("#affiliateMonthlyOverview").hide();
		$("#affiliateViewDetails").hide();
		$("#affiliateProductBreakdown").show();		
		$("#affiliateHelp").hide();
		$("#affiliateLogout").hide();
	});

	$(".affiliateHelp").click(function() {
		// Show/Hide content areas
		$("#affiliateAreaDetailsContainer").hide();												 
		$("#affiliateAreaHelpContainer").show();												 
		$("#affiliateAreaProductBreakdownContainer").hide();	
		$("#affiliateAreaMonthlyOverviewContainer").hide();
		
		// Show/Hide menu links
		$("#affiliateMonthlyOverview").hide();
		$("#affiliateViewDetails").hide();
		$("#affiliateProductBreakdown").hide();		
		$("#affiliateHelp").show();
		$("#affiliateLogout").hide();	
	});
	
	$("#affiliateLogoutLink").click(function() {
		$("#affLogoutForm").submit(); 
	});

});