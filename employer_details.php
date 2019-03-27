<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
	} else {
		require_once './wwwroot/utilities/common.php';
		require_once './wwwroot/classes/header.php';
		require_once './wwwroot/classes/footer.php';
		require_once './wwwroot/classes/location.php';
		require_once './wwwroot/classes/employer.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 1) {
		// not employer, send them back to home
        header("Location: home.php");
		die();				
	}
	
	$employer = \Classes\Employer::GetEmployerByUserId($user->userId);
	
	
	// empty fields
	$errorMessages = [];
	$companyName = "";
	$locationId = 0;
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$companyName = \Utilities\Common::GetRequest("CompanyName");
		$locationId = \Utilities\Common::GetRequest("LocationId");
		
		if ($companyName == "") {
			$errorMessages[] = "Please enter a Company Name";
		}
		
		if ($locationId == "") {
			$errorMessages[] = "Please select a location";
		}
		
		
		if (count($errorMessages) == 0) {
		
			// save employer
			
			if ($employer == null) {
				// employer not saved previously
				$employer = new \Classes\Employer();
			}
			
			$employer->userId = $user->userId;
			$employer->companyName = $companyName;
			$employer->locationId = $locationId;
			$objectSave = $employer->Save();
			
			if ($objectSave->hasError) {
				$errorMessages[] = $objectSave->errorMessage;
			}
			else {
				
				//update user object enteredDetails fields

				if ($user->enteredDetails == false) {
					$user->enteredDetails = true;
					$objectSave = $user->Save();
					if ($objectSave->hasError) {
						$errorMessages[] = $objectSave->errorMessage;
					}
				}
				
				
			}
			
			
			if (count($errorMessages) == 0) {
				//no errors, send to home page;
				header("Location: home.php");
				die();	
			}
		}	
		
	}
	else {
	
		//first load - load data if employer already saved
	
		if ($employer != null) {
			$companyName = $employer->companyName;
			$locationId = $employer->locationId;
		}
	}
	
	//get locations list for dropdown
	$locations = \Classes\Location::GetLocations();
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>Employer Details</h2>
			
			<p>Please enter your details below.</p>
			
			<form action="employer_details.php" method="post">
			
				<input type="hidden" name="SubmitForm" value="1">
				
				<?php if (count($errorMessages) > 0) { ?>
					<div class="alert alert-danger" role="alert"><?php echo join("<br />", $errorMessages); ?></div>
				<?php } ?>
		
				
				<div class="form-group">
					<label for="CompanyName">Company Name:</label>
					<input type="text" class="form-control" name="CompanyName" id="CompanyName" maxlength="100" value="<?php echo htmlspecialchars($companyName) ?>">
				</div>
				
				<div class="form-group">
					<label for="Location">Location:</label>
					<select name="LocationId" id="LocationId" class="form-control">
						<option value=""></option>
						
						<?php foreach ($locations as $location) { ?>
							<option value="<?php echo $location->locationId; ?>" <?php if ($location->locationId == $locationId) {echo "selected";} ?>><?php echo $location->name; ?></option>
						<?php } ?>
						
						
					</select>
				</div>
							
				<button type="submit" class="btn btn-primary">Save</button>  
			
			</form>
			

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
