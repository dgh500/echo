<?php

//! Controls tasks to do with baskets - creating, deleting etc.
class BasketController {

	//! Obj:PDO : Database used to access the underlying SQL
	var $mDatabase;

	//! Constructor, initiates the database access
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a basket with the current session ID then returns this basket as Obj:BasketModel If it already exists it will return the existing basket
	/*!
	 * @param `in` basketId - The basket ID to be created, generally the session ID
	 */
	function CreateBasket($basketId) {
		$check_sql = 'SELECT COUNT(Basket_ID) AS BasketCount FROM tblBasket WHERE Basket_ID = \'' . $basketId . '\'';
		$result = $this->mDatabase->query ( $check_sql );
		#$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		$resultObjArr = $result->fetchAll();
		$resultObj = $resultObjArr[0];
		#var_dump($resultObjArr);
		if ($resultObj['BasketCount'] > 0) {
			return new BasketModel ( $basketId );
		} else {
			$create_basket_sql = 'INSERT INTO tblBasket (`Basket_ID`,`Created`,`Total`) VALUES (\'' . $basketId . '\',\'' . time () . '\',\'0\')';
			if ($this->mDatabase->query ( $create_basket_sql )) {
				$newBasket = new BasketModel ( $basketId );
				return $newBasket;
			} else {
				$error = new Error ( 'Could not insert basket: ' . $create_basket_sql );
			#	die(var_dump($this->mDatabase->errorInfo()));
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
	} // End CreateBasket

} // End BasketController


?>