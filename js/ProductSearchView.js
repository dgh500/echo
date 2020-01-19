$(function() {
	$("#q").suggest("searchList.php",{
		onSelect: function() {
			$("#productSearchForm").submit();
		}
	});
});