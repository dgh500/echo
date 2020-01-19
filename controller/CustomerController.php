<?php

//! Deals with tasks to do with customers (creating etc.)
class CustomerController {

	//! Database access PDO
	var $mDatabase;

	//! Initialises the database
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a customer
	/*!
	 * @return Obj:CustomerModel - The new customer / An exception if an error occurs
	 */
	function CreateCustomer() {
		$sql = 'INSERT INTO tblCustomer (`First_Name`,`Last_Name`,`Password`,`Email`,`Daytime_Telephone`,`Mobile_Phone`,`Title`)
			VALUES (\'\',\'\',\'\',\'\',\'\',\'\',\'\')';
		if ($result = $this->mDatabase->query ( $sql )) {
			$get_latest_sql = 'SELECT Customer_ID FROM tblCustomer ORDER BY Customer_ID DESC LIMIT 1';
			if ($result = $this->mDatabase->query ( $get_latest_sql )) {
				$resultObj = $result->fetchObject ();
				return new CustomerModel ( $resultObj->Customer_ID, 'id' );
			} else {
				$error = new Error ( 'Could not select the customer just created.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not create a customer.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Returns an array of all customers as CustomerModel
	function GetAllCustomers() {
		$sql = 'SELECT DISTINCT Customer_ID FROM tblCustomer WHERE Email <> \'\' ORDER BY Customer_ID DESC LIMIT 1000';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all customers.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$retArr = array();
		$resultObjArr = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $resultObjArr as $resultObj ) {
			$newCustomer = new CustomerModel ( $resultObj->Customer_ID,'id' );
			$retArr [] = $newCustomer;
		}
		return $retArr;
	}

	//! Returns an array of all customers as CustomerModel that have bought something from the brand supplied
	/*!
	 * @param [in] $brand - ManufacturerModel
	 */
	function GetAllCustomersByBrand($brand,$debug=false) {
		$sql = '
				SELECT
				DISTINCT
					tblCustomer.Customer_ID,
					tblOrder.Order_ID
				FROM
					tblCustomer
				INNER JOIN tblOrder ON
					tblCustomer.Customer_ID = tblOrder.Customer_ID
				INNER JOIN tblOrder_Items ON
					tblOrder.Order_ID = tblOrder_Items.Order_ID
				INNER JOIN tblSku ON
					tblOrder_Items.Sage_Code = tblSku.Sage_Code
				INNER JOIN tblProduct_SKUs ON
					tblSku.SKU_ID = tblProduct_SKUs.SKU_ID
				INNER JOIN tblProduct ON
					tblProduct.Product_ID = tblProduct_SKUs.Product_ID
				WHERE
					Email <> \'\'
				AND
					tblProduct.Manufacturer_ID = '.$brand->GetManufacturerId().'
					';#echo $sql;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all customers for brand: '.$brand->GetDisplayName().'.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$retArr = array();
		$orderList = '';
		$resultObjArr = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $resultObjArr as $resultObj ) {
			$orderList .= $resultObj->Order_ID.' - ';
			$newCustomer = new CustomerModel ( $resultObj->Customer_ID,'id' );
			$retArr [] = $newCustomer;
		}
		if($debug) {
			return $orderList;
		} else {
			return $retArr;
		}
	}// End GetAllCustomersByBrand

	//! Returns an array of all customers as CustomerModel that have bought the product supplied
	/*!
	 * @param [in] $product - ProductModel
	 */
	function GetAllCustomersByProduct($product,$debug=false) {
		$sql = '
				SELECT
				DISTINCT
					tblCustomer.Customer_ID,
					tblOrder.Order_ID
				FROM
					tblCustomer
				INNER JOIN tblOrder ON
					tblCustomer.Customer_ID = tblOrder.Customer_ID
				INNER JOIN tblOrder_Items ON
					tblOrder.Order_ID = tblOrder_Items.Order_ID
				INNER JOIN tblSku ON
					tblOrder_Items.Sage_Code = tblSku.Sage_Code
				INNER JOIN tblProduct_SKUs ON
					tblSku.SKU_ID = tblProduct_SKUs.SKU_ID
				INNER JOIN tblProduct ON
					tblProduct.Product_ID = tblProduct_SKUs.Product_ID
				WHERE
					Email <> \'\'
				AND
					tblProduct.Product_ID = '.$product->GetProductId().'
					';#echo $sql;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all customers for product: '.$product->GetDisplayName().'.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$retArr = array();
		$orderList = '';
		$resultObjArr = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $resultObjArr as $resultObj ) {
			$orderList .= $resultObj->Order_ID.' - ';
			$newCustomer = new CustomerModel ( $resultObj->Customer_ID,'id' );
			$retArr [] = $newCustomer;
		}
		if($debug) {
			return $orderList;
		} else {
			return $retArr;
		}
	}// End GetAllCustomersByProduct

	//! Whether a customer exists
	/*!
	 * @return Boolean
	 */
	function CustomerAlreadyExists($emailAddress) {
		$sql = 'SELECT COUNT(Customer_ID) AS CustomerCount FROM tblCustomer WHERE Email = \'' . $emailAddress . '\'';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->CustomerCount > 0) {
				if($this->CustomerHasAPassword($emailAddress)) {
					return true;
				} else {
					// Treat them as new if they have only done phone orders before
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//! Whether the customer has a password (they won't if only made phone orders before)
	function CustomerHasAPassword($emailAddress) {
		$customer = new CustomerModel($emailAddress);
		if(trim($customer->GetPassword()) == '') {
			return false;
		} else {
			return true;
		}
	}

	//! Logs in a customer
	/*!
	 * @return Boolean - true if the customer details are OK
	 */
	function Login($customer, $password) {
		$sql = 'SELECT COUNT(Customer_ID) AS CustomerCount FROM tblCustomer WHERE Email = \'' . $customer->GetEmail () . '\' AND Password = \'' . sha1 ( $password ) . '\'';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->CustomerCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Get all the orders a customer has placed
	/*!
	 * @param $customer [in] Obj:CustomerModel - the customer to check
	 * @param $includePhoneOrders [in] Boolean - Def. True - Whether to include phone orders or not
	 * @return Array of Obj:OrderModel objects - the orders the customer has placed
	 */
	function GetOrders($customer,$includePhoneOrders=true) {
		// Initialise ID list
		$customerIdList = $customer->GetCustomerId().', ';

		// Add any phone orders to the list
		if($includePhoneOrders) {
			$sql = 'SELECT Customer_ID FROM tblCustomer WHERE Email = \''.$customer->GetEmail().'\'';
			$result = $this->mDatabase->query($sql);
			while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
				$customerIdList .= $resultObj->Customer_ID.', ';
			}
		}
		// Take off the comma space
		$customerIdList = substr($customerIdList,0,strlen($customerIdList)-2);

		// Get the Orders
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Customer_ID IN ('.$customerIdList.') AND Status_ID IN (2,3,4,5,6) ORDER BY Order_ID DESC';
		$result = $this->mDatabase->query ( $sql );
		$ordersArr = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$ordersArr [] = new OrderModel ( $resultObj->Order_ID );
		}
		return $ordersArr;
	}

}

?>