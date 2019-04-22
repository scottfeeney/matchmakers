<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/location.php';
		require_once './classes/skillcategory.php';
		require_once './classes/jobseeker.php';
		require_once './classes/jobtype.php';
		require_once './classes/skill.php';
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
	
	//$fieldOfExpertise = "";
	$skillCategoryId = 0;
	$ageGroup = "";
	$highestLevelCompleted = "";
	$currentlyStudying = "NO";
	$currentStudyLevel = "";
	$signUpReason = "";
	$jobChangeSpeed = "";
	$jobType = "";
	$selectedSkills = "";
	
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
		
		//$fieldOfExpertise = \Utilities\Common::GetRequest("FieldOfExpertise");
		$skillCategoryId = \Utilities\Common::GetRequest("SkillCategoryId");
		$ageGroup = \Utilities\Common::GetRequest("AgeGroup");
		$highestLevelCompleted = \Utilities\Common::GetRequest("HighestLevelCompleted");
		$currentlyStudying = \Utilities\Common::GetRequest("CurrentlyStudying");
		$currentStudyLevel = \Utilities\Common::GetRequest("CurrentStudyLevel");
		$signUpReason = \Utilities\Common::GetRequest("SignUpReason");
		$jobChangeSpeed = \Utilities\Common::GetRequest("JobChangeSpeed");
		$jobType = \Utilities\Common::GetRequest("JobType");
		$selectedSkills = \Utilities\Common::GetRequest("SkillsControlSelectedSkills");

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
		}else{$errorMessages[] = "Your area code can only contain numbers";
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
			$errorMessages[] = "Your mobile number must be 10 digits long";
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
		}else{$errorMessages[] = "Your post code can only contain numbers";
			$postcode = "";
		}
		
		if(strlen($postcode) <> 4){
		$errorMessages[] = "Post code must be 4 digits long";
			$postcode = "";	
		}
		
		
		if ($skillCategoryId == 0) {
			$errorMessages[] = "Please select a Field of Expertise";
		}
		
		if ($ageGroup == "") {
			$errorMessages[] = "Please select an Age Group";
		}
		
		if ($highestLevelCompleted == "") {
			$errorMessages[] = "Please select a Highest level of education completed";
		}
		
		if (strtoupper($currentlyStudying) == "YES") {
			if ($currentStudyLevel == "") {
				$errorMessages[] = "Please select a Level of current study";
			}
		}
		
		if ($signUpReason == "") {
			$errorMessages[] = "Please select a Reason for Sign-up";
		}
		
		if ($jobChangeSpeed == "") {
			$errorMessages[] = "Please select a Speed of job change";
		}

		if ($jobType == "") {
			$errorMessages[] = "Please select a type of work";
		}
		
		if ($selectedSkills == "") {
			$errorMessages[] = "Please select at least one skill";
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
			$jobSeeker->skillCategoryId = $skillCategoryId;
			$jobSeeker->ageGroup = $ageGroup;
			$jobSeeker->highestLevelCompleted = $highestLevelCompleted;
			$jobSeeker->currentlyStudying = (strtoupper($currentlyStudying) == "YES" ? "YES" : "NO");
			$jobSeeker->currentStudyLevel = (strtoupper($currentlyStudying) == "YES" ? $currentStudyLevel : "");
			$jobSeeker->signUpReason = $signUpReason;
			$jobSeeker->jobChangeSpeed = $jobChangeSpeed;
			$jobSeeker->jobTypeId = $jobType;
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
				else {
			
			
					//save job seeker skills
					$jobSeekerId = $objectSave->objectId;
					
					\Classes\JobSeeker::SaveJobSeekerSkills($jobSeekerId, $selectedSkills);
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
			$skillCategoryId = $jobSeeker->skillCategoryId;
			$ageGroup = $jobSeeker->ageGroup;
			$highestLevelCompleted = $jobSeeker->highestLevelCompleted;
			$currentlyStudying = $jobSeeker->currentlyStudying;
			$currentStudyLevel = $jobSeeker->currentStudyLevel;
			$signUpReason = $jobSeeker->signUpReason;
			$jobChangeSpeed = $jobSeeker->jobChangeSpeed;
			$jobType = $jobSeeker->jobTypeId;
			$selectedSkills = \Classes\JobSeeker::GetSkillsByJobSeekerString($jobSeeker->jobSeekerId);
		}
	}
	
	//get arrys list for dropdown

	$titles = \Classes\JobSeeker::GetTitles() ;
	$states = \Classes\JobSeeker::GetStates() ;
	$skillCategories = \Classes\SkillCategory::GetSkillCategories();
	$ageGroups = \Classes\JobSeeker::GetAgeGroups();
	$educationLevels = \Classes\JobSeeker::GetEducationLevels();
	$signupReasons = \Classes\JobSeeker::GetSignupReasons();
	$jobChangeSpeeds = \Classes\JobSeeker::GetJobChangeSpeeds();
	$jobTypes = \Classes\JobType::GetJobTypes();

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
					<div class="col-sm-5">
						<div class="form-group">
							<label for="PhoneNumber">*Phone Number:</label>
							<input type="tel" class="form-control" name="PhoneNumber" id="PhoneNumber" maxlength="8" value="<?php echo htmlspecialchars($phoneNumber) ?>" required>
							<div class="invalid-feedback">Please enter your Phone Number</div>
						</div>
					</div>
					<div class="col-sm-5">
						<div class="form-group">
							<label for="PhoneNumber">*Mobile Number:</label>
							<input type="tel" class="form-control" name="MobileNumber" id="MobileNumber" maxlength="10" value="<?php echo htmlspecialchars($mobileNumber) ?>" required>
							<div class="invalid-feedback">Please enter your Mobile Number</div>
						</div>
					</div>
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
				
				<div class="form-section">Additional Info</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="SkillCategoryId">*Field of Expertise:</label>
							<select name="SkillCategoryId" id="SkillCategoryId" class="form-control skills-control-skills-category" required>
								<option value=""></option>
								<?php foreach ($skillCategories as $skillCategory) { ?>
									<option value="<?php echo $skillCategory->skillCategoryId; ?>" <?php if ($skillCategory->skillCategoryId == $skillCategoryId) {echo "selected";} ?>><?php echo $skillCategory->skillCategoryName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Field of Expertise</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="AgeGroup">*Age Group:</label>
							<select name="AgeGroup" id="AgeGroup" class="form-control" required>
								<option value=""></option>
								<?php foreach ($ageGroups as $ageGroupItem) { ?>
									<option value="<?php echo $ageGroupItem; ?>" <?php if ($ageGroupItem == $ageGroup) {echo "selected";} ?>><?php echo $ageGroupItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select an Age Group</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="HighestLevelCompleted">*Highest level of education completed:</label>
							<select name="HighestLevelCompleted" id="HighestLevelCompleted" class="form-control" required>
								<option value=""></option>
								<?php foreach ($educationLevels as $educationLevelItem) { ?>
									<option value="<?php echo $educationLevelItem; ?>" <?php if ($educationLevelItem == $highestLevelCompleted) {echo "selected";} ?>><?php echo $educationLevelItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Highest level of education completed</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="JobType">*Type of work sought:</label>
							<select name="JobType" id="JobType" class="form-control" required>
								<option value=""></option>
								<?php foreach ($jobTypes as $currJobType) { ?>
									<option value="<?php echo $currJobType->jobTypeId; ?>" <?php if ($currJobType->jobTypeId == $jobType) {echo "selected";} ?>><?php echo $currJobType->jobTypeName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a type of work</div>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<label>*Currently studying:</label>
					<div class="form-check-inline">
					  <label class="form-check-label">
						<input type="radio" class="form-check-input jobseeker-currently-studying-field" name="CurrentlyStudying" value="YES" <?php if ($currentlyStudying == "YES") { echo "checked"; } ?>> Yes
					  </label>
					</div>
					<div class="form-check-inline">
					  <label class="form-check-label">
						<input type="radio" class="form-check-input jobseeker-currently-studying-field" name="CurrentlyStudying" value="NO" <?php if ($currentlyStudying != "YES") { echo "checked"; } ?>> No
					  </label>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group job-seeker-current-study-level-group" <?php if ($currentlyStudying != "YES") { echo "style=\"display: none;\""; } ?>>
							<label for="CurrentStudyLevel">*Level of current study:</label>
							<select name="CurrentStudyLevel" id="CurrentStudyLevel" class="form-control">
								<option value=""></option>
								<?php foreach ($educationLevels as $educationLevelItem) { ?>
									<option value="<?php echo $educationLevelItem; ?>" <?php if ($educationLevelItem == $currentStudyLevel) {echo "selected";} ?>><?php echo $educationLevelItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Level of current study</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label for="SignUpReason">*Reason for Sign-up:</label>
							<select name="SignUpReason" id="SignUpReason" class="form-control" required>
								<option value=""></option>
								<?php foreach ($signupReasons as $signupReasonItem) { ?>
									<option value="<?php echo $signupReasonItem; ?>" <?php if ($signupReasonItem == $signUpReason) {echo "selected";} ?>><?php echo $signupReasonItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Reason for Sign-up</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label for="JobChangeSpeed">*Intended timeframe for switching jobs:</label>
							<select name="JobChangeSpeed" id="JobChangeSpeed" class="form-control" required>
								<option value=""></option>
								<?php foreach ($jobChangeSpeeds as $jobChangeSpeedItem) { ?>
									<option value="<?php echo $jobChangeSpeedItem; ?>" <?php if ($jobChangeSpeedItem == $jobChangeSpeed) {echo "selected";} ?>><?php echo $jobChangeSpeedItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select an intended timeframe for switching jobs</div>
						</div>
					</div>
				</div>
				
				<div class="row">		
					<div class="col-sm-12">
						<div class="form-group">
							<label>*Skills: (select your skills based on your Field of Expertise)</label>
							<div class="skills-control-wrapper none-selected">
								<div class="card">
									<div class="card-body">
										<div class="skills-control-spinner"><i class="fas fa-spinner fa-spin"></i></div>
										<div class="skills-control">
											<?php 
												if ($skillCategoryId != "") {
													$skills = \Classes\Skill::GetSkillsBySkillCategory($skillCategoryId);
													echo \Utilities\Common::GetSkillsControl($skills, $selectedSkills); 
												}
											?>
										</div>
									</div>
								</div>
								<div class="invalid-skills-control-feedback">Please select at least one skill</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group mt-3">
					<button type="submit" class="btn btn-primary">Save</button>  
				</div>
			</form>
		</section>
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
