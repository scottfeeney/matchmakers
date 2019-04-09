<?php
	
	namespace Classes;
	
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	} else {
		require_once './config.php';
		require_once './classes/object_save.php';
	}
	
	class Skill {
	
		public $skillId;
		public $skillCategoryId;
		public $skillName;

		
		public function __construct($skillId = 0) {
        
			if ($skillId != 0) {
			
				$sql = "select * from skill where SkillId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $skillId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					Skill::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->skillId = 0;
			}
		}
		
		
		public function Save($user) {
		
			$errorMessage = "";
			$objectId = $this->skillId;
			
			
			if ($this->skillName == "") {
				$errorMessage = "Please enter a Skill Name";
			}
			
			
			if ($errorMessage == "" && Skill::GetSkillExists($this)) {
				$errorMessage = "A skill with the same name already exists in this category";
			}
			
			if ($errorMessage == "") {
		
				if ($this->skillId == 0) {
					
					// Insert skill
					
					
						
					$sql = "insert into skill";
					$sql .= " (SkillCategoryId, SkillName,";
					$sql .= " Created, CreatedBy)";
					$sql .= " values";
					$sql .= " (?, ?,";
					$sql .= " UTC_TIMESTAMP(), ?)";

					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isi", $this->skillCategoryId, $this->skillName, $user->userId);
						$stmt->execute();
						$objectId = $stmt->insert_id;
					} 
					else {
						$errorMessage = $conn->errno . ' ' . $conn->error;
					}
					
					$stmt->close();
					$conn->close();
				
					
				
				}
				else {

					// Edit skill
					
					$sql = "update skill";
					$sql .= " set";
					$sql .= " SkillCategoryId = ?,";
					$sql .= " SkillName = ?,";
					$sql .= " Modified = UTC_TIMESTAMP(),";
					$sql .= " ModifiedBy = ?";
					$sql .= " where SkillId = ?";

					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("isii", $this->skillCategoryId, $this->skillName, $user->userId, $this->skillId);
						$stmt->execute();
					} 
					else {
						$errorMessage = $conn->errno . ' ' . $conn->error;
					}
					
					$stmt->close();
					$conn->close();

				}
				
			}
			
			//return object
			return new \Classes\ObjectSave($errorMessage, $objectId);
		
		}
		
		// Get All skills by Category
		
		public static function DeleteSkill($skillId) {
			
			$skills = Array();
			
			//will need to delete children
			
			$sql = "delete from skill where SkillId = ?;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $skillId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			//TODO: some object to indicate success
			return new \Classes\ObjectSave("", 0);
		}
	
		
		// Get All skills by Category
		
		public static function GetSkillsBySkillCategory($skillCategoryId) {
			
			$skills = Array();
			
			$sql = "select * from skill where SkillCategoryId = ? order by SkillName;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $skillCategoryId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$skill = new Skill();
				Skill::LoadObject($skill, $row);
				$skills[] = $skill;
			}
			
			return $skills;
			
		}
		
		private static function GetSkillExists($object) {
			
			$sql = "select * from skill where SkillCategoryId = ? and SkillName = ? and SkillId <> ?";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("isi", $object->skillCategoryId, $object->skillName, $object->skillId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 0) {
				return false;
			}
			else {
				return true;
			}
			
		}

		
		private static function LoadObject($object, $row) {
			$object->skillId = $row['SkillId'];
			$object->skillCategoryId = $row['SkillCategoryId'];
			$object->skillName = $row['SkillName'];
		}
	
	}
	
?>	
