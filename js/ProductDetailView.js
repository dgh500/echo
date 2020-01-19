function toggleProductOverview() {
	var productOverviewOverflow = document.getElementById('productOverviewOverflow');
	var readMoreLink = document.getElementById('readMoreLink');

	if(productOverviewOverflow.style.display != 'none') {
		productOverviewOverflow.style.display = 'none';
		readMoreLink.innerHTML = 'READ FULL DESCRIPTION';
	} else {
		productOverviewOverflow.style.display = 'block';
		readMoreLink.innerHTML = 'HIDE FULL DESCRIPTION';
	}
}

// Document Load...
$(document).ready(function() {

	// Enable zooming
	$('#mainProductImageLink').fancyZoom({directory: 'http://www.echosupplements.com/images',closeOnClick: true});
	$('#mainProductImageLink2').fancyZoom({directory: 'http://www.echosupplements.com/images',closeOnClick: true});
	$('#additionalImageLink0').fancyZoom({directory: 'http://www.echosupplements.com/images',width:350,height:350,closeOnClick: true});
	$('#additionalImageLink1').fancyZoom({directory: 'http://www.echosupplements.com/images',width:350,height:350,closeOnClick: true});
	$('#additionalImageLink2').fancyZoom({directory: 'http://www.echosupplements.com/images',width:350,height:350,closeOnClick: true});
	$('#additionalImageLink3').fancyZoom({directory: 'http://www.echosupplements.com/images',width:350,height:350,closeOnClick: true});

	// Price Match Focus
	$("#priceMatchName").focus(function() {
		$("#priceMatchName").val('');
		$("#priceMatchName").addClass('focused');
	});
	$("#priceMatchPhone").focus(function() {
		$("#priceMatchPhone").val('');
		$("#priceMatchPhone").addClass('focused');
	});
	$("#priceMatchEmail").focus(function() {
		$("#priceMatchEmail").val('');
		$("#priceMatchEmail").addClass('focused');
	});
	$("#priceMatchWebsite").focus(function() {
		$("#priceMatchWebsite").val('');
		$("#priceMatchWebsite").addClass('focused');
	});

	// Add Review Focus
	$("#reviewName").focus(function() {
		$("#reviewName").val('');
		$("#reviewName").addClass('focused');
	});
	$("#reviewText").focus(function() {
		$("#reviewText").val('');
		$("#reviewText").addClass('focused');
	});
	// Add Review Un-Focus
	$("#reviewText").blur(function() {
		if($("#reviewText").val() == '') {
			$("#reviewText").val('Your Review');
			$("#reviewText").removeClass('focused');
		}
	});
	$("#reviewName").blur(function() {
		if($("#reviewName").val() == '') {
			$("#reviewName").val('Your Name');
			$("#reviewName").removeClass('focused');
		}
	});


	// And unfocus
	$("#priceMatchName").blur(function() {
		if($("#priceMatchName").val() == '') {
			$("#priceMatchName").val('your name');
			$("#priceMatchName").removeClass('focused');
		}
	});
	$("#priceMatchPhone").blur(function() {
		if($("#priceMatchPhone").val() == '') {
			$("#priceMatchPhone").val('your phone number');
			$("#priceMatchPhone").removeClass('focused');
		}
	});
	$("#priceMatchEmail").blur(function() {
		if($("#priceMatchEmail").val() == '') {
			$("#priceMatchEmail").val('your email address');
			$("#priceMatchEmail").removeClass('focused');
		}
	});
	$("#priceMatchWebsite").blur(function() {
		if($("#priceMatchWebsite").val() == '') {
			$("#priceMatchWebsite").val('website to beat!');
			$("#priceMatchWebsite").removeClass('focused');
		}
	});

	//if submit button is clicked
	$('#submitPriceMatchButton').click(function () {

		//Get the data from all the fields
		var name 			= $('input[name=priceMatchName]');
		var phone 			= $('input[name=priceMatchPhone]');
		var email 			= $('input[name=priceMatchEmail]');
		var websiteToBeat 	= $('input[name=priceMatchWebsite]');
		var productId 		= $('input[name=productId]');

		//Simple validation to make sure user entered something
		//If error found, add hightlight class to the text field
		if (name.val()=='') {
			name.addClass('hightlight');
			return false;
		} else name.removeClass('hightlight');

		if (phone.val()=='') {
			phone.addClass('hightlight');
			return false;
		} else phone.removeClass('hightlight');

		if (email.val()=='') {
			email.addClass('hightlight');
			return false;
		} else email.removeClass('hightlight');

		if (websiteToBeat.val()=='') {
			websiteToBeat.addClass('hightlight');
			return false;
		} else websiteToBeat.removeClass('hightlight');

		//organize the data properly
		var data = 'priceMatchName=' + encodeURIComponent(name.val()) + '&priceMatchEmail=' + encodeURIComponent(email.val()) + '&priceMatchPhone=' + encodeURIComponent(phone.val()) + '&priceMatchWebsite='  + encodeURIComponent(websiteToBeat.val()) + '&productId=' + encodeURIComponent(productId.val());

		//disabled all the text fields
		$('#priceMatchSection input').attr('disabled','true');

		// hide the price match form
		$('#priceMatchSection form').hide();
		//show the loading sign
		$('#priceMatchLoading').fadeIn('normal');

		//start the ajax
		$.ajax({
			//this is the php file that processes the data and send mail
			url: baseDir + "/formHandlers/PriceMatchHandler.php",

			//GET method is used
			type: "POST",

			//pass the data
			data: data,

			//Do not cache the page
			cache: false,

			//success
			success: function (html) {
				//if process.php returned 1/true (send mail success)
				if (html==1) {
					//hide the form
					$('#priceMatchLoading').fadeOut('slow');

					//show the success message
					$('#priceMatchSuccess').fadeIn('slow');

				//if process.php returned 0/false (send mail failed)
				} else alert('Sorry, unexpected error. Please try again later.');
			}
		});

		//cancel the submit button default behaviours
		return false;
	});

	//if add review button is clicked
	$('#addReviewButton').click(function () {

		//Get the data from all the fields
		var reviewName 		= $('input[name=reviewName]');
		var reviewRating 	= $('input:radio[name=reviewRating]:checked');
		var reviewText 		= $('textarea[name=reviewText]');
		var reviewIP 		= $('input[name=reviewIP]');
		var reviewProduct 	= $('input[name=reviewProduct]');

		// Check if the person has selected a rating
		if(!$("input[@name='reviewRating']:checked").val()) {
			$("#productRatingLabel").addClass('highlight');
			return false;
		} else {
			$("#productRatingLabel").removeClass('highlight');
		}

		//Simple validation to make sure user entered something
		//If error found, add highlight class to the text field
		if (reviewName.val()=='' || reviewName.val()=='ENTER YOUR NAME') {
			reviewName.addClass('highlight');
			reviewName.val('ENTER YOUR NAME');
			return false;
		} else reviewName.removeClass('highlight');

		if (reviewText.val()=='' || reviewText.val()=='Your Review') {
			reviewText.addClass('highlight');
			return false;
		} else reviewText.removeClass('highlight');

		//organize the data properly
		var data = 'reviewName=' + encodeURIComponent(reviewName.val()) + '&reviewRating=' + reviewRating.val() + '&reviewText=' + encodeURIComponent(reviewText.val()) + '&reviewIP='  + encodeURIComponent(reviewIP.val()) + '&reviewProduct=' + encodeURIComponent(reviewProduct.val());

		//disabled all the text fields
		$('#productDetailsReviewSection input').attr('disabled','true');

		// hide the add review form
		$('#productDetailsReviewSection form').hide();
		//show the loading sign
		$('#reviewLoading').fadeIn('normal');

		//start the ajax
		$.ajax({
			//this is the php file that processes the data and send mail
			url: baseDir + "/formHandlers/AddReviewHandler.php",

			//GET method is used
			type: "POST",

			//pass the data
			data: data,

			//Do not cache the page
			cache: false,

			//success
			success: function (html) {
				//if process.php returned 1/true (send mail success)
				if (html==1) {
					//hide the form
					$('#reviewLoading').fadeOut('fast');

					//show the success message
					$('#reviewSuccess').fadeIn('slow');

				//if process.php returned 0/false (send mail failed)
				} else alert('Sorry, review failed - please let us know!');
			}
		});

		//cancel the submit button default behaviours
		return false;
	});

});