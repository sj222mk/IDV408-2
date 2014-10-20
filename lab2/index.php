<?php

class HTMLView{
	public function echoHTML($body){
		echo "
			<!DOCTYPE html>
			<html>
			<body>
				$body
			</body>
			</html>";
	}
}
//require_once("HTMLView.php");
$view = new HTMLView();
$view->echoHTML("Hello World");
?>