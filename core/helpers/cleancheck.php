<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * SET CLEAN TEXT STRING HELPER
 *---------------------------------------------------------------
 * CLEAN THE TEXT
 *---------------------------------------------------------------
 *
 * This is how we remove all non-alpha characters from a string
 * Use: From anywhere in the script
 * @var text is the string to be cleaned
 * @var space is a bool true means leave spaces, false removes the spaces
 */	
	
	 function CLEAN_TEXT($text, $space = true){
		//Check if space is true, if so leave the space, if not take space away
		if($space){
			$text = preg_replace("/[^a-zA-Z0-9\s]/", "", $text);//remove everything leave the spaces
		}else{
			$text = preg_replace("/[^a-zA-Z0-9]/", "", $text);//remove everything even the spaces			
		}
		//return the new string
		return $text;		
	 }
	 
/**
 *---------------------------------------------------------------
 * CHECK A VALID EMAIL ADDRESS
 *---------------------------------------------------------------
 *
 * This is how we check if an email address is correct
 */	
	
	function _check_email_address($email) {
	  // First, we check that there's one @ symbol, 
	  // and that the lengths are right.
	  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		// Email invalid because wrong number of characters 
		// in one section or wrong number of @ symbols.
		return false;
	  }
	  // Split it into sections to make life easier
	  $email_array = explode("@", $email);
	  $local_array = explode(".", $email_array[0]);
	  for ($i = 0; $i < sizeof($local_array); $i++) {
		if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i])) {
		  return false;
		}
	  }

	  //Check to make sure the domain is in the DNS
	  if(!checkdnsrr($email_array[1],"MX")){return false;}
	  // Check if domain is IP. If not, 
	  // it should be valid domain name
	  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false; // Not enough parts to domain
		}
		for ($i = 0; $i < sizeof($domain_array); $i++) {
		  if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$",$domain_array[$i])) {
			return false;
		  }
		}
	  }
	  return true;
	}

/**
 *---------------------------------------------------------------
 * CHECK A VALID BIRTHDAY
 *---------------------------------------------------------------
 *
 * This is how we check if a birthday is correct
 */	
	function _check_birthday($bday) {
		if (!preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/", $bday)){return false;}
	   return true;
	}


/**
 *---------------------------------------------------------------
 * CREATE RANDOM ACCOUNT CODE
 *---------------------------------------------------------------
 *
 * This is the code that we check to verify email addresses
 */	
	function generateAccountCode($length=9) {
		$vowels = 'aeuyAEUY';
		$consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ23456789';
	 
		$code = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$code .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$code .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $code;
	}