<?php
	
	namespace Classes;
	
	
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
		public $skillCategoryId;

				
		public function __construct($jobId = 0) {
        
			if ($jobId != 0) {
			    
			    // TODO: Update query
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
			}
		}
		
		private static function LoadObject($object, $row) {
			$object->jobId = $row['JobId'];
			$object->employerId = $row['EmployerId'];
			$object->jobName = $row['JobName'];
			$object->skillCategoryId = $row['SkillCategoryId'];
		}
		
	
		public function Save() {
			
		
			$errorMessage = "";
			$objectId = $this->jobId;
			
		
			if ($this->jobId == 0) {
				
				// Insert job
				
				//print_r($this);
				//exit;
			
						
				if ($errorMessage == "") {

					$sql = "insert into job";
					$sql .= " (EmployerId, JobName, SkillCategoryId,";
					$sql .= " Created)";
					$sql .= " values";
					$sql .= " (?,?,?,";
					$sql .= " UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isi", $this->employerId, $this->jobName, $this->skillCategoryId);
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
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where JobId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("isii", $this->employerId, $this->jobName, $this->skillCategoryId,
							$this->jobId);
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
	
	
		public static function SaveJobSkills($jobId, $selectedSkills) {
			
			$job = new Job($jobId);
			
			// TODO check $selectedSkills contains integers
			
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
			
			$sql = " delete from job_skill where JobId = ? and SkillId not in";
			$sql .= " (";
			$sql .= " 	select SkillId from Skill where SkillCategoryId = ?";
			$sql .= " 	and SkillId in (" . $selectedSkills . ")";
			$sql .= " )";

			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ii", $job->jobId,  $job->skillCategoryId);
			$stmt->execute();
			
			
			$sql = " insert into job_skill (JobId, SkillId)";
			$sql .= " select ?, SkillId from Skill where SkillCategoryId = ?";
			$sql .= " and SkillId in (" . $selectedSkills . ")";
			$sql .= " and SkillId not in (select SkillId from job_skill where JobId = ?)";
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("iii", $job->jobId,  $job->skillCategoryId, $job->jobId);
			$stmt->execute();
			
			$stmt->close();
			$conn->close();

		}
		
		// Get Jobs By Employer
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
		
		public static function GetSkillsByJobString($jobId) {
			
			$skills = \Classes\Skill::GetSkillsByJob($jobId);
			
			$skillsList = Array();
			
			foreach ($skills as $skill) {
				$skillsList[] = $skill->skillId;
			}
			
			return join(",",$skillsList);
			
		}
		
		
	}
	
?>	
