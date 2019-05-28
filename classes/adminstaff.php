<?php
	
	//----------------------------------------------------------------
	// AdminStaff class - performs operations for staff member object
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
	
	class AdminStaff {

        public $adminStaffId;
        public $userId;
        public $firstName;
        public $lastName;
    
		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/
        public function __construct($adminStaffId = 0) {
            
            if ($adminStaffId != 0) {

                $sql = "select * from admin_staff where AdminStaffId = ?";

                $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $adminStaffId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					AdminStaff::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
                }
                
				$stmt->close();
				$conn->close();

            } else {
                $this->adminStaffId = 0;
            }
			
        }

		// populate object from database row
        private static function LoadObject($object, $row) {

            $object->adminStaffId = $row['AdminStaffId'];
			$object->userId = $row['UserId'];
			$object->firstName = $row['FirstName'];
			$object->lastName = $row['LastName'];

        }
		
		/*
		* GetAdminStaffByUserId returns an AdminStaff object based on supplied UserId
		* returns null if not found
		*/
		public static function GetAdminStaffByUserId($userId) 
		{
			
			$sql = "select AdminStaffId from admin_staff where UserId = ?";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $userId);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			if ($result->num_rows == 1) {
				$row = mysqli_fetch_array($result);
				return new AdminStaff($row['AdminStaffId']);
			}
			
			return null;
			
		}
		

    } 
    
?>