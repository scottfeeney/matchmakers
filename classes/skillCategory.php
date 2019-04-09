<?php
	
	namespace Classes;
	
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
	} else {
		require_once './config.php';
	}
	
	class SkillCategory {
	
		public $skillCategoryId;
		public $skillCategoryName;

		
		public function __construct($skillCategoryId = 0) {
        
			if ($skillCategoryId != 0) {
			
				$sql = "select * from skillCategory where skillCategoryId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $skillCategoryId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					SkillCategory::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->skillCategoryId = 0;
			}
		}
	
		
		// Get All industries/skill categories
		
		public static function GetSkillCategories() {
			
			$skillCategory = Array();
			
			$sql = "select * from skillCategory order by skillCategoryName;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$skillCategory = new SkillCategory();
				SkillCategory::LoadObject($skillCategory, $row);
				$skillCategory[] = $skillCategory;
			}
			
			return $skillCategory;
			
		}

		
		private static function LoadObject($object, $row) {
			$object->skillCategoryId = $row['skillCategoryId'];
			$object->skillCategoryName = $row['skillCategoryName'];
		}
	
	}
	
?>	
