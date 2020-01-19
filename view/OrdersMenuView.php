<?php
require_once ('../autoload.php');

//! View for the orders tree menu
class OrdersMenuView extends AdminView {

	//! Loads the default view of the page
	/*!
	 * @param $method - undispatched or all
	 * @return String - the code for the page
	 */
	function LoadDefault($method='undispatched') {
		parent::__construct(false,false,false,true);
		$this->IncludeCss('wombat7/dtree/dtree.css', false, false, true );
		$this->IncludeJavascript('wombat7/dtree/dtree.js', false, true );
		$this->mMethod = $method;
		$this->LoadMenu();
		return $this->mPage;
	}

	//! Loads the menu that contains a dated list of all orders
	function LoadMenu() {
		$registry = Registry::getInstance();
		$secureAdminPath = $registry->secureBaseDir.'/wombat7';
		$orderController = new OrderController ( );

		// Start tree
		$this->mPage .= <<<EOT
		<div id="catalogueListContainer">
			<script type="text/javascript">
				d = new dTree('d');
				d.add(0,-1,'Orders','#');
				d.add(1,0,'Search Orders','{$secureAdminPath}/editArea.php?what=searchOrders&id=0','','ordersEdit','{$secureAdminPath}/dtree/img/search.gif','{$secureAdminPath}/dtree/img/search.gif');
//				d.add(2,0,'<del>Add Order</del>','','','ordersEdit','{$secureAdminPath}/dtree/img/fileAdd2.gif','{$secureAdminPath}/dtree/img/fileAdd2.gif');
// d.add(2,0,'Add Order','{$secureAdminPath}/orderEditArea/addOrder/0','','ordersEdit','{$secureAdminPath}/dtree/img/fileAdd2.gif','{$secureAdminPath}/dtree/img/fileAdd2.gif');

				d.add(2,0,'Add Order','{$secureAdminPath}/editArea.php?what=addOrder2&id=0','','ordersEdit','{$secureAdminPath}/dtree/img/fileAdd2.gif','{$secureAdminPath}/dtree/img/fileAdd2.gif');
EOT;

		// Which Method
		if($this->mMethod == 'undispatched') {
			$identifier = 3;
			$parentIdentifier = 0;
			// Get all undispatched orders
			$orders = $orderController->GetUndispatchedOrders();
			$orderList = ''; // Initialise
			// Loop over and print links
			foreach($orders as $order) {
				if ($order->GetStatus ()->IsAuthorised()) {
					$orderList .= <<<EOT
				d.add($identifier,$parentIdentifier,'ECHO{$order->GetOrderId()}','{$secureAdminPath}/editArea.php?what=order&id={$order->GetOrderId()}','','ordersEdit');
EOT;
					$identifier ++;
				}
			}
			$this->mPage .= $orderList;
		} else {
			$numberOfDaysToList = 40;
			$identifier = 3;
			$parentIdentifier = 0;
			$time = mktime ( 23, 59, 59 ); // The time at midnight "today"


			for($i = 0; $i < $numberOfDaysToList; $i ++) {
				switch ($i) {
					case 0 :
						$date = 'Today';
						break;
					case 1 :
						$date = 'Yesterday';
						break;
					default :
						$date = date ( 'd/m/Y', $time );
						break;
				}
				$dayLink = <<<EOT
				d.add($identifier,0,'{$date} [PLACEHOLDER_ORDERCOUNT]','','','ordersEdit','{$secureAdminPath}/dtree/img/folderRightArrow.gif','{$secureAdminPath}/dtree/img/folderDownArrow.gif');
EOT;
				$parentIdentifier = $identifier;
				$identifier ++;
				$daysOrders = $orderController->GetDaysOrders ( $time );
				$orderList = ''; // Initialise
				$daysOrderCount = 0;
				foreach ( $daysOrders as $order ) {
					if ($order->GetStatus ()->IsAuthorised () || $order->GetStatus ()->IsInTransit () || $order->GetStatus ()->IsComplete ()) {
						$orderList .= <<<EOT
					d.add($identifier,$parentIdentifier,'ECHO{$order->GetOrderId()}','{$secureAdminPath}/editArea.php?what=order&id={$order->GetOrderId()}','','ordersEdit');
EOT;
						$identifier ++;
						$daysOrderCount++;
					}
				}

				// Add in date string with count
				$this->mPage .= str_replace('PLACEHOLDER_ORDERCOUNT',$daysOrderCount,$dayLink);

				// Add in orders string
				$this->mPage .= $orderList;

				$time = $time - 86400; // Number of seconds in a day
			}
		} // End if method choice
	$this->mPage .= '
			document.write(d);
			</script>
			</div>';
	} // End function


} // End Class


if(!isset($_GET['method'])) {
	$_GET['method'] = 'undispatched';
}
$page = new OrdersMenuView ( );
echo $page->LoadDefault ($_GET['method']);

?>