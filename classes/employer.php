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
		public $title;
		public $firstName;
		public $lastName;
		public $phoneAreaCode;
		public $phoneNumber;
		public $mobileNumber;
		public $otherTitle;
		public $otherFirstName;
		public $otherLastName;
		public $otherPhoneAreaCode;
		public $otherPhoneNumber;
		public $address1;
		public $address2;
		public $city;
		public $state;
		public $postcode;
		public $companyType;
		public $companySize;
		public $expectedGrowth;
				
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
			$object->title = $row['Title'];
			$object->firstName = $row['FirstName'];
			$object->lastName = $row['LastName'];
			$object->phoneAreaCode = $row['PhoneAreaCode'];
			$object->phoneNumber = $row['PhoneNumber'];
			$object->mobileNumber = $row['MobileNumber'];
			$object->otherTitle = $row['OtherTitle'];
			$object->otherFirstName = $row['OtherFirstName'];
			$object->otherLastName = $row['OtherLastName'];
			$object->otherPhoneAreaCode = $row['OtherPhoneAreaCode'];
			$object->otherPhoneNumber = $row['OtherPhoneNumber'];
			$object->address1 = $row['Address1'];
			$object->address2 = $row['Address2'];
			$object->city = $row['City'];
			$object->state = $row['State'];
			$object->postcode = $row['Postcode'];
			$object->companyType = $row['CompanyType'];
			$object->companySize = $row['CompanySize'];
			$object->expectedGrowth = $row['ExpectedGrowth'];
		}
		
	
		public function Save() {
		
			$errorMessage = "";
			$objectId = $this->employerId;
			
		
			if ($this->employerId == 0) {
				
				// Insert employer
				
						
				if ($errorMessage == "") {
					
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

				// Edit employer
				
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
	
		
		// get employer by user id
		public static function GetEmployerByUserId($userId) 
		{
			
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
				return new Employer($row['EmployerId']);
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
		
		public static function GetCompanyTypes() 
		{
			return array("Accountancy", "Health", "Information Technology", "Marketing", "Sales");
		}

		public static function GetCompanySizes() 
		{
			return array("0-9", "10-49", "50-99", "100-499", "500-999", "1000-9999", "10000+");
		}
		
		public static function GetExpectedGrowths() 
		{
			return array("1-4", "5-9", "10-19", "20-49", "50+");
		}

	
	
	}
	
?>	
