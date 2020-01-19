<?php

//! This class contains a few methods that make password generation/checking easier.
/*
 * Usage: 
 $pass = new PasswordHelper;
 $salt = $pass->GenerateSalt()
 $hash = $pass->GeneratePasswordHash($salt,'password');
 // Then add $hash to the database.
 if($pass->CheckPassword('password',$hash)) ... // Assuming $hash is from the database and 'password' from user input
}

 */
class PasswordHelper {
	
	//! Generate 'salt' to make cracking more than one password a pain
	/*!
	 * @return String - the salt
	 */
	function GenerateSalt() {
		return substr ( str_pad ( dechex ( mt_rand () ), 8, '0', STR_PAD_LEFT ), - 8 );
	} // End GenerateSalt
	

	//! Generates a password hash for storing (eg. in database)
	/*
	 * @param password - the password supplied by a customer 
	 * @return String - the password to be stored
	 */
	function GeneratePasswordHash($salt, $password) {
		return $salt . hash ( 'whirlpool', $salt . $password );
	} // End GeneratePasswordHash
	

	//! Check a user supplied password against the stored hash
	/*!
	 * @param password - password supplied by the user
	 * @param hash - the password that was stored to check against
	 * @return Boolean - True if password is correct
	 */
	function CheckPassword($password, $hash) {
		$salt = substr ( $hash, 0, 8 );
		return $hash === $this->GeneratePasswordHash ( $salt, $password );
	} // End CheckPassword	
} // End PasswordHelper 


?>