<?php
	
	//----------------------------------------------------------------
	// SkillCategory class - performs operations for Skill category 
	// object
	//----------------------------------------------------------------
	
	namespace Classes;
	
	// include required php file, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
	} else {
		require_once './config.php';
	}
	
	class SkillCategory {
	
		public $skillCategoryId;
		public $skillCategoryName;

		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/			
		public function __construct($skillCategoryId = 0) {
        
			if ($skillCategoryId != 0) {
			
				$sql = "select * from skill_category where SkillCategoryId = ?";
				
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
	
		
		/*
		* GetSkillCategories returns an array of all skill categories
		*/	
		public static function GetSkillCategories() {
			
			$skillCategories = Array();
			
			$sql = "select * from skill_category order by SkillCategoryName;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			if($stmt = $conn->prepare($sql)) {
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				
				while($row = mysqli_fetch_array($result))
				{
					$skillCategory = new SkillCategory();
					SkillCategory::LoadObject($skillCategory, $row);
					$skillCategories[] = $skillCategory;
				}
			} else {
				$errorMessage = $conn->errno . ' ' . $conn->error;
				echo $errorMessage;
			}
			$stmt->close();
			$conn->close();
			
			return $skillCategories;
			
		}

		// populate object from database row
		private static function LoadObject($object, $row) {
			$object->skillCategoryId = $row['SkillCategoryId'];
			$object->skillCategoryName = $row['SkillCategoryName'];
		}
	
	}
	
?>	
