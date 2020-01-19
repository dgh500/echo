<?php

//! Performs validation tasks, and always returns a string (or other) with the replacement having been done
class ValidationHelper {

	//! Strips HTML tags
	function RemoveHtml($str) {
		return strip_tags ( $str );
	}

	//! Removes white space from a string
	function RemoveWhitespace($str) {
		return trim ( $str );
	}

	function RemoveAllSpaces($str) {
		return str_replace ( ' ', '', $str );
	}

	//! Makes a string safe for use in a link, incorporates RemoveForeign
	/*!
	 * @param $str : Str - The string to operate on
	 * @param $strip : Bool - If true the characters will be removed rather than made safe
	 * @return Str - The safe link
	 */
	function MakeLinkSafe($str,$strip=false,$lowerCase=true,$utf8=false) {
		$str = trim ( $str );
		$code_entities_match = array (' ', '&quot;', '&amp;', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', '', ';', "'", ',', '.', '_', '/', '*', '+', '~', '`', '=', '---', '--', '--', '’', '™', '–', '”', '’', '‘', '“' );
		if ($strip) {
			$code_entities_replace = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
		} else {
			$code_entities_replace = array ('-', '', 'and', '-', '-', '', '', '', '-', '-', '', '', '', '', '', '', '', '-', '', '', '', '', '', '', '', '', '', '-', '', '-', '-', '', '', '', '', '', '-', '-', '-', '-', '', '', '', '', '', '', '' );
		}
		// Replace the unsafe characters
		$str = str_replace ( $code_entities_match, $code_entities_replace, $str );
		// Remove foreign characters
		$str = $this->RemoveForeign($str);
		// Lowercase if needed
		if($lowerCase) {
			if($utf8) {
				return utf8_encode(strtolower($str));
			} else {
				return strtolower($str);
			}
		} else {
			if($utf8) {
				return utf8_encode($str);
			} else {
				return $str;
			}
		}
	}

	//! Replace foreign characters with english equivalents (Eg. À to A)
	function RemoveForeign($str) {
		$str = strtr ( $str, "ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜİàáâãäåçèéêëìíîïğòóôõöøùúûüıÿÑñ", "AAAAAACEEEEIIIIOOOOOOUUUUYaaaaaaceeeeiiiiooooooouuuuyyNn" );
		return $str;
	}

	//! Remove dodgy characters like TM trademark etc.
	function RemoveNasties($str,$leaveEmails=false) {
		$str = trim ( $str );
		// Email addresses need @, ., _ etc.
		if($leaveEmails) {
			$code_entities_match = array ('&quot;', '&amp;', '!',  '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', '', ';', "'", ',', '/', '*', '+', '~', '`', '=', '---', '--', '--', '’', '™', '–', '”', '’', '‘', '“', '’', '’' );
			$code_entities_replace = array ('', '', '', '', '',  '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',  '', '', '', '', '', '', '', '', '', '' );
		} else {
			$code_entities_match = array ('&quot;', '&amp;', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', '', ';', "'", ',', '.', '_', '/', '*', '+', '~', '`', '=', '---', '--', '--', '’', '™', '–', '”', '’', '‘', '“', '’', '’' );
			$code_entities_replace = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
		}
		$str = str_replace ( $code_entities_match, $code_entities_replace, $str );
		return $str;
	}

	//! Makes mysql SQL safe - replaces ' with \'
	function MakeMysqlSafe($str) {
		return mysql_escape_string($str );
	}

	function OnlyAlphaNumeric($str) {
		return preg_replace("/[^a-zA-Z0-9\s]/","",$str);
	}

	function OnlyNumeric($str) {
		return preg_replace("/[^0-9\s]/","",$str);
	}

	//! Replaces HTML entities, Make SQL safe, Remove whitespace, Remove HTML - Use if need to make sure (eg. AJAX calls)
	function MakeSafe($str) {
		return $this->RemoveHtml ( $this->RemoveWhitespace ( $this->MakeMysqlSafe ( $this->ConvertHtmlEntities ( $str ) ) ) );
	}

	//! Makes a string Javascript safe
	function MakeJsSafe($str) {
		return str_replace ( "'", "\'", str_replace ( ',', '', str_replace ( '(', '', str_replace ( ')', '', str_replace ( '™', '', $str ) ) ) ) );
	}

	//! Make XML safe for processing
	/*!
	 * @param $str : Str - The string to clean
	 * @param $strict : Bool - Whether or not to remove nasty characters (using $this->RemoveNasties)
	 */
	function MakeXmlSafe($str, $strict = false) {
		if ($strict) {
			return htmlspecialchars ( $this->RemoveNasties ( $str ), ENT_QUOTES );
		} else {
			return htmlspecialchars ( $str, ENT_QUOTES );
		}
	}

	//! Convert characters to their HTML entity codes
	function ConvertHtmlEntities($str) {
		return htmlentities ( $str, ENT_QUOTES );
	}

	//! Whether or not a string is numberic
	function IsNumeric($str) {
		return is_numeric ( $str );
	}

	//! Remove '/' and '-' from a string
	function RemoveSlashes($str) {
		return str_replace ( '/', '', str_replace ( '-', '', $str ) );
	}

	//! Whether or not a string contains numbers
	function ContainsNumbers($str) {
		$pattern = '/[0-9]/';
		if (preg_match ( $pattern, $str )) {
			return true;
		} else {
			return false;
		}
	}
} // End ValidationHelper


?>