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
	$givenname = "";
	$surname = "";
	$phone = "";
	$otherGivenname = "";
	$otherSurname = "";
	$otherPhone = "";
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$companyName = \Utilities\Common::GetRequest("CompanyName");
		$locationId = \Utilities\Common::GetRequest("LocationId");
		$givenname = \Utilities\Common::GetRequest("Givenname");
		$surname = \Utilities\Common::GetRequest("Surname");
		$phone = \Utilities\Common::GetRequest("Phone");
		$otherGivenname = \Utilities\Common::GetRequest("OtherGivenname");
		$otherSurname = \Utilities\Common::GetRequest("OtherSurname");
		$otherPhone = \Utilities\Common::GetRequest("OtherPhone");
		
		if ($companyName == "") {
			$errorMessages[] = "Please enter a Company Name";
		}
		
		if ($locationId == "") {
			$errorMessages[] = "Please select a location";
		}
		
		if ($givenname == "") {
			$errorMessages[] = "Please enter your First Name";
		}
		
		if ($surname == "") {
			$errorMessages[] = "Please enter your Last Name";
		}
		
		if ($phone == "") {
			$errorMessages[] = "Please enter your Phone Number";
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
			$employer->givenname = $givenname;
			$employer->surname = $surname;
			$employer->phone = $phone;
			$employer->otherGivenname = $otherGivenname;
			$employer->otherSurname = $otherSurname;
			$employer->otherPhone = $otherPhone;
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
			$givenname = $employer->givenname;
			$surname = $employer->surname;
			$phone = $employer->phone;
			$otherGivenname = $employer->otherGivenname;
			$otherSurname = $employer->otherSurname;
			$otherPhone = $employer->otherPhone;
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
					<label for="CompanyName">*Company Name:</label>
					<input type="text" class="form-control" name="CompanyName" id="CompanyName" maxlength="100" value="<?php echo htmlspecialchars($companyName) ?>"  required>
				</div>
				
				<div class="form-group">
					<label for="Location">*Location:</label>
					<select name="LocationId" id="LocationId" class="form-control" required>
						<option value=""></option>
						<?php foreach ($locations as $location) { ?>
							<option value="<?php echo $location->locationId; ?>" <?php if ($location->locationId == $locationId) {echo "selected";} ?>><?php echo $location->name; ?></option>
						<?php } ?>
					</select>
				</div>
				
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="Givenname">*First Name:</label>
							<input type="text" class="form-control" name="Givenname" id="Givenname" maxlength="30" value="<?php echo htmlspecialchars($givenname) ?>" required>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="Surname">*Last Name:</label>
							<input type="text" class="form-control" name="Surname" id="Surname" maxlength="30" value="<?php echo htmlspecialchars($surname) ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="Phone">*Phone Number:</label>
					<input type="tel" class="form-control" name="Phone" id="Phone" maxlength="20" value="<?php echo htmlspecialchars($phone) ?>">
				</div>
				
				<p><strong>Secondary Contact Person</strong></p>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="OtherGivenname">First Name:</label>
							<input type="text" class="form-control" name="OtherGivenname" id="OtherGivenname" maxlength="30" value="<?php echo htmlspecialchars($otherGivenname) ?>">
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="Surname">Last Name:</label>
							<input type="text" class="form-control" name="OtherSurname" id="OtherSurname" maxlength="30" value="<?php echo htmlspecialchars($otherSurname) ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="OtherPhone">Phone Number:</label>
					<input type="tel" class="form-control" name="OtherPhone" id="OtherPhone" maxlength="20" value="<?php echo htmlspecialchars($otherPhone) ?>">
				</div>
							
				<button type="submit" class="btn btn-primary">Save</button>  
			
			</form>
			

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
