<?php

namespace controller;

require_once("model/LoginModel.php");
require_once("view/LoginView.php");
require_once("view/LogoutView.php");
require_once("view/CookieStorage.php"); 

class LoginController {
	private $view;
	private $model;
	private $storage;
	private $isLoggedIn;
	private $textMessage = "";
	private $savedSession;
	private $userData = "";
	private $userValid;
	private $userName;
	private $sessionArray;
	
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
		
		/*if($this->isLoggedIn === TRUE){
			return $this->doLogout();		
			//return $this->logoutView->showLogout($this->textMessage);
			
		}*/
		
		//$this->isLoggedIn = FALSE;
		
		
		//Kontrollera om användaren är inloggad
		if($this->checkIfUserIsLoggedIn() === false){
			$this->isLoggedIn = FALSE;
		}
				
		while($this->isLoggedIn === FALSE){
		
		//Hantera indata
			if($this->checkIfUserPressedLogin() === true){
				//Validerar användaruppgifter	
				if($this->model->checkUser($this->userData) === true){
					if ($this->saveSessionAndSetMessage() && $this->setToLogout()){
						//Gå vidare till inloggad-sida
						return $this->doLogout();
					}
				}
				else{
					$this->textMessage = self::$notValidMessage;
					return $this->loginView->showLogin($this->textMessage);
				}
			}
			else{
				return $this->loginView->showLogin($this->textMessage);
			}
		}
		
		return $this->doLogout();
	}
	
	private function setToLogout(){
		//Ändra inställningar inför byte av vy
		if(/*$this->cookies->remove(self::$messageCookie) && */$this->isLoggedIn = TRUE){
			//if($this->model->save)											//spara i session
		
			return true;
		}
		return false;
	}
	
	private function doLogout(){
		
		while($this->isLoggedIn === TRUE){
			if ($this->logoutView->didUserPressLogout()){
				$this->model->unsetSession();					
				
				$this->cookies->removeUser();
				
				$this->textMessage = self::$outlogMessage;
				$this->savedSession = false;
				$this->isLoggedIn = FALSE;	
					
				return $this->loginView->showLogin($this->textMessage);						
			}
			return $this->logoutView->showLogout($this->textMessage, $this->userName);
		}

	}

	private function checkIfUserIsLoggedIn(){
		$clientSession;
		$userSession;
		
		if($this->cookies->checkUserCookie()){
			$this->userData = $this->cookies->loadUserFromCookie();
			if(!$this->userData === false){
				$clientSession = $this->model->doesClientExist($this->userData);
				if($clientSession){
					$this->textMessage = self::$cookieMessage;
					$this->userName = $clientSession;
					$this->isLoggedIn = TRUE;
					return true;
				}
				else{
					$this->textMessage = self::$notCookieMessage;
					return false;
				}
			}
			else{
				$this->textMessage = self::$notCookieMessage;
				return false;
			}	
		}
		else{
			$clientSession = $this->model->doesSessionExist();
				if($clientSession != false){
					$this->userName = $clientSession;	
					$this->isLoggedIn = TRUE;
					return true;
			}
		
		return false;
		}
	}
	
	private function checkIfUserPressedLogin(){
		if ($this->loginView->didUserPressLogin()) {
			$this->userData = $this->loginView->getUserData();
			if($this->userData === false){
				$this->textMessage = self::notValidMessage;
				return false;
			}
			else{
				$this->userName = $this->userData['user'];
				return true;
			}
		}
	}
	
	private function saveSessionAndSetMessage(){
		//Om användaren kryssat i "Håll mig inloggad"
		if($this->loginView->didUserWantToBeRemembered()){
			return $this->userWantsToBeRemembered();
		}
		else{//Om användaren inte vill bli hållas inloggad
			return $this->userDontWantToBeRemembered();
		}
		//return false;
	}
	
	private function userWantsToBeRemembered(){
		$this->savedSession = TRUE;
			if($this->model->saveUserSession($this->userData) && $this->cookies->saveUser($this->userData)){
				$this->textMessage = self::$savedSessionMessage;
				return true;
			}
			else{//Om sessionen inte lyckats sparas
				$this->textMessage = self::$validUserMessage;
				return true;
			}
	}	
	private function userDontWantToBeRemembered(){
		if($this->model->saveSession($this->sessionArray, $this->userName)){
			if($this->textMessage === ""){
				$this->savedSession = FALSE;
				$this->textMessage = self::$validUserMessage;
			}
			return true;
		}
		return false;
	}
		
		
		
		//return $this->logoutView->showLogout($this->textMessage);
		//$this->cookies->remove("Message");
		//$this->doLogout();
		//$this->cookies->remove("Message");
			
		
			

}