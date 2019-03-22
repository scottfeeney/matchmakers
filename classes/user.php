<?php
	
	namespace Classes;
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	
	class User {
	
		public $userId;
		public $userType;
		public $email;
		public $active;
		public $verifyCode;
		public $verified;
		public $enteredDetails;
		public $password;
		
		public function __construct($userId = 0) {
        
			if ($userId != 0) {
			
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
					
					//print_r($result);
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
	
		public function Save() {
		
		
			$errorMessage = "";
			$objectId = $this->userId;
			
			
			// check email does not exist for another user
			if (User::GetEmailExists($this->email, $this->userId)) {
				$errorMessage = "Email address exists in system";
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
					$sql .= " (UserType, Email, Active, VerifyCode, Verified, EnteredDetails, Password, Created)";
					$sql .= " values";
					$sql .= " (?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isisiis", $this->userType, $this->email, $this->active, $this->verifyCode, $this->verified, $this->enteredDetails, $this->password);
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
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where UserId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isisiisi", $this->userType, $this->email, $this->active, $this->verifyCode, $this->verified, $this->enteredDetails, $this->password, $this->userId);
					$stmt->execute();
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();

			}
			
			//return object
			return new \Classes\ObjectSave($errorMessage, $objectId);
		
		}
	
		
		// check if email address exists in users table
		
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
		
		
		// get user by verify Code
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

		// get user for login
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
	
	
	}
	
?>	