<?php
// Settings
include('../autoload.php');

if(isset($_REQUEST['ajaxRequest'])) {

	switch($_REQUEST['ajaxRequest']) {
		case 'PENDINGREVIEWDATA':
			$review = new ReviewModel($_REQUEST['reviewId']);
			echo str_replace(',','&#44;',$review->GetName()).','.$review->GetRating().','.$review->GetIPAddress().','.str_replace(',','&#44;',$review->GetText());
		break;
		case 'APPROVEREVIEW':
			$review = new ReviewModel($_REQUEST['pendingReviewIdP']);
			$review->SetName($_REQUEST['reviewName']);
			$review->SetRating($_REQUEST['reviewRating']);
			$review->SetIPAddress($_REQUEST['reviewIP']);
			$review->SetText($_REQUEST['reviewText']);
			if($review->SetApproved(1)) {
				echo 'SUCCESS';
			} else {
				echo 'FAILURE APPROVING REVIEW';
			}
		break;
	} // End switch

} // End if ajax request


?>