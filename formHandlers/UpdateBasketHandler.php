<?php
session_start ();
echo session_id ();

require_once ('autoload.php');

foreach ( $_POST as $key => $value ) {
	echo '<strong>' . $key . ':</strong> ' . $value . '<br />';
}

class UpdateBasketHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
	}
	
	function Validate($postArr) {
	}
	
	function UpdateBasket() {
		echo 'update';
	}

}

try {
	$handler = new UpdateBasketHandler ( );
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>