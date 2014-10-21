<?php

require_once("model/LoginModel.php");
require_once("view/LoginView.php");
require_once("view/LogoutView.php");
require_once("view/CookieStorage.php"); //??

class LoginController {
	private $view;
	private $model;
	private $storage;
	
	public function __construct() {		
		$this->model = new LoginModel();
		$this->loginView = new LoginView($this->model);
		$this->logoutView = new LogoutView($this->model);
		$this->cookies = new \view\CookieStorage();
	}


//@return String HTML


	public function doLogin() {
		$errorMessage = "";
		$textMessage = "";
		$userData;
		$postControl;
		//$clientIdentifier = $this->messages->getClientIdentifier();
		
		do{
			
		//Hantera indata
			if ($this->loginView->didUserPressLogin()) {
				$userData = $this->loginView->getUserData();
				if(!$this->model->checkUser($userData)){				//Rätt uppgifter????????????????????
					$errorMessage = "Felaktigt användarnamn eller lösenord";
				} 
				else{
					if(true){//sparad inloggning
						$textMessage = "<p>Inloggning lyckades och vi kommer ihåg dig nästa gång</p>";
					}
					elseif (false) {//inloggning lyckades
						$textMessage = "<p>Inloggning lyckades</p>";
					}
					$this->cookies->save($textMessage);
					return $this->logoutView->showLogout($textMessage);
				}
			}
			else{
				
				 		
			}
			//$errorMessage = $this->cookies->load(); 
			return $this->loginView->showLogin($errorMessage);
		}
		while (1<2);
	}
}