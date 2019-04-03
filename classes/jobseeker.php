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
		
		public $fieldOfExpertise;
		public $ageGroup;
		public $highestLevelCompleted;
		public $currentlyStudying;
		public $currentStudyLevel;
		public $signUpReason;
		public $jobChangeSpeed;
				
		public function __construct($jobSeekerId = 0) {
        
			if ($jobSeekerId != 0) {
			    
			    // TODO: Update query
				$sql = "select * from job_seeker where JobSeekerId = ?";
				
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
			
			$object->fieldOfExpertise = $row['FieldOfExpertise'];
			$object->ageGroup = $row['AgeGroup'];
			$object->highestLevelCompleted = $row['HighestLevelCompleted'];
			$object->currentlyStudying = $row['CurrentlyStudying'];
			$object->currentStudyLevel = $row['CurrentStudyLevel'];
			$object->signUpReason = $row['SignUpReason'];
			$object->jobChangeSpeed = $row['JobChangeSpeed'];
		}
		
	
		public function Save() {
			

		
			$errorMessage = "";
			$objectId = $this->jobSeekerId;
			
		
			if ($this->jobSeekerId == 0) {
				
				// Insert jobseeker
						
				if ($errorMessage == "") {

					$sql = "insert into job_seeker";
					$sql .= " (UserId, Title, FirstName, LastName, PhoneAreaCode, PhoneNumber, MobileNumber,";
					$sql .= " Address1, Address2, City, State, Postcode,";
					$sql .= " FieldOfExpertise, AgeGroup, HighestLevelCompleted, CurrentlyStudying, CurrentStudyLevel, SignUpReason, JobChangeSpeed,";
					$sql .= " Created)";
					$sql .= " values";
					$sql .= " (?, ?, ?, ?, ?, ?, ?,";
					$sql .= " ?, ?, ?, ?, ?,";
					$sql .= " ?, ?, ?, ?, ?, ?, ?,";
					$sql .= " UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("issssssssssssssssss", $this->userId, $this->title, $this->firstName, $this->lastName, $this->phoneAreaCode, $this->phoneNumber, $this->mobileNumber,
								$this->address1, $this->address2, $this->city, $this->state, $this->postcode,
								$this->fieldOfExpertise, $this->ageGroup, $this->highestLevelCompleted, $this->currentlyStudying, $this->currentStudyLevel, $this->signUpReason, $this->jobChangeSpeed
								);
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
				
				
				$sql = "update job_seeker";
				$sql .= " set";
				$sql .= " UserId = ?,";
				$sql .= " Title = ?,";
				$sql .= " FirstName = ?,";
				$sql .= " LastName = ?,";
				$sql .= " PhoneAreaCode = ?,";
				$sql .= " PhoneNumber = ?,";
				$sql .= " MobileNumber = ?,";
				$sql .= " Address1 = ?,";
				$sql .= " Address2 = ?,";
				$sql .= " City = ?,";
				$sql .= " State = ?,";
				$sql .= " Postcode = ?,";
				$sql .= " FieldOfExpertise = ?,";
				$sql .= " AgeGroup = ?,";
				$sql .= " HighestLevelCompleted = ?,";
				$sql .= " CurrentlyStudying = ?,";
				$sql .= " CurrentStudyLevel = ?,";
				$sql .= " SignUpReason = ?,";
				$sql .= " JobChangeSpeed = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where JobSeekerId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("issssssssssssssssssi", $this->userId, $this->title, $this->firstName, $this->lastName, $this->phoneAreaCode, $this->phoneNumber, $this->mobileNumber,
							$this->address1, $this->address2, $this->city, $this->state, $this->postcode,
							$this->fieldOfExpertise, $this->ageGroup, $this->highestLevelCompleted, $this->currentlyStudying, $this->currentStudyLevel, $this->signUpReason, $this->jobChangeSpeed,
							$this->jobSeekerId);
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
			$sql = "select JobSeekerId from job_seeker where UserId = ?";
				
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

		public static function GetExpertiseFields() 
		{
			return array("Accountancy", "Health", "Information Technology", "Marketing", "Sales");
		}
		
		public static function GetAgeGroups() 
		{
			return array("18-24", "25-34", "35-49", "50-65", "65+", "Rather Not Say");
		}
		
		public static function GetEducationLevels() 
		{
			return array("High School", 
				"T.A.F.E. or Trade Certificate", 
				"Diploma", 
				"Advanced Diploma",
				"Undergraduate Degree", 
				"Postgraduate Degree", 
				"Master's Degree",
				"Doctorate Degree");
		}
		
		/*public static function GetYesNo() {
			return array("Yes", "No"); 
			
		}*/
		
		public static function GetSignupReasons() 
		{
			return array("Currently without employment",
				"Considering a change in the near future", 
				"Seeing what may be out there");
	
		}
		
		public static function GetJobChangeSpeeds() 
		{
			return array("Immediate",
				"Within 2 weeks", 
				"2-4 weeks", 
				"1-2 months", 
				"2-6 months",
				"Other");
	
		}
	
	}
	
?>	
