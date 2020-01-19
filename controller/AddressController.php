<?php
#@include ('./autoload.php');

//! Deals with address tasks (create, delete etc)
class AddressController {

	var $mDatabase;

	//! Creates a new address in the database then returns this address as an object of type AddressModel
	/*!
	 * @return Obj:AddressModel - the new address
	 */
	function CreateAddress() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$countryController = new CountryController ( );
		$create_sql = 'INSERT INTO tblAddress (`Company`,`Country_ID`) VALUES (\'\',\'' . $countryController->GetDefault ()->GetCountryId () . '\')';
		if ($this->mDatabase->query ( $create_sql )) {
			$latest_sql = 'SELECT Address_ID FROM tblAddress ORDER BY Address_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $latest_sql )) {
				$error = new Error ( 'Could not select new address.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_address = $result->fetch ( PDO::FETCH_OBJ );
			$newAddress = new AddressModel ( $latest_address->Address_ID );
			return $newAddress;
		} else {
			$error = new Error ( 'Could not create new address.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Searches address based on $searchFor
	/*!
	 * @param $searchFor [in] : String - The postcode to search for
	 * @param $num [in] : Int - The number of results to return (default 5)
	 * @return Array of Obj:AddressModel objects
	 */
	function SearchOnPostcode($searchFor, $num = 5) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT DISTINCT
					tblAddress.Postcode, tblAddress.Address_Line_1, tblAddress.Address_ID,
					tblCustomer.First_Name, tblCustomer.Last_Name, tblCustomer.Email, tblCustomer.Daytime_Telephone
				FROM tblAddress
				INNER JOIN tblOrder
					ON tblAddress.Address_ID = tblOrder.Shipping_Address_ID
				INNER JOIN tblCustomer
					ON tblCustomer.Customer_ID = tblOrder.Customer_ID
				WHERE tblAddress.Postcode LIKE \'' . $searchFor . '%\' ORDER BY tblAddress.Address_ID DESC
				LIMIT ' . $num;
		if ($result = $this->mDatabase->query ( $sql )) {
			$addresses = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the daysOrders variable
			foreach ( $addresses as $address ) {
				$newAddress = new AddressModel ( $address->Address_ID );
				$searchOrders ['address'] [] = $newAddress;
				$searchOrders ['name'] [] = $address->First_Name . ' ' . $address->Last_Name;
				$searchOrders ['email'] [] = trim ( $address->Email );
				$searchOrders ['phone'] [] = trim ( $address->Daytime_Telephone );
			}
			if (0 == count ( $addresses )) {
				$searchOrders = array ();
			}
			return $searchOrders;
		} else {
			$error = new Error ( 'Could not get search addresses with .'.$sql);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End SearchOnPostcode


}
?>