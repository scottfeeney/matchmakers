<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/location.php';
		require_once './classes/jobseeker.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 2) {
		// not job seeker, send them back to home
        header("Location: home.php");
		die();				
	}
	
	$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
	

	// empty fields
	$errorMessages = [];
	
	$title = "";
	$firstName = "";
	$lastName = "";
	
	$phoneAreaCode = "";
	$phoneNumber = "";
	$mobileNumber = "";
	
	$address1 = "";
	$address2 = "";
	$city = "";
	$state = "";
	$postcode = "";
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$title = \Utilities\Common::GetRequest("Title");
		$firstName = \Utilities\Common::GetRequest("FirstName");
		$lastName = \Utilities\Common::GetRequest("LastName");
		
		$phoneAreaCode = \Utilities\Common::GetRequest("PhoneAreaCode");
		$phoneNumber = \Utilities\Common::GetRequest("PhoneNumber");
		$mobileNumber = \Utilities\Common::GetRequest("MobileNumber");
		
		$address1 = \Utilities\Common::GetRequest("Address1");
		$address2 = \Utilities\Common::GetRequest("Address2");
		$city = \Utilities\Common::GetRequest("City");
		$state = \Utilities\Common::GetRequest("State");
		$postcode = \Utilities\Common::GetRequest("Postcode");

		// Name/Title validation
		if ($title == "") {
			$errorMessages[] = "Please select your Title";
		}
		
		if ($firstName == "") {
			$errorMessages[] = "Please enter your First Name";
		}
		
		if ($lastName == "") {
			$errorMessages[] = "Please enter your Last Name";
		}
		
		// Phone number validation
		if ($phoneAreaCode == "") {
			$errorMessages[] = "Please enter your Area Code";
		}
		
		if(strlen($phoneAreaCode) <> 2){
			$errorMessages[] = "Your area code has to be 2 characters (eg 02)";
			$phoneAreaCode = "";
		}
		
		if(ctype_digit($phoneAreaCode)){
		}else{$errorMessages[] = "Your area code can only be numbers";
			$phoneAreaCode = "";
		}
		
		
		if ($phoneNumber == "") {
			$errorMessages[] = "Please enter your Phone Number";
		}
		
		if(strlen($phoneNumber) <> 8){
			$errorMessages[] = "Your phone nuber must be 8 digits long";
			$phoneNumber = "";
		}
		
		if(ctype_digit($phoneNumber)){
		}else{$errorMessages[] = "Your phone number can only be numbers";
			$phoneNumber = "";
		}
		
		if ($mobileNumber == "") {
			$errorMessages[] = "Please enter your Mobile Number";
		}
		
		if(ctype_digit($mobileNumber)){
		}else{$errorMessages[] = "Your mobile number can only be numbers";
			$mobileNumber = "";
		}
		
		if(strlen($mobileNumber) <> 10){
			$errorMessages[] = "Your mobile number must be 8 digits long";
			$mobileNumber = "";
		}
		
		// Address validation
		if ($address1 == "") {
			$errorMessages[] = "Please enter a Street Address 1";
		}
		
		if ($city == "") {
			$errorMessages[] = "Please enter a City";
		}
		
		if ($state == "") {
			$errorMessages[] = "Please select a State";
		}
		
		if ($postcode == "") {
			$errorMessages[] = "Please enter a Postcode";
		}
		
		if(ctype_digit($postcode)){
		}else{$errorMessages[] = "Your post code can only be numbers";
			$postcode = "";
		}
		
		if(strlen($postcode) <> 4){
		$errorMessages[] = "Post code must be 4 digits long";
			$postcode = "";	
		}
		
		if (count($errorMessages) == 0) {
		
			// save job seeker
			
			if ($jobSeeker == null) {
				// job seeker not saved previously
				$jobSeeker = new \Classes\JobSeeker();
			}
			
			$jobSeeker->userId = $user->userId;
			$jobSeeker->title = $title;
			$jobSeeker->firstName = $firstName;
			$jobSeeker->lastName = $lastName;
			$jobSeeker->phoneAreaCode = $phoneAreaCode;
			$jobSeeker->phoneNumber = $phoneNumber;
			$jobSeeker->mobileNumber = $mobileNumber;
			$jobSeeker->address1 = $address1;
			$jobSeeker->address2 = $address2;
			$jobSeeker->city = $city;
			$jobSeeker->state = $state;
			$jobSeeker->postcode = $postcode;
			$objectSave = $jobSeeker->Save();
			
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
	
		if ($jobSeeker != null) {
			$title = $jobSeeker->title;
			$firstName = $jobSeeker->firstName;
			$lastName = $jobSeeker->lastName;
			$phoneAreaCode = $jobSeeker->phoneAreaCode;
			$phoneNumber = $jobSeeker->phoneNumber;
			$mobileNumber = $jobSeeker->mobileNumber;
			$address1 = $jobSeeker->address1;
			$address2 = $jobSeeker->address2;
			$city = $jobSeeker->city;
			$state = $jobSeeker->state;
			$postcode = $jobSeeker->postcode;
			
		}
	}
	
	//get arrys list for dropdown
	//$locations = \Classes\Location::GetLocations();
	$titles = \Classes\JobSeeker::GetTitles() ;
	$states = \Classes\JobSeeker::GetStates() ;
	// $companyTypes = \Classes\Employer::GetCompanyTypes() ;
	// $companySizes = \Classes\Employer::GetCompanySizes() ;
	// $expectedGrowths = \Classes\Employer::GetExpectedGrowths() ;
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>Job Seeker Details</h2>
			
			<p>Please enter your details below.</p>
			
			<form action="job_seeker_details.php" method="post" class="needs-validation" novalidate>
			
				<input type="hidden" name="SubmitForm" value="1">
				
				<?php if (count($errorMessages) > 0) { ?>
					<div class="alert alert-danger" role="alert"><?php echo join("<br />", $errorMessages); ?></div>
				<?php } ?>
				
				<div class="form-section">Your Details</div>
				
				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
							<label for="Title">*Title:</label>
							<select name="Title" id="Title" class="form-control" required>
								<option value=""></option>
								<?php foreach ($titles as $titleItem) { ?>
									<option value="<?php echo $titleItem; ?>" <?php if ($titleItem == $title) {echo "selected";} ?>><?php echo $titleItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select your Title</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="FirstName">*First Name:</label>
							<input type="text" class="form-control" name="FirstName" id="FirstName" maxlength="50" value="<?php echo htmlspecialchars($firstName) ?>" required>
							<div class="invalid-feedback">Please enter your First Name</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="LastName">*Last Name:</label>
							<input type="text" class="form-control" name="LastName" id="LastName" maxlength="50" value="<?php echo htmlspecialchars($lastName) ?>" required>
							<div class="invalid-feedback">Please enter your Last Name</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
							<label for="PhoneAreaCode">*Area Code:</label>
							<input type="tel" class="form-control" name="PhoneAreaCode" id="PhoneAreaCode" maxlength="2" value="<?php echo htmlspecialchars($phoneAreaCode) ?>" required>
							<div class="invalid-feedback">Please enter your Area Code</div>
						</div>
					</div>
					<div class="col-sm-10">
						<div class="form-group">
							<label for="PhoneNumber">*Phone Number:</label>
							<input type="tel" class="form-control" name="PhoneNumber" id="PhoneNumber" maxlength="8" value="<?php echo htmlspecialchars($phoneNumber) ?>" required>
							<div class="invalid-feedback">Please enter your Phone Number</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="PhoneNumber">*Mobile Number:</label>
					<input type="tel" class="form-control" name="MobileNumber" id="MobileNumber" maxlength="10" value="<?php echo htmlspecialchars($mobileNumber) ?>" required>
					<div class="invalid-feedback">Please enter your Mobile Number</div>
				</div>
				
				<div class="form-section">Address</div>
				
				<div class="form-group">
					<label for="Address1">*Street Address 1:</label>
					<input type="text" class="form-control" name="Address1" id="Address1" maxlength="100" value="<?php echo htmlspecialchars($address1) ?>" required>
					<div class="invalid-feedback">Please enter a Street Address 1</div>
				</div>
				
				<div class="form-group">
					<label for="Address2">Street Address 2:</label>
					<input type="text" class="form-control" name="Address2" id="Address2" maxlength="100" value="<?php echo htmlspecialchars($address2) ?>">
				</div>	

				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="City">*City:</label>
							<input type="text" class="form-control" name="City" id="City" maxlength="100" value="<?php echo htmlspecialchars($city) ?>" required>
							<div class="invalid-feedback">Please enter a City</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label for="Title">*State:</label>
							<select name="State" id="State" class="form-control" required>
								<option value=""></option>
								<?php foreach ($states as $stateItem) { ?>
									<option value="<?php echo $stateItem; ?>" <?php if ($stateItem == $state) {echo "selected";} ?>><?php echo $stateItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a State</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label for="Postcode">*Postcode:</label>
							<input type="text" class="form-control" name="Postcode" id="Postcode" maxlength="6" value="<?php echo htmlspecialchars($postcode) ?>" required>
							<div class="invalid-feedback">Please enter a Postcode</div>
						</div>
					</div>
				</div>
				
				<div class="form-section">Skills</div>
				<h4>Coming Sprint 6</h4>
				
				<div class="form-section">Experience</div>
				<h4>Coming Sprint 6</h4>
				
				<div class="form-group mt-3">
					<button type="submit" class="btn btn-primary">Save</button>  
				<div>
				
			</form>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
