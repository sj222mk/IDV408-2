<?php

require_once("view/HTMLView.php");
require_once("view/TimeView.php");
require_once("ctrl/LoginController.php");
//require_once("view/CookieStorage.php");

//$cookies = new \view\CookieStorage();
//$c = $cookie->start("test", "");
//setCookie("CookieStorage['test']", "");
//session_name('mySession'); 
//setcookie("mySession[mess]", "test", -1);
//setcookie("mySession[user]", "none", -1);
//$SESSION['mess'] = "test";
//$SESSION['user'] = "none";

session_start();

$lc = new LoginController();
$t = new TimeView();

$htmlBody = $lc->doLogin();
$time = $t->setTime();

$view = new HTMLView();
$view->echoHTML($htmlBody, $time);
var_dump($_COOKIE);