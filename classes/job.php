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
					
					JobSeeker::LoadObject($this, $row);
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
			$object->jobName = $row['JobName'];
			$object->skillCategoryId = $row['SkillCategoryId'];
		}
		
	
		public function Save() {
			

		
			$errorMessage = "";
			$objectId = $this->jobId;
			
		
			if ($this->jobId == 0) {
				
				// Insert job
						
				if ($errorMessage == "") {

					$sql = "insert into job";
					$sql .= " (JobName, SkillCategoryId,";
					$sql .= " Created)";
					$sql .= " values";
					$sql .= " (?,?,";
					$sql .= " UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("si", $this->jobName, $this->skillCategoryId);
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
				$sql .= " JobName = ?,";
				$sql .= " SkillCategoryId = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where JobId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("sii", $this->jobName, $this->skillCategoryId,
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
	
	
	}
	
?>	
