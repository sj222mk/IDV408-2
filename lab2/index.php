<?php

require_once "/customers/3/d/d/ssalander.se//httpd.www/courses/1dv408-2/common/HTMLView.php";
require_once "/customers/3/d/d/ssalander.se//httpd.www/courses/1dv408-2/view/TimeView.php";
require_once "/customers/3/d/d/ssalander.se//httpd.www/courses/1dv408-2/ctrl/LoginController.php";

session_name("MySession");
session_start();

$lc = new \controller\LoginController();
$t = new \view\TimeView();

$htmlBody = $lc->doLogin();
$time = $t->setTime();

$view = new \common\HTMLView();
$view->echoHTML($htmlBody, $time);
