<?php

require_once("model/LoginModel.php");
require_once("view/LoginView.php");
require_once("view/LogoutView.php");
require_once("view/CookieStorage.php"); 

class LoginController {
	private $view;
	private $model;
	private $storage;
	private $isLoggedIn = FALSE;
	private $textMessage = "";
	private $savedSession;
	
	//Meddelanden till användaren efter validering och händelser
	private static $messageCookie = "Message";
	private static $outlogMessage = "Du har nu loggat ut";
	private static $cookieMessage = "Inloggning via cookie";
	private static $notCookieMessage = "Felaktig information i cookie";
	private static $notValidMessage = "Felaktigt användarnamn eller lösenord";
	private static $savedSessionMessage = "Inloggning lyckades och vi kommer ihåg dig nästa gång";
	private static $validUserMessage = "Inloggning lyckades";
	
	public function __construct() {		
		$this->model = new \model\LoginModel();
		$this->cookies = new \view\CookieStorage();
		$this->loginView = new \view\LoginView($this->cookies);
		$this->logoutView = new \view\LogoutView($this->cookies);
	}

	public function doLogin() {
		$userData = "";
		$clientSession;
		$userCookieExist;
		$userValid;
		
		if($this->isLoggedIn === TRUE){
			return $this->logoutView->showLogout($this->textMessage);
			$this->doLogout();	
		}
		
		//$this->isLoggedIn = FALSE;
		
		//Kontrollera om användaren är inloggad
		$userCookieExist = $this->cookies->checkUserCookie();
		if($userCookieExist){
			$userData = $this->cookies->loadUserFromCookie();
			if(!$userData === false){
				$clientSession = $this->model->doesClientExist($userData);
				if($clientSession){
					$this->textMessage = self::$cookieMessage;
				}
				else{
					$this->textMessage = self::$notCookieMessage;
				}
			}
			else{
				$this->textMessage = self::$notCookieMessage;
			}	
		}
		
		while($this->isLoggedIn === FALSE){
		
		//Hantera indata
		if ($this->loginView->didUserPressLogin()) {
			$userData = $this->loginView->getUserData();
			if($userData === false){
				$this->textMessage = self::notValidMessage;
			}
		}
		
		//Validerar användaruppgifter	
		$userValid = $this->model->checkUser($userData);
		
		if($userData != false){
			if(!$userValid){
			$this->textMessage = self::$notValidMessage;
			}
			else{
				if($this->loginView->didUserWantToBeRemembered()){
					$this->savedSession = true;
					if($this->model->saveSession($userData) && $this->cookies->saveUser($userData)){
						$this->textMessage = self::$savedSessionMessage;
					}
					else{//Om sessionen inte lyckats sparas
						$this->textMessage = self::$validUserMessage;
					}
				}
				else{//Om användaren inte vill bli ihågkommen
					if($this->textMessage === ""){
						$this->savedSession = false;
						$this->textMessage = self::$validUserMessage;
					}
				}
				
				//Gå vidare till inloggad-ruta
				if($this->cookies->remove(self::$messageCookie)){
					$this->isLoggedIn = TRUE;
				}
				return $this->doLogout();
				//return $this->logoutView->showLogout($this->textMessage);
				//$this->cookies->remove("Message");
				//$this->doLogout();
				//$this->cookies->remove("Message");
			}
		}
		//Visa inloggningssida
		return $this->loginView->showLogin($this->textMessage);
		}
		
		//$this->doLogout();
	}
	
	public function doLogout(){
		
			while($this->isLoggedIn === TRUE){
			if ($this->logoutView->didUserPressLogout()){
				if($this->savedSession === true){
					if($this->cookies->removeUser() === false){
						var_dump("Lyckades inte ta bort cookie");
						die();
					}
					else{
						if($this->model->unsetSession()=== false){
							var_dump("Lyckades inte ta bort session");
							die();
						}
						else{
						$this->textMessage = self::$outlogMessage;
						$this->savedSession = false;
						$this->isLoggedIn = FALSE;
						var_dump($this->textMessage. "session");
						die();
						}
					}
				}
				elseif($this->savedSession === false){
					$this->textMessage = self::$outlogMessage;
					$this->savedSession = false;
					$this->isLoggedIn = FALSE;
					var_dump($this->textMessage . "ej session");
					die();
					
				}
					
					$this->isLoggedIn = FALSE;
					//var_dump($this->textMessage);
					//die();
					$this->doLogin();
					
			}
			//else{
				//var_dump("Lyckades inte ta bort cookie");
			//die();}
			return $this->logoutView->showLogout($this->textMessage);
			//$this->cookies->save("Message", $this->textMessage);
		}

	}
}