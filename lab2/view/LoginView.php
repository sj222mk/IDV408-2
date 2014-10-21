<?php

require_once("CookieStorage.php");

class LoginView {
	private $model;
	private $cookies;
	private $message;
	private $errorMessage = "";
	
	public function __construct(LoginModel $model) {
		$this->model = $model;
		$this->cookies = new \view\CookieStorage();
	}

	public function didUserPressLogin() {
		
		if (isset($_POST["login"])){
			if($_POST["UserID"] != "" && $_POST["PasswordID"] != ""){
				return true;
			}
			else{
				if($_POST["UserID"] === ""){
					$this->errorMessage = 'Användarnamn saknas';
				}
				else{ 
					$this->errorMessage = 'Lösenord saknas';
				}
			$this->cookies->save("mess", $this->errorMessage);
			}
		return false;
		}
	}
	
	public function getUserData(){
		$data = array("user" => $_POST["UserID"], "pw" => $_POST["PasswordID"]);	
		return $data;
	}
	
	//Kontroll av mest aktuellt felmeddelande (vissa sätts av controllern, vissa av vyn - om de saknas kollas cookies!)
	public function setNewestErrorMessage($message){
		$userMessage = "";
		$mess = "mess";
		
		if($message != ""){
			$userMessage = $message;
		}
		elseif($this->errorMessage != ""){
			$userMessage = $this->errorMessage;	
		}
		else{
			$userMessage = $this->cookies->load($mess); 
		}
		
		$this->cookies->save($mess, $userMessage);
		return $userMessage;
	}
	
	public function showLogin($message) {
		$this->errorMessage = $this->setNewestErrorMessage($message);
		
		$ret = "<fieldset>
			<form method='post'>
			<legend>Login - Skriv in användarnamn och lösenord</legend>
			<p>$this->errorMessage</p> 
			<label for='UserID'>Lösenord :</label>
			<input id='UserID' name='UserID' type='text' value=''>
			<label for='PasswordID'>Lösenord :</label>
			<input id='PasswordID' name='PasswordID' type='password' value=''>
			<label for='AutologinID'>Håll mig inloggad :</label>
			<input id='AutologinID' type='checkbox'>
			<button type='submit'name='login'>Logga in</button>
			</form>
			</fieldset>";
		
		return $ret;
		}
}