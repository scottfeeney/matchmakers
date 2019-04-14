<?php
	
	namespace Classes;
	
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
	} else {
		require_once './config.php';
	}
	
	class JobType {
	
		public $jobTypeId;
		public $jobTypeName;

		
		public function __construct($jobTypeId = 0) {
        
			if ($jobTypeId != 0) {
			
				$sql = "select * from job_type where JobTypeId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $jobTypeId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					JobType::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->jobTypeId = 0;
			}
		}
	
		
		// Get All jobTypes
		
		public static function GetJobTypes() {
			
			$jobTypes = Array();
			
			$sql = "select * from job_type order by JobTypeName;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$jobType = new JobType();
				JobType::LoadObject($jobType, $row);
				$jobTypes[] = $jobType;
			}
			
			return $jobTypes;
			
		}

		
		private static function LoadObject($object, $row) {
			$object->jobTypeId = $row['JobTypeId'];
			$object->jobTypeName = $row['JobTypeName'];
		}
	
	}
	
?>	
