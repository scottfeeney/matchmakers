<?php
	
	namespace Classes;
	
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	} else {
		require_once './config.php';
		require_once './classes/object_save.php';
	}
	
	class JobSeeker {
	
		public $jobSeekerId;
		public $userId;
		
		public $title;
		public $firstName;
		public $lastName;
		
		public $phoneAreaCode;
		public $phoneNumber;
		public $mobileNumber;
		
		public $address1;
		public $address2;
		public $city;
		public $state;
		public $postcode;
				
		public function __construct($jobSeekerId = 0) {
        
			if ($jobSeekerId != 0) {
			    
			    // TODO: Update query
				$sql = "select * from jobSeeker where jobSeekerId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $jobSeekerId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					JobSeeker::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->jobSeekerId = 0;
			}
		}
		
		private static function LoadObject($object, $row) {
			//$object->employerId = $row['EmployerId'];
			$object->jobSeekerId = $row['JobSeekerId'];
			$object->userId = $row['UserId'];
			
			$object->title = $row['Title'];
			$object->firstName = $row['FirstName'];
			$object->lastName = $row['LastName'];
			
			$object->phoneAreaCode = $row['PhoneAreaCode'];
			$object->phoneNumber = $row['PhoneNumber'];
			$object->mobileNumber = $row['MobileNumber'];
			
			$object->address1 = $row['Address1'];
			$object->address2 = $row['Address2'];
			$object->city = $row['City'];
			$object->state = $row['State'];
			$object->postcode = $row['Postcode'];
		}
		
	
		public function Save() {
		
			$errorMessage = "";
			$objectId = $this->jobSeekerId;
			
		
			if ($this->jobSeekerId == 0) {
				
				// Insert jobseeker
						
				if ($errorMessage == "") {
				    // TODO: Update query
					// Remove CompanyName, LocationId, all OtherX, CompanyType, CompanySize, ExpectedGrowth
					$sql = "insert into employer";
					$sql .= " (UserId, CompanyName, LocationId,";
					$sql .= " Title, FirstName, LastName, PhoneAreaCode, PhoneNumber, MobileNumber,";
					$sql .= " OtherTitle, OtherFirstName, OtherLastName, OtherPhoneAreaCode, OtherPhoneNumber,";
					$sql .= " Address1, Address2, City, State, Postcode,";
					$sql .= " CompanyType, CompanySize, ExpectedGrowth,";
					$sql .= " Created)";
					$sql .= " values";
					$sql .= " (?, ?, ?,";
					$sql .= " ?, ?, ?, ?, ?, ?,";
					$sql .= " ?, ?, ?, ?, ?,";
					$sql .= " ?, ?, ?, ?, ?,";
					$sql .= " ?, ?, ?,";
					$sql .= " UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isisssssssssssssssssss", $this->userId, $this->companyName, $this->locationId, 
								$this->title, $this->firstName, $this->lastName, $this->phoneAreaCode, $this->phoneNumber, $this->mobileNumber,
								$this->otherTitle, $this->otherFirstName, $this->otherLastName, $this->otherPhoneAreaCode, $this->otherPhoneNumber,
								$this->address1, $this->address2, $this->city, $this->state, $this->postcode,
								$this->companyType, $this->companySize, $this->expectedGrowth);
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

				// Edit job seeker
				
				// TODO: Update query
				// Remove CompanyName, LocationId, all OtherX, CompanyType, CompanySize, ExpectedGrowth
				
				$sql = "update employer";
				$sql .= " set";
				$sql .= " UserId = ?,";
				$sql .= " CompanyName = ?,";
				$sql .= " LocationId = ?,";
				$sql .= " Title = ?,";
				$sql .= " FirstName = ?,";
				$sql .= " LastName = ?,";
				$sql .= " PhoneAreaCode = ?,";
				$sql .= " PhoneNumber = ?,";
				$sql .= " MobileNumber = ?,";
				$sql .= " OtherTitle = ?,";
				$sql .= " OtherFirstName = ?,";
				$sql .= " OtherLastName = ?,";
				$sql .= " OtherPhoneAreaCode = ?,";
				$sql .= " OtherPhoneNumber = ?,";
				$sql .= " Address1 = ?,";
				$sql .= " Address2 = ?,";
				$sql .= " City = ?,";
				$sql .= " State = ?,";
				$sql .= " Postcode = ?,";
				$sql .= " CompanyType = ?,";
				$sql .= " CompanySize = ?,";
				$sql .= " ExpectedGrowth = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where EmployerId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isisssssssssssssssssssi", $this->userId, $this->companyName, $this->locationId, 
							$this->title, $this->firstName, $this->lastName, $this->phoneAreaCode, $this->phoneNumber, $this->mobileNumber,
							$this->otherTitle, $this->otherFirstName, $this->otherLastName, $this->otherPhoneAreaCode, $this->otherPhoneNumber,
							$this->address1, $this->address2, $this->city, $this->state, $this->postcode,
							$this->companyType, $this->companySize, $this->expectedGrowth,
							$this->employerId);
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
	
		
		// get job seeker by user id
		public static function GetJobSeekerByUserId($userId) 
		{
			// TODO: Update query
			$sql = "select EmployerId from employer where UserId = ?";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result);
				return new JobSeeker($row['JobSeekerId']);
			}
			
			
			return null;
			
		}
		
		
		public static function GetTitles() 
		{
			return array("Mr", "Ms", "Miss", "Mrs", "Dr");
		}
		
		public static function GetStates() 
		{
			return array("ACT", "NSW", "NT", "QLD", "SA", "TAS", "VIC", "WA");
		}

	
	
	}
	
?>	
