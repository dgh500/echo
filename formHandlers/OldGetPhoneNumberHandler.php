
<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class OldGetPhoneNumberHandler {
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->ConnectToOldDatabase ();
		$this->ConnectToNewDatabase ();
	}
	
	function ConnectToOldDatabase() {
		$host = 'VI-COLO-1758-DE';
		$username = 'deepblue08';
		$password = 'supplement5';
		$dbName = '[deepbluejul07]';
		$this->mOldDatabase = new PDO ( 'mysql:host=' . $host . ';dbname=' . $dbName, $username, $password );
	}
	
	function ConnectToNewDatabase() {
		$host = 'VI-COLO-1758-DE';
		$username = 'deepblue08';
		$password = 'supplement5';
		$dbName = '[deepbluejul08]';
		$this->mNewDatabase = new PDO ( 'mysql:host=' . $host . ';dbname=' . $dbName, $username, $password );
	}
	
	function Process($postArr) {
		echo '<span style="font-family: Arial; font-size: 10pt">';
		$sql = 'SELECT Customer_ID from tblCustomer where Email = \'' . $postArr ['email'] . '\' LIMIT 1';
		$result = $this->mOldDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj) {
			$sql = 'SELECT 
						Telephone_Daytime, 
						Telephone_Evening, 
						Telephone_Mobile 
					FROM 
						tblOrder_Billing_Address 
					WHERE Order_ID in
					(SELECT Order_ID from tblOrder WHERE Customer_ID = ' . $resultObj->Customer_ID . ')';
			$result = $this->mOldDatabase->query ( $sql );
			echo '<h1>All phone numbers for ' . $postArr ['email'] . '</h1>';
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				echo '<strong>Daytime</strong>: ' . $resultObj->Telephone_Daytime . '<br />';
				echo '<strong>Evening</strong>: ' . $resultObj->Telephone_Evening . '<br />';
				echo '<strong>Mobile</strong>: ' . $resultObj->Telephone_Mobile . '<br />';
			}
		} else {
			echo 'Customer does not exist.';
		}
		echo '</span>';
	}
}

try {
	$handler = new OldGetPhoneNumberHandler ( );
	$handler->Process ( $_POST );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>