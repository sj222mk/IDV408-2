<?php

namespace controller;

require_once("./model/LoginModel.php");
require_once("./view/LoginView.php");
require_once("./view/LogoutView.php");
require_once("./view/CookieStorage.php"); 
require_once("./view/ServerStorage.php");

class LoginController {
	private $cookies;
	private $loginView;
	private $logoutView;
	private $model;
	private $servers;
	
	private $isLoggedIn;
	private $savedSession;
	private $sessID;
	private $sessionArray;
	private $textMessage = "";
	private $userData = "";
	private $userName;
	private $userServerSettings;
	private $userValid;
	
	//Meddelanden till användaren efter validering och händelser
	private static $messageCookie = "Message";
	private static $outlogMessage = "Du har nu loggat ut";
	private static $cookieMessage = "Inloggning lyckades via cookies";
	private static $notCookieMessage = "Felaktig information i cookie";
	private static $notValidMessage = "Felaktigt användarnamn eller lösenord";
	private static $savedSessionMessage = "Inloggning lyckades och vi kommer ihåg dig nästa gång";
	private static $validUserMessage = "Inloggning lyckades";
	
	public function __construct() {		
		$this->model = new \model\LoginModel();
		$this->cookies = new \view\CookieStorage();
		$this->loginView = new \view\LoginView($this->cookies);
		$this->logoutView = new \view\LogoutView($this->cookies);
		$this->servers = new \view\ServerStorage();
	}

	public function doLogin() {
		//Kolla av vem användaren är
		$this->userServerSettings = $this->servers->getUserServerSettings(); 
		$this->sessID = $this->cookies->getSessid();
		
		//Kontrollera om användaren är inloggad
		if(!$this->checkIfUserIsLoggedIn()){ 
			$this->isLoggedIn = FALSE;
		}
						
		while($this->isLoggedIn === FALSE){
		
		//Hantera indata
			if($this->checkIfUserPressedLogin() === true){
				//Validerar användaruppgifter	
				if($this->model->checkUser($this->userData) === true){
					if ($this->saveSessionAndSetMessage()){
						//Gå vidare till inloggad-sida
						return $this->doLogout();
					}
				}
				else{
					$this->textMessage = self::$notValidMessage;
					return $this->login();
				}
			}
			else{
				return $this->login();
			}
		}
		return $this->doLogout();
	}
	
	//Funktionaliteten på logga-ut-sidan
	private function doLogout(){
		
		while($this->isLoggedIn === TRUE){
			if ($this->logoutView->didUserPressLogout()){
				if($this->cookies->removeUserCookies() && 
					$this->model->unsetSession() &&	
					$this->model->removeRememberedUserSession($this->userName)				
					){
					$this->textMessage = self::$outlogMessage;
					$this->savedSession = false;
					$this->isLoggedIn = FALSE;	
				
					return $this->login();
				}						
			}
			return $this->logout();
		}
	}
	
	private function login(){
		if($this->textMessage != ""){
			$this->loginView->setUserMessage($this->textMessage);
		}
		if($this->userName != ""){
			$this->loginView->setUsername($this->userName);
		}
		$this->textMessage = "";
		return $this->loginView->showLogin();
	}
	
	private function logout(){
		$this->logoutView->setUsername($this->userName);
		$this->logoutView->setUserMessage($this->textMessage);
		$this->textMessage = "";
		return $this->logoutView->showLogout();
	}
	
	private function checkIfUserIsLoggedIn(){
		$clientName;
		$clientSession;		
												//Kollar om användarens serveruppgifter är lagrade i sessionen
		$clientName = $this->model->doesSessionExist($this->userServerSettings); //
		if($clientName != false){
			$this->userName = $clientName;	
			$this->textMessage = "";
			$this->isLoggedIn = TRUE;
			return true;
		}
			
		if($this->cookies->checkUserCookies() === true){ //Kollar kakor för användarnamn och lösenord är satta
			$userCookies = $this->cookies->loadUserFromCookie(); //Returns array eller false
			if($userCookies != false){
															//Kollar om sessionen finns sparad eller en äldre version
				$clientSession = $this->model->verifyRememberedClient($userCookies, $this->userServerSettings, $this->sessID);
				if($clientSession === false){
					$this->textMessage = self::$notCookieMessage;
					return false;
				}
				if($clientSession['session'] === "new"){
					$this->textMessage = self::$cookieMessage;
				}
				elseif($clientSession['session'] === "old"){
					$this->textMessage = "";
				}
				$this->userName = $clientSession['user'];
				$this->userData['user'] = $clientSession['user'];
				if($this->model->saveUserSession($this->userData, $this->userServerSettings) &&
					$this->model->saveRememberedUserSession($this->userData, $this->userServerSettings, $this->sessID))
				{
					
					$this->isLoggedIn = TRUE;
					return true;
				}
			}	
		}
		return false;
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
		if($this->model->saveUserSession($this->userData, $this->userServerSettings)){
			$this->isLoggedIn = TRUE;
			//Om användaren kryssat i "Håll mig inloggad"
			if($this->loginView->didUserWantToBeRemembered()){
				return $this->userWantsToBeRemembered();
			}
			else{//Om användaren inte vill bli hållen inloggad
				return $this->userDontWantToBeRemembered(); 
			}
		}
	}
	
	private function userWantsToBeRemembered(){
		$this->savedSession = TRUE;
		if($this->model->saveRememberedUserSession($this->userData, $this->userServerSettings, $this->sessID) && 
			$this->cookies->saveUserCookies($this->userData))
			{
			$this->textMessage = self::$savedSessionMessage;
			return true;
		}
		else{											//Om sessionen inte lyckats sparas
			$this->textMessage = self::$validUserMessage;
			return true;
		}
	}	
	
	private function userDontWantToBeRemembered(){
		if($this->textMessage === ""){
			$this->savedSession = FALSE;
			$this->textMessage = self::$validUserMessage;
		}
		return true;
	}
}