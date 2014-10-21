<?php

//require_once("CookieStorage.php");

class LogoutView {
	private $model;
	private $messages;
	
	public function __construct(LoginModel $model) {
	$this->model = $model;
	$this->messages = new \view\CookieStorage();
	}

	public function didUserPressLogout() {
		if (isset($_POST["login"]))
			return true;
		
		return false;
	}
	
	
	public function showLogout($textMessage) {
		
		$ret = "<header>
					<h2>Admin är inloggad<h2>
				</header>
				<p>$textMessage</p>
				<form action='' method='post'>
				<input type='submit' value='Logga ut!' name='logout'/>
				</form>";
	
		
		
		return $ret;
		}
}