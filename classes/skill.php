<?php
	
	//----------------------------------------------------------------
	// Skill class - performs operations for Skill object
	// and skill related functionality
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
	
	class Skill {
	
		public $skillId;
		public $skillCategoryId;
		public $skillName;

		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/			
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
		
		// Save Object
		public function Save($user) {
		
			$errorMessage = "";
			$objectId = $this->skillId;
			

			// check for errors
			if ($this->skillName == "") {
				$errorMessage = "Please enter a Skill Name";
			}
			
			if ($errorMessage == "" && Skill::GetSkillExists($this)) {
				$errorMessage = "A skill with the same name already exists in this category";
			}
			
			if ($user->userType != 3) {
				$errorMessage = "Only admin users may add skills";
			}

			if ($errorMessage == "") {
		
				// Insert skill
				if ($this->skillId == 0) {
						
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

						if ($objectId === 0) {
							$errorMessage = "Error saving object";
						}
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
		

		/*
		* DeleteSkill deletes a skill
		*/	
		public static function DeleteSkill($skillId, $deleteReferencedSkill = false) {

			$skills = Array();
			
			/*
			* Only delete skill that is in use currently if user indicates that they are
			* aware skill is in use and still want to delete it
			*/
			if ($deleteReferencedSkill) {

				/*
				 * Will need to delete any children
				 */
				 
				$sql = "delete from job_skill where skillId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $skillId);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				//No point checking for success via rowsAffected here - can legitimately be zero

				$sql = "delete from job_seeker_skill where skillId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("i", $skillId);
				$stmt->execute();
				$result = mysqli_stmt_get_result($stmt);
				$stmt->close();
				//No point checking for success via rowsAffected here - can legitimately be zero
			}

			//Then can delete actual skill record
			
			$sql = "delete from skill where SkillId = ?;";

			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $skillId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			

			//Indicate success or failure
			if (mysqli_affected_rows($conn) == 1) {
				$stmt->close();
				$conn->close();
				return new \Classes\ObjectSave("", 0);
			} else {
				$stmt->close();
				$conn->close();
				return new \Classes\ObjectSave("Could not delete skill with id ".$skillId, 0);
			}
		}
	
		/*
		* GetSkillsBySkillCategory returns an array of skills by category
		*/	
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
		

		/*
		* GetSkillsByJob returns an array of skills by job
		*/
		public static function GetSkillsByJob($jobId) {
			
			$skills = Array();
			
			$sql = "select s.* from skill s";
			$sql .= " inner join job_skill js on s.SkillId = js.SkillId";
			$sql .= " where js.JobId = ?";
			$sql .= " order by s.SkillName";

				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $jobId);
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
		

		/*
		* GetSkillsByJobSeeker returns an array of skills by job seeker
		*/
		public static function GetSkillsByJobSeeker($jobSeekerId) {
			
			$skills = Array();
			
			$sql = "select s.* from skill s";
			$sql .= " inner join job_seeker_skill jss on s.SkillId = jss.SkillId";
			$sql .= " where jss.JobSeekerId = ?";
			$sql .= " order by s.SkillName";

				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $jobSeekerId);
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
		
		/*
		* GetSkillExists - checks if skill already exists
		*/
		public static function GetSkillExists($object) {
			
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

		// populate object from database row
		private static function LoadObject($object, $row) {
			$object->skillId = $row['SkillId'];
			$object->skillCategoryId = $row['SkillCategoryId'];
			$object->skillName = $row['SkillName'];
		}
	
	}
	
?>	
