<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/location.php';
		require_once './classes/employer.php';
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
	$title = "";
	$firstName = "";
	$lastName = "";
	$phoneAreaCode = "";
	$phoneNumber = "";
	$mobileNumber = "";
	$otherTitle = "";
	$otherFirstName = "";
	$otherLastName = "";
	$otherPhoneAreaCode = "";
	$otherPhoneNumber = "";
	$address1 = "";
	$address2 = "";
	$city = "";
	$state = "";
	$postcode = "";
	$companyType = "";
	$companySize = "";
	$expectedGrowth = "";
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$companyName = \Utilities\Common::GetRequest("CompanyName");
		$locationId = \Utilities\Common::GetRequest("LocationId");
		$title = \Utilities\Common::GetRequest("Title");
		$firstName = \Utilities\Common::GetRequest("FirstName");
		$lastName = \Utilities\Common::GetRequest("LastName");
		$phoneAreaCode = \Utilities\Common::GetRequest("PhoneAreaCode");
		$phoneNumber = \Utilities\Common::GetRequest("PhoneNumber");
		$mobileNumber = \Utilities\Common::GetRequest("MobileNumber");
		$otherTitle = \Utilities\Common::GetRequest("OtherTitle");
		$otherFirstName = \Utilities\Common::GetRequest("OtherFirstName");
		$otherLastName = \Utilities\Common::GetRequest("OtherLastName");
		$otherPhoneAreaCode = \Utilities\Common::GetRequest("OtherPhoneAreaCode");
		$otherPhoneNumber = \Utilities\Common::GetRequest("OtherPhoneNumber");
		$address1 = \Utilities\Common::GetRequest("Address1");
		$address2 = \Utilities\Common::GetRequest("Address2");
		$city = \Utilities\Common::GetRequest("City");
		$state = \Utilities\Common::GetRequest("State");
		$postcode = \Utilities\Common::GetRequest("Postcode");
		$companyType = \Utilities\Common::GetRequest("CompanyType");
		$companySize = \Utilities\Common::GetRequest("CompanySize");
		$expectedGrowth = \Utilities\Common::GetRequest("ExpectedGrowth");

		
		
		if ($title == "") {
			$errorMessages[] = "Please select your Title";
		}
		
		if ($firstName == "") {
			$errorMessages[] = "Please enter your First Name";
		}
		
		if ($lastName == "") {
			$errorMessages[] = "Please enter your Last Name";
		}
		
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
			$errorMessages[] = "Your phone number must be 8 digits long";
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
		
				
		if ($companyName == "") {
			$errorMessages[] = "Please enter a Company Name";
		}
		
		if ($companyType == "") {
			$errorMessages[] = "Please select a Company Type";
		}
		
		if ($companySize == "") {
			$errorMessages[] = "Please select a Company Size";
		}
		
		if ($expectedGrowth == "") {
			$errorMessages[] = "Please select an Expected Growth";
		}
		
		if ($locationId == "") {
			$errorMessages[] = "Please select a Location";
		}
		
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
		
		if($otherPhoneAreaCode != ''){
			if(strlen($otherPhoneAreaCode) <> 2){
				$errorMessages[] = "Your area code has to be 2 characters (eg 02)";
				$otherPhoneAreaCode = "";
			}
			
			if(ctype_digit($otherPhoneAreaCode)){
			}else{$errorMessages[] = "Your area code can only be numbers";
				$otherPhoneAreaCode = "";
			}
		}
		
		if($otherPhoneNumber != ''){
			if(strlen($otherPhoneNumber) <> 8){
				$errorMessages[] = "Your secondary phone number must be 8 digits long";
				$otherPhoneNumber = "";
			}
			
			if(ctype_digit($otherPhoneNumber)){
			}else{$errorMessages[] = "Your secondary phone number can only be numbers";
				$otherPhoneNumber = "";
			}
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
			$employer->title = $title;
			$employer->firstName = $firstName;
			$employer->lastName = $lastName;
			$employer->phoneAreaCode = $phoneAreaCode;
			$employer->phoneNumber = $phoneNumber;
			$employer->mobileNumber = $mobileNumber;
			$employer->otherTitle = $otherTitle;
			$employer->otherFirstName = $otherFirstName;
			$employer->otherLastName = $otherLastName;
			$employer->otherPhoneAreaCode = $otherPhoneAreaCode;
			$employer->otherPhoneNumber = $otherPhoneNumber;
			$employer->address1 = $address1;
			$employer->address2 = $address2;
			$employer->city = $city;
			$employer->state = $state;
			$employer->postcode = $postcode;
			$employer->companyType = $companyType;
			$employer->companySize = $companySize;
			$employer->expectedGrowth = $expectedGrowth;
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
			$title = $employer->title;
			$firstName = $employer->firstName;
			$lastName = $employer->lastName;
			$phoneAreaCode = $employer->phoneAreaCode;
			$phoneNumber = $employer->phoneNumber;
			$mobileNumber = $employer->mobileNumber;
			$otherTitle = $employer->otherTitle;
			$otherFirstName = $employer->otherFirstName;
			$otherLastName = $employer->otherLastName;
			$otherPhoneAreaCode = $employer->otherPhoneAreaCode;
			$otherPhoneNumber = $employer->otherPhoneNumber;
			$address1 = $employer->address1;
			$address2 = $employer->address2;
			$city = $employer->city;
			$state = $employer->state;
			$postcode = $employer->postcode;
			$companyType = $employer->companyType;
			$companySize = $employer->companySize;
			$expectedGrowth = $employer->expectedGrowth;
		}
	}
	
	//get arrys list for dropdown
	$locations = \Classes\Location::GetLocations();
	$titles = \Classes\Employer::GetTitles() ;
	$states = \Classes\Employer::GetStates() ;
	$companyTypes = \Classes\Employer::GetCompanyTypes() ;
	$companySizes = \Classes\Employer::GetCompanySizes() ;
	$expectedGrowths = \Classes\Employer::GetExpectedGrowths() ;
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>Employer Details</h2>
			
			<p>Please enter your details below.</p>
			
			<form action="employer_details.php" method="post" class="needs-validation" novalidate>
			
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
				
				<div class="form-section">Company Details</div>
				
				<div class="form-group">
					<label for="CompanyName">*Company Name:</label>
					<input type="text" class="form-control" name="CompanyName" id="CompanyName" maxlength="100" value="<?php echo htmlspecialchars($companyName) ?>"  required>
					<div class="invalid-feedback">Please enter a Company Name</div>
				</div>
				
				<div class="form-group">
					<label for="CompanyType">*Company Type:</label>
					<select name="CompanyType" id="CompanyType" class="form-control" required>
						<option value=""></option>
						<?php foreach ($companyTypes as $companyTypeItem) { ?>
							<option value="<?php echo $companyTypeItem; ?>" <?php if ($companyTypeItem == $companyType) {echo "selected";} ?>><?php echo $companyTypeItem; ?></option>
						<?php } ?>
					</select>
					<div class="invalid-feedback">Please select a Company Type</div>
				</div>
				
				<div class="form-group">
					<label for="CompanySize">*Company Size:</label>
					<select name="CompanySize" id="CompanySize" class="form-control" required>
						<option value=""></option>
						<?php foreach ($companySizes as $companySizeItem) { ?>
							<option value="<?php echo $companySizeItem; ?>" <?php if ($companySizeItem == $companySize) {echo "selected";} ?>><?php echo $companySizeItem; ?></option>
						<?php } ?>
					</select>
					<div class="invalid-feedback">Please select a Company Size</div>
				</div>
				
				<div class="form-group">
					<label for="ExpectedGrowth">*Expected Growth Next 12 Months:</label>
					<select name="ExpectedGrowth" id="ExpectedGrowth" class="form-control" required>
						<option value=""></option>
						<?php foreach ($expectedGrowths as $expectedGrowthItem) { ?>
							<option value="<?php echo $expectedGrowthItem; ?>" <?php if ($expectedGrowthItem == $expectedGrowth) {echo "selected";} ?>><?php echo $expectedGrowthItem; ?></option>
						<?php } ?>
					</select>
					<div class="invalid-feedback">Please select an Expected Growth</div>
				</div>
				
				<div class="form-group">
					<label for="Location">*Location:</label>
					<select name="LocationId" id="LocationId" class="form-control" required>
						<option value=""></option>
						<?php foreach ($locations as $location) { ?>
							<option value="<?php echo $location->locationId; ?>" <?php if ($location->locationId == $locationId) {echo "selected";} ?>><?php echo $location->name; ?></option>
						<?php } ?>
					</select>
					<div class="invalid-feedback">Please select a Location</div>
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
				
				
				<div class="form-section">Secondary Contact Person</div>
				
				<div class="row">
					<div class="col-sm-2">
						<div class="form-group">
							<label for="OtherTitle">Title:</label>
							<select name="OtherTitle" id="OtherTitle" class="form-control">
								<option value=""></option>
								<?php foreach ($titles as $titleItem) { ?>
									<option value="<?php echo $titleItem; ?>" <?php if ($titleItem == $otherTitle) {echo "selected";} ?>><?php echo $titleItem; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="OtherFirstName">First Name:</label>
							<input type="text" class="form-control" name="OtherFirstName" id="OtherFirstName" maxlength="50" value="<?php echo htmlspecialchars($otherFirstName) ?>">
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="OtherLastName">Last Name:</label>
							<input type="text" class="form-control" name="OtherLastName" id="OtherLastName" maxlength="50" value="<?php echo htmlspecialchars($otherLastName) ?>">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-2">				
						<div class="form-group">
							<label for="OtherPhoneAreaCode">Area Code:</label>
							<input type="tel" class="form-control" name="OtherPhoneAreaCode" id="OtherPhoneAreaCode" maxlength="2" value="<?php echo htmlspecialchars($otherPhoneAreaCode) ?>">
						</div>
					</div>
					<div class="col-sm-10">						
						<div class="form-group">
							<label for="OtherPhoneNumber">Phone Number:</label>
							<input type="tel" class="form-control" name="OtherPhoneNumber" id="OtherPhoneNumber" maxlength="8" value="<?php echo htmlspecialchars($otherPhoneNumber) ?>">
						</div>
					</div>
				</div>
				
				<div class="form-group mt-3">
					<button type="submit" class="btn btn-primary">Save</button>  
				<div>
				
			</form>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
