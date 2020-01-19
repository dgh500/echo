<?php
//! Defines the view when a user clicks 'My Account'
class AccountView extends View {

	var $mSystemSettings;
	var $mSessionHelper;
	var $mCatalogue;

	function __construct($catalogue) {
		// Initialise
		$this->mCatalogue = $catalogue;

		$jsIncludes = array('validateAccountForms.js','validateLoginForm.js');
		#$cssIncludes = array('OrderTrackingView.css.php','AccountView.css.php');
		$cssIncludes = array();

		// Set title and load parent
		parent::__construct($this->mCatalogue->GetDisplayName().' > My Account',$cssIncludes,$jsIncludes);

		// Get system settings, start session
		$this->mSystemSettings 		= new SystemSettingsModel($this->mCatalogue);
		$this->mSessionHelper 		= new SessionHelper();
		$this->mCustomerController 	= new CustomerController();

		// Initialise Customer
		if($this->mSessionHelper->GetCustomer()) {
			$this->mCustomer = new CustomerModel($this->mSessionHelper->GetCustomer(),'id');
		}
	}

	//! Default load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}

	//! Loads the central column
	function LoadMainContentColumn() {

		// If they have requested to go to a page, send them to the correct one depending on whether they are logged in or not
		if(isset($_GET['page'])) {
			if ($this->mSessionHelper->GetCustomer()) {
				switch ($_GET ['page']) {
					case 'track' :
						$this->LoadOrderTracking ();
						break;
					case 'details' :
						$this->LoadMyDetails ();
						break;
					case 'changeDetails' :
						$this->LoadChangeDetails ();
						break;
					case 'changePassword' :
						$this->LoadChangePassword ();
						break;
					case 'passwordFailure' :
						$this->LoadPasswordFailure ();
						break;
					case 'passwordSuccess' :
						$this->LoadPasswordSuccess ();
						break;
					case 'order' :
						$this->LoadOrder ( $_GET ['id'] );
						break;
					case 'receipt' :
						$this->LoadReceipt ( $_GET ['id'] );
						break;
					default :
						$this->LoadLogin ();
						break;
				}
			} else {
				switch ($_GET ['page']) {
					case 'forgottenPassword' :
						$this->LoadForgottenPassword ();
						break;
					case 'passwordReset' :
						$this->LoadPasswordReset ();
						break;
					default :
						$this->LoadLogin ();
						break;
				}
			}
		} else {
			// If nothing has happened yet, show them the right screen for whether they have logged in previously or not
			if ($this->mSessionHelper->GetLoginStage()) {
				switch ($this->mSessionHelper->GetLoginStage()) {
					case 'loginFailure' :
						$this->LoadFailedLogin ();
						break;
					case 'logggedIn' :
						$this->LoadMyAccount ();
						break;
					case 'loggedOut' :
					default :
						$this->LoadLogin ();
						break;
				}
			} else {
				$this->LoadLogin ();
			}
		}
	} // End LoadMainContentColumn()

	//! If they have tried to change their password and failed..
	function LoadPasswordFailure() {
		$this->mPage .= '<h2>Password Change Failure</h2>';
		$this->mPage .= 'Please try again, making sure your old password is correct and your new passwords match -
						if this doesn\'t work please contact us and we will reset your password<br />';
		$this->LoadChangePassword();
	}

	//! If they have successfully changed their password...
	function LoadPasswordSuccess() {
		$this->mPage .= '<h2>Password Changed Successfully</h2>';
		$this->LoadMyAccount ();
	}

	//! If their log in attempt has failed...
	function LoadFailedLogin() {
		$this->mPage .= 'Login failed';
		$this->LoadLogin ();
	}

	//! Once they have reset their password...
	function LoadPasswordReset() {
		$this->mPage .= '<strong>Your password has been reset, please check your email for further instructions!</strong><br /><br /><br />';
		$this->LoadLogin ();
	}

	//! If they have requested the forgotten password form...
	function LoadForgottenPassword() {
		$forgottenPasswordView = new ForgottenPasswordView ( );
		$this->mPage .= $forgottenPasswordView->LoadDefault ( 'account' );
	}

