
// Start Action
$(document).ready(function() {

/*	$("#topRightNav").corner("5px bl");
	$("#searchBar").corner("5px");

	$(".sectionBody").corner("5px bl br keep");
	$(".sectionHeader").corner("5px tl tr keep");*/

	// Newsletter focus
	$("#signUpEmail").focus(function() {
		$("#signUpEmail").val('');
			$("#signUpEmail").addClass('focused');
	});
	// And unfocus
	$("#signUpEmail").blur(function() {
		if($("#signUpEmail").val() == '') {
			$("#signUpEmail").val('email address');
			$("#signUpEmail").removeClass('focused');
		}
	});

	// Product Search
	$(function() {
		$("#q").suggest(baseDir + "/searchList.php",{
			onSelect: function() {
				$("#productSearchForm").submit();
		}});
	});


}); // End doc ready