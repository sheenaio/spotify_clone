<?php 
	//include("includes/classes/Constants.php");
	
	class Account {
		
		private $con;
		private $errorArray;

		public function __construct($con) {
			$this->con = $con;
			$this-> errorArray = array();
		}

		public function login($un, $pw) {
			$pw = md5($pw);

			$query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw'");

			if(mysqli_num_rows($query) == 1) {
				return true;
			} 
			else {
				array_push($this->errorArray, Constants::$loginFailed);
				return false;
			}
		}

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstName($fn);
			$this->validateLastName($ln);
			$this->validateEmails($em, $em);
			$this->validatePasswords($pw, $pw);

			if(empty($this->errorArray)) {
				//insert into database

				return $this->insertUserDetails($un, $fn, $ln, $em, $pw);
			} 
			else {
				return false;
			}
		}


		public function getError($error) {
			if(!in_array($error, $this->errorArray)) {
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		private function insertUserDetails($un, $fn, $ln, $em, $pw) {
			$encryptedPw = md5($pw);
			$profilePic = "assets/images/profile-pics/head.jpg";
			$date = date("Y-m-d");

			$result = mysqli_query($this->con, "INSERT INTO users VALUES ('', '$un', '$fn', '$ln', '$em', '$encryptedPw', 'date', '$profilePic')");

			return $result;
		}

		private function validateUsername($un) {

			//check if username is greater than 25 chars or less than 5 chars
			if(strlen($un) > 25 || strlen($un) < 5 ) {
				array_push($this->errorArray, Constants::$usernameCharacters);
				return;
			}

			//check if username doesn't already exist
			$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
			if(mysqli_num_rows($checkUsernameQuery) != 0) {
				array_push($this->errorArray, Constants::$usernameTaken);
				return;
			}
		}


		private function validateFirstName($fn) {
			//check if first name is not less than 2 chars
			if(strlen($fn) < 2 ) {
				array_push($this->errorArray, Constants::$firstNameCharacters);
				return;
			}
		}

		private function validateLastName($ln) {
			//check if last name is not less than 2 chars
			if(strlen($ln) < 2 ) {
				array_push($this->errorArray, Constants::$lastNameCharacters);
				return;
			}
			
		}

		private function validateEmails($em, $em2) {
			//checks if email and confirmation email matches
			if($em != $em2) {
				array_push($this->errorArray, Constants::$emailsDoNotMatch);
				return;
			}

			//check if email is in the correct format
			if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
				array_push($this->errorArray, Constants::$emailInvalid);
				return;
			}

			//check that email hasn't already been used.
			$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");
			if(mysqli_num_rows($checkEmailQuery) != 0) {
				array_push($this->errorArray, Constants::$emailTaken);
				return;
			}
		}


		private function validatePasswords($pw, $pw2) {
			//checks if password and confirmation password matches
			if($pw != $pw2){
				array_push($this->errorArray, Constants::$passwordsDoNotMatch);
				return;
			}

			//checks if it contains non-alphanumeric chars
			if(preg_match('/[^A-Za-z0-9]/', $pw)) {
				array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
				return;
			}

			//check length of password
			if(strlen($pw) > 30 || strlen($pw) < 5 ) {
				array_push($this->errorArray, Constants::$passwordCharacters);
				return;
			}
		}

	}

?>