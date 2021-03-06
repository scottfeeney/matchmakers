<?php
	
	//----------------------------------------------------------------
	// Location class - performs operations for Location object
	//----------------------------------------------------------------
	
	namespace Classes;
	
	// include required php file, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
	} else {
		require_once './config.php';
	}
	
	class Location {
	
		public $locationId;
		public $name;

		/*
		* Constructor: initialise data members based on supplied Id
		* 0: initialise empty object
		*/	
		public function __construct($locationId = 0) {
        
			if ($locationId != 0) {
			
				$sql = "select * from location where LocationId = ?";
				
				$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
				
				if($stmt = $conn->prepare($sql)) {
					$stmt->bind_param("i", $locationId);
					$stmt->execute();
					$result = mysqli_stmt_get_result($stmt);
					
					$row = mysqli_fetch_array($result);
					
					Location::LoadObject($this, $row);
				} 
				else {
					$errorMessage = $conn->errno . ' ' . $conn->error;
				}
				
				$stmt->close();
				$conn->close();
				
			}
			else {
				$this->locationId = 0;
			}
		}
	
		
		/*
		* GetLocations returns an array of all locations
		*/	
		public static function GetLocations() {
			
			$locations = Array();
			
			$sql = "select * from location order by Name;";
				
			$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
			
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			$result = mysqli_stmt_get_result($stmt);
			$stmt->close();
			$conn->close();
			
			while($row = mysqli_fetch_array($result))
			{
				$location = new Location();
				Location::LoadObject($location, $row);
				$locations[] = $location;
			}
			
			return $locations;
			
		}

		// populate object from database row
		private static function LoadObject($object, $row) {
			$object->locationId = $row['LocationId'];
			$object->name = $row['Name'];
		}
	
	}
	
?>	
