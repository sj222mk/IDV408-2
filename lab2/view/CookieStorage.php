<?php

namespace view;

class CookieStorage {
	
	public function save($name, $string) {
		setcookie($name, $string, -1);
	}

	public function load($name) {
		//$ret = isset($_COOKIE["CookieStorage"]) ? $_COOKIE["CookieStorage"] : "";
		
		if (isset($_COOKIE[$name]) && $_COOKIE[$name] != "")
			$ret = $_COOKIE[$name];
		else
			$ret = "";

		return $ret;
		}
	
		public function remove($name) {
			if (isset($_COOKIE[$name]) && $_COOKIE[$name] != "")
				setcookie(self::$cookieName, "", time() -10);
		}
	
	/*public function getClientIdentifier() {
		return $_SERVER["REMOTE_ADDR"];
	}*/
	
}