	//! Loads an order placed by the customer using PublicOrderView
	function LoadOrder($id) {
		try {
			$order = new OrderModel ( $id );
		} catch(Exception $e) {
			echo '<img src="http://www.echosupplements.com/images/echoWatermarkLarge.jpg" /><br />';
			echo '<p style="font-family: Arial, Sans-Serif; font-size: 14pt;">Sorry this order does not exist, redirecting you to www.echosupplements.com please wait...</p>';
			echo '<script type="text/javascript">
			<!--
			setTimeout("top.location.href = \'http://www.echosupplements.com\'",4000);
			//-->
			</script>';
			die();
		}
		if($order->GetCustomer()->GetEmail() == $this->mCustomer->GetEmail()) {
			$publicOrderView = new PublicOrderView ( );
			$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> - Order: ECHO'.$order->GetOrderId().'</h1><br />';
			$this->mPage .= $publicOrderView->LoadDefault($id);
		} else {
			$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a></h1><br />';
		}
	}

	//! Loads an order placed by the customer using PublicReceiptView
	function LoadReceipt($id) {
		try {
			$order = new OrderModel ( $id );
		} catch(Exception $e) {
			echo '<img src="http://www.echosupplements.com/images/echoWatermarkLarge.jpg" /><br />';
			echo '<p style="font-family: Arial, Sans-Serif; font-size: 14pt;">Sorry this order does not exist, redirecting you to www.echosupplements.com please wait...</p>';
			echo '<script type="text/javascript">
			<!--
			setTimeout("top.location.href = \'http://www.echosupplements.com\'",4000);
			//-->
			</script>';
			die();
		}
		if($order->GetCustomer()->GetCustomerId() == $this->mCustomer->GetCustomerId() && $order->GetStatus()->IsInTransit()) {
			$publicReceiptView = new PublicReceiptView( );
			$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> - Receipt: ECHO'.$order->GetOrderId().'</h1><br />';
			$this->mPage .= $publicReceiptView->LoadDefault($id);
		} else {
			$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a></h1><br />';
		}
	}

	//! Loads the main 'My Account' Interface
	function LoadMyAccount() {
		$this->mPage .= '<h1 id="accountLogo"><span></span>My Account</h1>';
		$this->mPage .= '<div id="myAccountOptionsContainer">';
		$this->mPage .= '<ul id="myAccountOptionsList">';
		$this->mPage .= '<li id="myAccountOrderTracking">
						<div>
							<a href="' . $this->mBaseDir . '/account/orderTracking"><img src="'.$this->mBaseDir.'/images/myAccountOrderTracking.gif" /></a>
							<p>
							<a href="' . $this->mBaseDir . '/account/orderTracking">Order Tracking</a>
							<br />View all of your previous orders and track any current orders.
							</p>
						</div>
						</li>';
		$this->mPage .= '<li id="myAccountChangeDetails">
						<div>
							<a href="' . $this->mBaseDir . '/account/changeMyDetails"><img src="'.$this->mBaseDir.'/images/myAccountChangeDetails.gif" /></a>
							<p>
							<a href="' . $this->mBaseDir . '/account/changeMyDetails">Change Details</a>
							<br />Change your personal and contact details.
							</p>
						</div>
						</li>';
		$this->mPage .= '<li id="myAccountViewDetails">
						<div>
							<a href="' . $this->mBaseDir . '/account/myDetails"><img src="'.$this->mBaseDir.'/images/myAccountViewDetails.gif" /></a>
							<p>
							<a href="' . $this->mBaseDir . '/account/myDetails">View Details</a>
							<br />View your personal and contact details.
							</p>
						</div>
						</li>';
		$this->mPage .= '<li id="myAccountChangePassword">
						<div>
							<a href="' . $this->mBaseDir . '/account/changeMyPassword"><img src="'.$this->mBaseDir.'/images/myAccountChangeDetails.gif" /></a>
							<p>
							<a href="' . $this->mBaseDir . '/account/changeMyPassword">Change Password</a>
							<br />Change your existing password to a new one.
							</p>
						</div>
						</li>';
		$this->mPage .= '</ul>';
		$this->mPage .= '</div>';
		$this->mPage .= '<br style="clear: both" /><form action="' . $this->mFormHandlersDir . '/LogoutHandler.php" method="post" name="logoutForm" id="logoutForm">';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/logoutButton.png" />';
		$this->mPage .= '<div id="error"></div>';
		$this->mPage .= '</form>';
	}

