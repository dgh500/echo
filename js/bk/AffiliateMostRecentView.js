$(document).ready(function() {
	
	$("#showProducts").toggle(
	function() {
		$(".productRow").show();
		$("#showProducts").text('Hide Product Details');		
	},
	function() {
		$(".productRow").hide();
		$("#showProducts").text('Show Product Details');		
	});
	
});