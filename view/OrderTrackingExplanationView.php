<?php

class OrderTrackingExplanationView extends View {
	
	function LoadDefault() {
		$this->mPage = <<<EOT
			<br />
			<div style="border: 1px solid #CCC; margin-bottom: 10px; margin-top: 10px;">
			<div style="margin: 10px;">
				<strong style="font-size: 12pt; display: block; width: 100%; text-align: center; text-decoration: underline;">Order Status Explanation</strong>
				<strong>Authorised</strong><br/>
				Your order has been successfully taken and is awaiting dispatch.<br/> If there is any delay with your order you will receive an email
				with an estimated dispatch date.<br/> You will also receive an email once your order has been dispatched.<br /><br />
				<strong>Awaiting Authorisation</strong>
				<br/>Your order has NOT been successful and you must <strong>re-order</strong> online or by telephone.<br /><br />
				<strong>Cancelled By Merchant/User</strong>
				<br/>Your order has been cancelled, you will receive an email with full details.<br /><br />
				<strong>Failed</strong>
				<br/>Your order has failed - this is usually a problem with the payment details provided.<br /><br />
				<strong>In Transit</strong>
				<br/>Your order is on it&rsquo;s way! If your order has been sent by recorded delivery or courier you can track it using the tracking number on your invoice.<br /><br />
			</div>
			</div>
EOT;
	return $this->mPage;
	}
}

?>