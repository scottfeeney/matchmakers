<?php
	
	//----------------------------------------------------------------
	// User class - performs operations for User object
	// and user related functionality
	//----------------------------------------------------------------
	
	namespace Classes;
	
	// include required php file, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	} else {
		require_once './config.php';
		require_once './classes/object_save.php';
	}
	
	class User {
	
		public $userId;
		public $userType;
		public $email;
		public $active;
		public $verifyCode;
		public $verified;
		public $enteredDetails;
		public $password;
		public $resetCode;
		
		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/		
		public function __construct($userId = 0) {
		
			if ($userId > 0) {
			
				$sql = "select * from user where UserId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $userId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					$this->userId = $row['UserId'];
					$this->userType = $row['UserType'];
					$this->email = $row['Email'];
					$this->active = $row['Active'];
					$this->verifyCode = $row['VerifyCode'];
					$this->verified = $row['Verified'];
					$this->enteredDetails = $row['EnteredDetails'];
					$this->password = $row['Password'];
					$this->resetCode = $row['ResetCode'];
					
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->userId = 0;
			}
		}
	
		// Save Object	
		public function Save() {
		
		
			$errorMessage = "";
			$objectId = $this->userId;
			
			
			// check email does not exist for another user
			if (User::GetEmailExists($this->email, $this->userId)) {
				$errorMessage = "Email address ".$this->email." exists in system for userId other than ".$this->userId;
			}
				
		
			if ($this->userId == 0) {
				
				// Insert user
				
				// default values
				$this->active = true;
				$this->verifyCode = \Utilities\Common::GetGuid();
				$this->verified = false;
				$this->enteredDetails = false;
		
				if ($errorMessage == "") {
					
					$sql = "insert into user";
					$sql .= " (UserType, Email, Active, VerifyCode, Verified, EnteredDetails, Password, ResetCode, Created)";
					$sql .= " values";
					$sql .= " (?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isisiiss", $this->userType, $this->email, $this->active, $this->verifyCode, $this->verified, $this->enteredDetails, $this->password, $this->resetCode);
						$stmt->execute();
						$objectId = $stmt->insert_id;
					} 
					else {
						$errorMessage = $conn->errno . ' ' . $conn->error;
					}
					
					$stmt->close();
					$conn->close();
				
				}
			
			}
			else {
			
			
				// Edit User
				
				$sql = "update user";
				$sql .= " set";
				$sql .= " UserType = ?,";
				$sql .= " Email = ?,";
				$sql .= " Active = ?,";
				$sql .= " VerifyCode = ?,";
				$sql .= " Verified = ?,";
				$sql .= " EnteredDetails = ?,";
				$sql .= " Password = ?,";
				$sql .= " ResetCode = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where UserId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isisiissi", $this->userType, $this->email, $this->active, $this->verifyCode, $this->verified, $this->enteredDetails, $this->password, $this->resetCode, $this->userId);
					$stmt->execute();
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();

			}
			
			//return helper object with errors and Id
			return new \Classes\ObjectSave($errorMessage, $objectId);
		
		}
	
		
		/*
		* GetEmailExists - checks if email address exists in users table
		*/			
		public function GetEmailExists($email, $userId) {
			
			$sql = "select * from user where email = ? and UserId <> ?";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("si", $email, $userId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 0) {
				return false;
			}
			else {
				return true;
			}
			
		}
		
		/*
		* GetUserByVerifyCode - returns user by verify Code
		* null is return if not found
		*/		
		public static function GetUserByVerifyCode($verifyCode) {
			
			$sql = "select UserId from user where VerifyCode = ? and Verified = 0 and Active = 1";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("s", $verifyCode);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result);
				return new User($row['UserId']);
			}
			else {
				return null;
			}
			
		}
		
		/*
		* GetUserByEmailAddress - returns user by email address
		* null is return if not found
		*/				
		public static function GetUserByEmailAddress($email) 
		{
			if ($email != "")
			{
				$sql = "select UserId from user where Email = ? and Verified = 1 and Active = 1";
					
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $email);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				$conn->close();
				
				if ($result->num_rows == 1) {
					$row = mysqli_fetch_array($result);
					return new User($row['UserId']);
				}
			}
			
			return null;
			
		}

		/*
		* GetUnverifiedUserByEmailAddress - returns user by email address
		* As per above, but doesn't require verified or active to be set
		* necessary where a user has lost or deleted the verification email
		* and then tries the forgot password function to reset their account.
		*/
		public static function GetUnverifiedUserByEmailAddress($email) 
		{
			if ($email != "")
			{
				$sql = "select UserId from user where Email = ?";
					
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $email);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				$conn->close();
				
				if ($result->num_rows == 1) {
					$row = mysqli_fetch_array($result);
					return new User($row['UserId']);
				}
			}
			
			return null;
			
		}
		

		/*
		* GetUserByResetCode - returns user by reset Code
		* null is return if not found
		*/	
		public static function GetUserByResetCode($resetCode) 
		{
			if (strlen($resetCode) == 36)
			{
				$sql = "select UserId from user where ResetCode = ? and Verified = 1 and Active = 1";
					
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $resetCode);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				$conn->close();
				
				if ($result->num_rows == 1) {
					$row = mysqli_fetch_array($result);
					return new User($row['UserId']);
				}
			}
			
			return null;
			
		}
		

		/*
		* GetUserLogin - gets user for login
		* null is return if not found
		*/	
		public static function GetUserLogin($email, $password) {
			
			if (strlen($email) > 3 and strlen($password) > 3)
			{
			
				$sql = "select UserId, Password from user where email=? and Verified = 1 and Active = 1";
					
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $email);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				$conn->close();
				
				if ($result->num_rows == 1) {
					$row = mysqli_fetch_array($result);
					if (password_verify($password, $row['Password'])) {
						return new User($row['UserId']);
					}
				}
				
			}
			
			return null;
			
		}
		
		/*
		* CreateApiToken - creates an API token for the user
		*/	
		public function CreateApiToken() {
			
			$token = password_hash(\Utilities\Common::GetGuid(), PASSWORD_BCRYPT);
			
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$sql = " update api_token set Active = 0 where UserId = ? and Active = 1;";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $this->userId);
			$stmt->execute();
			
			$sql = " insert into api_token (UserId, Token, Created, ExpiryDate, Active)";
			$sql .= " values";
			$sql .= " (?, ?, UTC_TIMESTAMP(), DATE_ADD(UTC_TIMESTAMP(), INTERVAL 1 HOUR), 1)";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("is", $this->userId, $token);
			$stmt->execute();
			
			$stmt->close();
			$conn->close();
			
			
			return $token;
			
		}
		
		/*
		* GetUserByApiToken - gets user by API token
		* null is return if not found
		*/	
		public static function GetUserByApiToken($token) {
			
			if (strlen($token) == 60)
			{
			
				$sql = "select UserId from api_token where Token = ? and Active = 1 and UTC_TIMESTAMP() < ExpiryDate;";
					
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("s", $token);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				$conn->close();
				
				if ($result->num_rows == 1) {
					$row = mysqli_fetch_array($result);
					return new User($row['UserId']);
				}
				
			}
			
			return null;
			
		}
	
	
	}
	
?>	