	//! Loads the order tracking screen showing all of their orders
	function LoadOrderTracking() {
		$this->mCustomer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$orderTrackingExplanationView = new OrderTrackingExplanationView;
		$allOrders = $this->mCustomerController->GetOrders ( $this->mCustomer );
		$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> > Order Tracking</h1>';
		$this->mPage .= $orderTrackingExplanationView->LoadDefault();
		$this->mPage .= '<table id="orderTrackingTable"><tr>';
		$this->mPage .= '<th class="center">Order</th><th class="center">Placed</th><th class="center">Status</th><th class="center">Value</th>
						<th class="center">Tracking No.</th><th class="center">Order Info</th>
						<th class="center">Sales Receipt</th>';
		$this->mPage .= '</tr>';

		// List Orders
		foreach ( $allOrders as $order ) {
			// If the order is in transit then let them print the sales receipt
			if($order->GetStatus()->IsInTransit()) {
				$salesReceiptLink = '<a href="' . $this->mBaseDir . '/account/receipt/' . $order->GetOrderId () . '">RECEIPT</a>';
			} else {
				$salesReceiptLink = 'N/A';
			}

			// Tracking link
			$trackUrl = $order->GetCourier ()->GetTrackingUrl () . $order->GetTrackingNumber ();

			// Build HTML
			$this->mPage .= '<tr>';
			$this->mPage .= '<td class="center">ECHO' . $order->GetOrderId () . '</td>';
			$this->mPage .= '<td class="center">' . $this->mPresentationHelper->DDMMYYYY ( $order->GetCreatedDate () ) . '</td>';
			$this->mPage .= '<td class="left">' . $order->GetStatus ()->GetDescription () . '</td>';
			$this->mPage .= '<td class="right">&pound;' . $this->mPresentationHelper->Money ( $order->GetTotalPrice () ) . '</td>';
			$this->mPage .= '<td class="center"><a href="' . $trackUrl . '" target="_blank">' . $order->GetTrackingNumber () . '</a></td>';
			$this->mPage .= '<td class="center"><a href="' . $this->mBaseDir . '/account/order/' . $order->GetOrderId () . '">ECHO' . $order->GetOrderId () . '</a></td>';
			$this->mPage .= '<td class="center">'.$salesReceiptLink.'</td>';
			$this->mPage .= '</tr>';
		}
		$this->mPage .= '</table><br /><br />';
		$this->mPage .= '<a href="' . $this->mBaseDir . '/account"><img src="' . $this->mBaseDir . '/images/backButton.gif" /></a>';
	}

	//! Generates the <select> dropdown for all titles (mr, mrs, lord...)
	function GenerateTitleSelect() {
		$possibleTitles = array('Mr','Mrs','Miss','Ms','Dr','Prof','Rev','Lord');
		$selectString = '<select name="title" id="title" />';
		foreach($possibleTitles as $title) {
			if(trim($title)==trim($this->mCustomer->GetTitle())) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$selectString .= '<option value="'.$title.'" '.$selected.'>'.$title.'</option>';
		}
		$selectString .= '</select><br />';
		return $selectString;
	}

