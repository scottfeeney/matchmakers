<?php
	
	//----------------------------------------------------------------
	// ObjectSave class - helper class to return errors and created Id
	// back to client when saving
	//----------------------------------------------------------------
	
	namespace Classes;

	class ObjectSave {
	
		public $hasError;
		public $errorMessage;
		public $objectId;

		public function __construct($errorMessage, $objectId) {
			
			if ($errorMessage == "") {
				$this->hasError = false;
				$this->objectId = $objectId;
			}
			else {
				$this->hasError = true;
				$this->errorMessage = $errorMessage;
			}
		}
	
	}
	
?>	