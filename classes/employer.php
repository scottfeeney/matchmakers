<?php
	
	namespace Classes;
	
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	} else {
		require_once './wwwroot/config.php';
		require_once './wwwroot/classes/object_save.php';
	}
	
	class Employer {
	
		public $employerId;
		public $userId;
		public $companyName;
		public $locationId;
				
		public function __construct($employerId = 0) {
        
			if ($employerId != 0) {
			
				$sql = "select * from employer where EmployerId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $employerId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					Employer::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->employerId = 0;
			}
		}
		
		private static function LoadObject($object, $row) {
			$object->employerId = $row['EmployerId'];
			$object->userId = $row['UserId'];
			$object->companyName = $row['CompanyName'];
			$object->locationId = $row['LocationId'];
		}
		
	
		public function Save() {
		
		
			$errorMessage = "";
			$objectId = $this->employerId;
			
		
			if ($this->employerId == 0) {
				
				// Insert employer
				
						
				if ($errorMessage == "") {
					
					$sql = "insert into employer";
					$sql .= " (UserId, CompanyName, LocationId, Created)";
					$sql .= " values";
					$sql .= " (?, ?, ?, UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isi", $this->userId, $this->companyName, $this->locationId);
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
			
			
				// Edit employer
				
				$sql = "update employer";
				$sql .= " set";
				$sql .= " UserId = ?,";
				$sql .= " CompanyName = ?,";
				$sql .= " LocationId = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where EmployerId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isii", $this->userId, $this->companyName, $this->locationId, $this->employerId);
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
	
		
		// get employer by user id
		public static function GetEmployerByUserId($userId) 
		{
			
			$sql = "select EmployerId from Employer where UserId = ?";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result);
				return new Employer($row['EmployerId']);
			}
			
			
			return null;
			
		}
	
	
	}
	
?>	