	//! Load change details form
	function LoadChangeDetails() {
		$this->mCustomer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$selectString = $this->GenerateTitleSelect();
		$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> > Change My Details</h1><br />';
		$this->mPage .= <<<EOT
			<div id="myAccountChangeDetailsContainer">
			<form action="{$this->mFormHandlersDir}/AccountChangeHandler.php" method="post" id="changeMyDetailsForm">
				<input type="hidden" name="customerEmail" id="customerEmail" value="{$this->mCustomer->GetEmail()}" />
				<label for="title">Title: </label>
					{$selectString}
				<label for="firstName">First Name: </label>
					<input type="text" name="firstName" id="firstName" value="{$this->mCustomer->GetFirstName()}" /><br />
				<label for="lastName">Last Name: </label>
					<input type="text" name="lastName" id="lastName" value="{$this->mCustomer->GetLastName()}" /><br />
				<label for="daytimePhone">Daytime Phone: </label>
					<input type="text" name="daytimePhone" id="daytimePhone" value="{$this->mCustomer->GetDaytimeTelephone()}" /><br />
				<label for="mobilePhone">Mobile Phone: </label>
					<input type="text" name="mobilePhone" id="mobilePhone" value="{$this->mCustomer->GetMobileTelephone()}" /><br />
				<input type="submit" value="Save Changes" />
			</form>
			</div>
EOT;
	}

	//! Load change password form
	function LoadChangePassword() {
		$this->mCustomer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> > Change My Password</h1><br />';
		$this->mPage .= <<<EOT
		<div id="myAccountChangePasswordContainer"><div>
			<form action="{$this->mFormHandlersDir}/AccountChangePasswordHandler.php" method="post" id="changeMyPasswordForm" onsubmit="return validateChangePasswordForm(this)">
				<input type="hidden" name="customerEmail" id="customerEmail" value="{$this->mCustomer->GetEmail()}" />
				<label for="newPassword1">New Password: </label>
					<input type="password" name="newPassword1" id="newPassword1" /><br />
				<label for="newPassword2">New Password (again): </label>
					<input type="password" name="newPassword2" id="newPassword2" /><br />
				<label for="oldPassword">Old Password: </label>
					<input type="password" name="oldPassword" id="oldPassword" /><br />
				<input type="submit" value="Change Password" />
			</form>
		</div></div>
			<div id="error"></div>
EOT;
	}

	//! Load view details form
	function LoadMyDetails() {
		$this->mCustomer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$this->mPage .= '<h1><a href="' . $this->mBaseDir . '/account">My Account</a> > My Details</h1><br />';
		$this->mPage .= '<div id="myAccountViewDetailsContainer"><div>';
		$this->mPage .= '<strong>Name: </strong>' . $this->mCustomer->GetTitle () . ' ' . $this->mCustomer->GetFirstName () . ' ' . $this->mCustomer->GetLastName () . '<br />';
		$this->mPage .= '<strong>Email Address: </strong>' . $this->mCustomer->GetEmail () . '<br />';
		$this->mPage .= '<strong>Daytime Phone Number: </strong>' . $this->mCustomer->GetDaytimeTelephone () . '<br />';
		$this->mPage .= '<strong>Mobile Phone Number: </strong>' . $this->mCustomer->GetMobileTelephone () . '<br />';
		$this->mPage .= '<br /><a href="' . $this->mBaseDir . '/account"><img src="' . $this->mBaseDir . '/images/backButton.gif" /></a>';
		$this->mPage .= '</div></div>';
	}

	function LoadLogin() {
		$this->mPage .= '<div id="loginTitle"><h2>Account Login</h2></div>';
		$this->mPage .= '<form action="' . $this->mFormHandlersDir . '/LoginHandler.php" method="post" name="loginForm" id="loginForm" onsubmit="return validateLoginForm(this)">';
		$this->mPage .= '<label for="loginEmail">Email:</label><input type="text" name="loginEmail" id="loginEmail" /><br />';
		$this->mPage .= '<br style="clear: both" /><label for="loginPassword">Password:</label><input type="password" name="loginPassword" id="loginPassword" />';
		$this->mPage .= '<br style="clear: both" /><input type="image" src="' . $this->mBaseDir . '/images/loginButton.png" id="loginButton" /><br />';
		$this->mPage .= '<div id="error"></div>';
		$this->mPage .= '</form>';
		$this->mPage .= '<form action="' . $this->mBaseDir . '/formHandlers/AccountForgotPasswordHandler.php" method="post" style="width: 794px; margin-top: 10px; text-align: center;">';
		$this->mPage .= '<input type="submit" value="Forgotten Your Password?" id="forgotPasswordSubmit" />';
		$this->mPage .= '</form>';
	}

} // End AccountView

?>