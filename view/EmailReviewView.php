<?php

class EmailReviewView extends View {

	var $mOrder;

	function LoadDefault($orderId) {
		$this->mOrderItemController = new OrderItemController;
		$this->mOrder = new OrderModel($orderId);
		$this->mTimeHelper = new TimeHelper();
		$this->mPresentationHelper = new PresentationHelper();
		$this->mOrderItemController = new OrderItemController;
		$plHelper = new PublicLayoutHelper;
		$this->mMediumImageDir = $this->mRegistry->mediumImageDir;
		$customerName = $this->mOrder->GetCustomer()->GetFirstName();
		$this->mPage .= '<html>
<head>
<style type="text/css"> <!--
body {
	font-family: Arial, Helvetica, sans-serif;
}
h1 {
	font-size: 16pt;
}
strong {
	font-size: 12pt;
}
p {
	font-size: 10pt;
}
--> </style>
</head>
<body>
<center>
<table width="572" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td colspan="3"><a href="http://www.echosupplements.com"><img src="http://www.echosupplements.com/images/emailReviewHeader.jpg" width="572" height="64" border="0" /></a></td>
</tr>
<tr>
	<td width="2" bgcolor="#000000"></td>
    <td width="567" align="center"> <br>

        <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center"><h1>Hi '.ucfirst(strtolower($customerName)).',<br>Thank you for your order from Echo Supplements</h1>
    <p>We hope that you are enjoying your purchase and would love it if you could find the time to write
a short review of your experience with it.</p><p>To do this simply click the \'Review Now\' button next to the product and write your review which will help other customers.</p>
    <table width="538" cellspacing="0" cellpadding="0" border="0">         <tr>
            <td height="2" colspan="2" align="center"><img src="http://www.echosupplements.com/images/emailReviewHr.jpg" width="557" height="3"></td>
            </tr>
			 <tr>
		            <td colspan="2" align="center"><img src="http://www.echosupplements.com/images/emailReviewHrHalf.jpg" width="557" height="3"></td>
		            </tr>';
	$productCount = 0; // If this is zero at the end of the loop then the products in the order don't exist any more
	$alreadyListed = array(); // Looks crap with the same prouct listed X times
	foreach($this->mOrderItemController->GetProductsForOrder($this->mOrder) as $productItem) {
		// Can only do this for products that still exist (obviously) - so use the Sage Code to make a product
		if($productItem->GetSageCode() != '') {
			$product = $this->mOrderItemController->GetProductForOrderItem($productItem);
	        if($product && !in_array($product->GetProductId(),$alreadyListed)) {
				$brand = $product->GetManufacturer()->GetDisplayName();
				$href = $plHelper->LoadLinkHref($product).'#addReview';
				$this->mPage .= '
				<tr>
		            <td width="150" height="170" align="center">
						<img src="'.$this->mBaseDir.'/'.$this->mMediumImageDir.$product->GetMainImage()->GetFilename().'" />
					</td>
		            <td align="left" valign="middle">
		            	<p><strong>'.$product->GetDisplayName().'</strong>
		                <em><br>
		                Bought on '.date('jS F Y',$this->mOrder->GetCreatedDate()).'</em><br>
		                '.$brand.'</p>
						<a href="'.$href.'">
		                	<img src="http://www.echosupplements.com/images/emailReviewReviewButton.jpg" width="100" height="30" border="0">
						</a>
						</td>
		            </tr>
		        <tr>
		            <td colspan="2" align="center"><img src="http://www.echosupplements.com/images/emailReviewHrHalf.jpg" width="557" height="3"></td>
		            </tr>';
				$productCount ++;
			$alreadyListed[] = $product->GetProductId();
			}
		}
	} // End foreach
	$this->mPage .= '
        <tr>
            <td height="2" colspan="2" align="center"><img src="http://www.echosupplements.com/images/emailReviewHr.jpg" width="557" height="3"></td>
            </tr>
        <tr>
            <td colspan="2" align="center"><strong><br>
                Thank You For Your Time!<br>
                    </strong><em>Echo Supplements</em>
				<br />
				<span style="font-size: 8pt">You are receiving this email as you have ordered from ourselves (Order: ECHO'.$this->mOrder->GetOrderId().') recently. If you would prefer not
				to received these emails then please just reply to this email letting us know and we will remove your email address from this list immediately.</span>
				</td>
            </tr>
    </table></td>
            </tr>
        </table>

   </td>
    <td width="2" bgcolor="#000000"></td>
    </tr>
    <tr>
    	<td colspan="3">
        	<img src="http://www.echosupplements.com/images/emailReviewFooter.jpg" width="572" height="64" border="0" />
        </td>
    </tr>
</table>
</center>
</body>
</html>';
		if($productCount == 0) {
			return false;
		} else {
			return $this->mPage;
		}
	}


} // End EmailReviewView


?>