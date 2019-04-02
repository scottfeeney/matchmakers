<?php
	
	namespace Classes;
	
	
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
    
        public function __construct($adminStaffId = 0) {
            
            if ($adminStaffId != 0) {

                $sql = "select * from adminStaff where adminStaffId = ?";

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

        private static function LoadObject($object, $row) {

            $object->adminStaffId = $row['adminStaffId'];
			$object->userId = $row['UserId'];
			
			$object->firstName = $row['FirstName'];
			$object->lastName = $row['LastName'];

        }

        public function Save() {
		
			$errorMessage = "";
			$objectId = $this->adminStaffId;
			
		
			if ($this->adminStaffId == 0) {
				
				// Insert adminStaff
						
				if ($errorMessage == "") {
					$sql = "insert into adminStaff";
					$sql .= " (adminStaffId, UserId, firstName, lastName, created, modified)";
					$sql .= " values";
					$sql .= " (?, ?, ?, ?, UTC_TIMESTAMP(), UTC_TIMESTAMP())";
	
					$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
					
					if($stmt = $conn->prepare($sql)) {
						$stmt->bind_param("iiss", $this->adminStaffId, $this->userId, $this->firstName, $this->lastName);
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

				// Edit existing adminStaff record
							
				$sql = "update adminStaff";
                $sql .= " set";
				$sql .= " UserId = ?,";
				$sql .= " FirstName = ?,";
				$sql .= " LastName = ?,";
				$sql .= " Modified = UTC_TIMESTAMP()";
				$sql .= " where adminStaffId = ?";

				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("issi", $this->userId, $this->firstName, $this->lastName, $this->adminStaffId);
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