<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#adminHeaderLogo, #adminMenuOrders, #adminMenuHeader, #ordersMenu, #staffNotes, #catalogueListContainer, .dtree {
	display: none !important; 
}
html {
	margin: 0px;
	padding: 0px;
	display: block;
}

#reportChoice,#reportDateRange,#generateReport {
	display: none;
}

.printLineBreak {
	clear: both;
	display: block;
}

#adminMissingViewContentContainer,#reportResults {
	border: 0px;
}

#reportResults,#adminReportsViewContentContainer {
	overflow: visible;
	border: 0px;
    font-size: 14pt !important;    
}
.screenOnly {
	display: none;	
}