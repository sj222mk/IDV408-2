 <?php
class LoginModel {
	public function __construct() {
	}

	public function checkUser($userData){
		return false;
		/*if(@file('Users/Admin.txt') === TRUE){
			return 1;
		}
		else{
			return 0;
		}*/
	}
	/*public function getNumLikes() {
		$lines = @file("LikeModel.txt");

		if ($lines === FALSE) {
			return 0;
		}
	
	return count($lines);
}*/

	public function hasLiked($clientIdentifier) {
		$lines = @file("LikeModel.txt");
	
		if ($lines === FALSE) {
			return false;
		}
	
		foreach ($lines as $existingidentifier) {
			$existingidentifier = trim($existingidentifier);
			if ($existingidentifier === $clientIdentifier) {
				return true;
			}
		}
		return false;
	}
	
	public function addLike($clientIdentifier) {
		if ($this->hasLiked($clientIdentifier)) {
			return;
		}
		$fp = fopen("LikeModel.txt", 'a');
		fwrite($fp, $clientIdentifier . "\n");
	}
}