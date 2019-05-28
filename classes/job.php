<?php
	
	//----------------------------------------------------------------
	// Job class - performs operations for job object
	// and job related functionality
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
	
	class Job {
	
		public $jobId;
		public $employerId;
		public $jobName;
		public $referenceNumber;
		public $numberAvailable;
		public $locationId;
		public $jobTypeId;
		public $jobDescription;
		public $skillCategoryId;
		public $positionAvailability;
		public $active;
		public $created;
				
		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/					
		public function __construct($jobId = 0) {
        
			if ($jobId != 0) {
			    
				$sql = "select * from job where JobId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $jobId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					Job::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->jobId = 0;
				$this->active = 1;
			}
		}
		
		// populate object from database row
		private static function LoadObject($object, $row) {
			$object->jobId = $row['JobId'];
			$object->employerId = $row['EmployerId'];
			$object->jobName = $row['JobName'];
			$object->skillCategoryId = $row['SkillCategoryId'];
			$object->referenceNumber = $row['ReferenceNumber'];
			$object->locationId = $row['LocationId'];
			$object->jobTypeId = $row['JobTypeId'];
			$object->numberAvailable = $row['NumberAvailable'];
			$object->positionAvailability = $row['PositionAvailability'];
			$object->jobDescription = $row['JobDescription'];
			$object->active = $row['Active'];
			$object->created = $row['Created'];
		}
		
		// Save Object
		public function Save() {
			
			
			$errorMessage = "";
			$objectId = $this->jobId;
			
		
			if ($this->jobId == 0) {
				
				// Insert job
				if ($errorMessage == "") {

					$sql = "insert into job";
					$sql .= " (EmployerId, JobName, SkillCategoryId,";
					$sql .= " ReferenceNumber, LocationId, JobTypeId, NumberAvailable, PositionAvailability, JobDescription, Active,";
					$sql .= " Created)";
					$sql .= " values";
					$sql .= " (?,?,?,";
					$sql .= " ?,?,?,?,?,?,?,";
					$sql .= " UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isisiiissi", $this->employerId, $this->jobName, $this->skillCategoryId,
						$this->referenceNumber, $this->locationId, $this->jobTypeId, $this->numberAvailable, $this->positionAvailability, $this->jobDescription, $this->active);
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

				// Edit job
				$sql = "update job";
				$sql .= " set";
				$sql .= " EmployerId = ?,";
				$sql .= " JobName = ?,";
				$sql .= " SkillCategoryId = ?,";
				$sql .= " ReferenceNumber = ?,";
				$sql .= " LocationId = ?,";
				$sql .= " JobTypeId = ?,";
				$sql .= " NumberAvailable = ?,";
				$sql .= " PositionAvailability = ?,";
				$sql .= " JobDescription = ?,";
				$sql .= " Active = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where JobId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isisiiissii", $this->employerId, $this->jobName, $this->skillCategoryId,
							$this->referenceNumber, $this->locationId, $this->jobTypeId, $this->numberAvailable, $this->positionAvailability, $this->jobDescription, $this->active,
							$this->jobId);
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
		* SaveJobSkills saves job skills selected by employer
		*/
		public static function SaveJobSkills($jobId, $selectedSkills) {
			
			$job = new Job($jobId);
			
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
			
			$sql = " delete from job_skill where JobId = ? and SkillId not in";
			$sql .= " (";
			$sql .= " 	select SkillId from Skill where SkillCategoryId = ?";
			$sql .= " 	and SkillId in (" . \Utilities\Common::GetCheckedSelectedSkills($selectedSkills) . ")";
			$sql .= " )";

			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ii", $job->jobId,  $job->skillCategoryId);
			$stmt->execute();
			
			
			$sql = " insert into job_skill (JobId, SkillId)";
			$sql .= " select ?, SkillId from Skill where SkillCategoryId = ?";
			$sql .= " and SkillId in (" . \Utilities\Common::GetCheckedSelectedSkills($selectedSkills) . ")";
			$sql .= " and SkillId not in (select SkillId from job_skill where JobId = ?)";
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("iii", $job->jobId,  $job->skillCategoryId, $job->jobId);
			$stmt->execute();
			
			$stmt->close();
			$conn->close();

		}
		
		/*
		* GetJobsByEmployer returns jobs by employer
		*/
		public static function GetJobsByEmployer($employerId) {
			
			$jobs = Array();
			
			$sql = "select * from Job where EmployerId = ? order by Created desc";

				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $employerId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$job = new Job();
				Job::LoadObject($job, $row);
				$jobs[] = $job;
			}
			
			return $jobs;
			
		}
		
		/*
		* GetSkillsByJobString returns a comma string of skill Ids
		*/		
		public static function GetSkillsByJobString($jobId) {
			
			$skills = \Classes\Skill::GetSkillsByJob($jobId);
			
			$skillsList = Array();
			
			foreach ($skills as $skill) {
				$skillsList[] = $skill->skillId;
			}
			
			return join(",",$skillsList);
			
		}
		
		
		/*
		* GetJobMatchesByJobSeeker returns Job Matches for a Job Seeker
		*/		
		public static function GetJobMatchesByJobSeeker($jobSeekerId) {
			
			$jobMatches = Array();
			
			$sql = "call JobSeeker_JobMatches(?, null)";

				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $jobSeekerId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$object = new \StdClass;
				$object->jobId = $row['JobId'];
				$object->jobName = $row['JobName'];
				$object->employerName = $row['EmployerName'];
				$object->locationName = $row['LocationName'];
				$object->jobTypeName = $row['JobTypeName'];
				$object->score = $row['Score'];
				$jobMatches[] = $object;
			}
			
			return $jobMatches;
			
		}
		
		
		/*
		* GetJobSeekerMatch returns Job match score for a job seeker and job
		*/	
		public function GetJobSeekerMatch($jobSeekerId) {
			
			$sql = "call JobSeeker_JobMatches(?, ?)";

			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ii", $jobSeekerId, $this->jobId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result);
				return round($row['Score']);
			}
			
			
			return null;
			
		}
		
		/*
		* The following arrays are used in job form
		*/		
		public static function GetPositionAvailabilities() 
		{
			return array("Immediate",
				"Within 2 weeks", 
				"2-4 weeks", 
				"1-2 months", 
				"2-6 months",
				"Other");
		}
		
		public static function GetNumberAvailables() 
		{
			return array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10+");
	
		}
		
	}
	
?>	
