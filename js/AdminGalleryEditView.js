$(document).ready(function() {
					
	// This makes the details tab focussed on page load
	$("#adminGalleryViewTabContainer-details").css({backgroundPosition: "0 -150px", borderWidth: "0px", borderBottom: "1px solid #FFFFFF"});
	$("#detailsLink").css({backgroundPosition: "100% -150px"});
	
	// Declare the tabs for this page
	var tabsArray = new Array();
	tabsArray[0] = new Array('details','galleryDetails');
	tabsArray[1] = new Array('items','galleryItems');
	
	// When clicking a link, switch to the respective tab
	$("#detailsLink").click(function() {
		showTab(tabsArray,'details','adminGalleryViewTabContainer');
	 });

	$("#itemsLink").click(function() {
		showTab(tabsArray,'items','adminGalleryViewTabContainer');
	 });
	
	$("#light").click(function() {
		$("#galleryNavTheme").val('light');			 
	});

	$("#dark").click(function() {
		$("#galleryNavTheme").val('dark');			 
	});
	
	// Edit gallery item
	$(".editGalleryButton").click(function() {
		
		// Fill in the form
		var galleryItemId = $(this).attr('id');
		var caption	= $("#galleryCaption" + galleryItemId).val();
		
		$("#newGalleryItemDescription").val(caption);
		$("#newGalleryGalleryItemId").val(galleryItemId);
		
		$("#editGalleryContainer").dialog({
			 autoOpen: false,
			 bgiframe: true,
			 title: 'Edit Gallery Item',
			 modal: true,
			 width: 450,
			 height: 350,
			 buttons: { 
				"OK": function() {
					$.ajax({
							type: "POST",
							url: BASE_DIRECTORY + "/ajaxHandlers/AdminGalleryItemEditAjaxHandler.php",
							data: 'newGalleryGalleryItemId=' + $("#newGalleryGalleryItemId").val() + '&newGalleryItemDescription=' + $("#newGalleryItemDescription").val(),
							async: false,
							success: function(data, textStatus) {
								// Refresh the list
								$("#galleryItems").html(data);
							}					
						});			
						
						$(this).dialog("close"); $(this).dialog("destroy"); 
					}, 
				"Cancel": function() { 
					$(this).dialog("close"); $(this).dialog("destroy"); 
				} 
			 },
		});
		$('#editGalleryContainer').dialog("open");
	});

	// Delete gallery item
	$(".deleteGalleryButton").click(function() {
		var galleryItemId = $(this).attr('id');								 
		var galleryId = $('#galleryId').val();
		jConfirm('Are you sure you want to delete this?', 'Delete Gallery Item?', function(result) {
			if(result) {
				$.ajax({
						type: "POST",
						url: BASE_DIRECTORY + "/ajaxHandlers/AdminGalleryItemDeleteAjaxHandler.php",
						data: 'galleryItemId=' + galleryItemId + '&galleryId=' + galleryId,
						async: false,
						success: function(data, textStatus) {
							jAlert('Gallery Item Deleted', 'Delete Gallery Item?');
							// Refresh the list
							$("#galleryItems").html(data);
						},
						failure: function(data, textStatus) {
							alert('failure');
						}						
					});			
			} else {
	    		jAlert('Not Deleted', 'Delete Gallery Item?');				
			}
			return false;
		});
	}); // End delete gallery item

}); // End document ready

