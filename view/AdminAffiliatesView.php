<?php

//! Defines the view for the missing section of the admin area
class AdminAffiliates extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuAffiliates';
	
	function __construct() {
		parent::__construct('Admin > Affiliates',false,false,false);	
		$this->IncludeCss('AdminAffiliatesView.css.php');
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault($aff = false) {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->InitialiseDisplay ();
			$this->InitialiseContentDisplay ();
			if (! $aff) {
				$this->LoadAffiliatesDisplay ();
			} else {
				$this->LoadAffiliate ( $aff );
			}
			$this->CloseContentDisplay ();
			$this->CloseDisplay ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminAffiliatesViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminAffiliatesViewContentContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeadView = new AdminHeadView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeadView->LoadDefault ( ' > Affiliates' );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}
	
	function LoadAffiliate($affId) {
		$affiliate = new AffiliateModel ( $affId );
		$orderController = new OrderController ( );
		$this->mPage .= '<h3><a href="' . $this->mBaseDir . '/admin/affiliates">Affiliates</a> > ' . $affiliate->GetName () . '(' . $affiliate->GetAffiliateId () . ')</h3>';
		$this->mPage .= '<strong>Email</strong>: ' . $affiliate->GetEmail () . '<br />';
		$this->mPage .= '<strong>URL</strong>: ' . $affiliate->GetUrl () . '<br />';
		$this->mPage .= '<strong>Telephone</strong>: ' . $affiliate->GetTelephone () . '<br />';
		$this->mPage .= '<strong>Last Claim</strong>: ' . date('l jS \of F Y',$affiliate->GetLastClaim()) . '<br />';
		$this->mPage .= '<strong>Claimed</strong>: &pound;' . $affiliate->GetClaimedAmount () . '<br /><br />';
		$this->mPage .= <<<EOT
		<table>
			<tr>
				<th>Order ID</th>
				<th>Date</th>
				<th>Total Price</th>
				<th></th>
			</tr>
EOT;
		$i = 0;
		foreach ( $orderController->GetAffiliatesOrders ( $affiliate ) as $order ) {
			if ($i % 2) {
				$rowClass = 'tr1';
			} else {
				$rowClass = 'tr2';
			}
			$orderedDate = date ( 'r', $order->GetCreatedDate () );
			$this->mPage .= <<<EOT
			<tr class="{$rowClass}">
				<td><a href="https://www.deepbluedive.com/admin/orders/{$order->GetOrderId()}">ECHO{$order->GetOrderId()}</a></td>
				<td>{$orderedDate}</td>
				<td>&pound;{$this->mPresentationHelper->Money($order->GetTotalPrice())}</td>
				<td></td>
			</tr>
EOT;
			$i ++;
		}
		$this->mPage .= <<<EOT
		</table>
EOT;
	}
	
	function LoadAffiliatesDisplay() {
		$affiliateController = new AffiliateController ( );
		$allAffiliates = $affiliateController->GetAll ();
		$this->mPage .= <<<EOT
		<table>
			<tr>
				<th>Affiliate Name</th>
				<th>Total Spend</th>
				<th>Since Last Claim</th>
				<th>Order Count</th>
			</tr>
EOT;
		$i = 0;
		foreach ( $allAffiliates as $affiliate ) {
			if ($i % 2) {
				$rowClass = 'tr1';
			} else {
				$rowClass = 'tr2';
			}
			$this->mPage .= <<<EOT
			<tr class="{$rowClass}">
				<td><a href="http://www.deepbluedive.com/admin/affiliates/{$affiliate->GetAffiliateId()}">{$affiliate->GetName()}</a> </td>
				<td>&pound; {$this->mPresentationHelper->Money($affiliateController->GetTotalSpend($affiliate))}</td>
				<td>&pound; {$this->mPresentationHelper->Money($affiliateController->GetTotalSinceLastClaim($affiliate))}</td>
				<td>{$affiliateController->OrderCount($affiliate)}</td>
			</tr>
EOT;
			$i ++;
		}
		$this->mPage .= <<<EOT
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
EOT;
	}
}
set_time_limit ( 300 );
$page = new AdminAffiliates ( );
$page->IncludeCss ( 'admin.css.php' );
$page->IncludeCss ( 'adminForms.css.php' );

if (! isset ( $_GET ['id'] )) {
	echo $page->LoadDefault ();
} else {
	echo $page->LoadDefault ( $_GET ['id'] );
}

?>