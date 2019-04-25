<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/skill.php';
		require_once './classes/skillcategory.php';
		require_once './classes/employer.php';
		require_once './classes/jobseeker.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	// Get job seeker id from get reqeuest and load job details
	$jobseekerId = \Utilities\Common::GetRequest("js");
	$jobseeker = new \Classes\jobseeker($jobseekerId);
	
	$firstName = $jobseeker->firstName;
	$lastName = $jobseeker->lastName;
	$phoneAreaCode = $jobseeker->phoneAreaCode;
	$phoneNumber = $jobseeker->phoneNumber;
	$mobileNumber = $jobseeker->mobileNumber;
	$address1 = $jobseeker->address1;
	$address2 = $jobseeker->address2;
	$city = $jobseeker->city;
	$state = $jobseeker->state;
	$postcode = $jobseeker->postcode;
	$skillCategoryId = $jobseeker->skillCategoryId;
	$skillCategoryName = (new \Classes\skillcategory($jobseeker->skillCategoryId))->skillCategoryName;
	$ageGroup = $jobseeker->ageGroup;
	$highestLevelCompleted = $jobseeker->highestLevelCompleted;
	$currentlyStudying = $jobseeker->currentlyStudying;
	$currentStudyLevel = $jobseeker->currentStudyLevel;
	$signUpReason = $jobseeker->signUpReason;
	$jobChangeSpeed = $jobseeker->jobChangeSpeed;
	$active = $jobseeker->active;
	
	// Create variables of job details for more reusable code.
	$selectedSkills = \Classes\Skill::GetSkillsByJobSeeker($jobseekerId);

	// Display header section	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();	
?>	
        <section>
			<!-- Job Seeker name -->
			<div class="row">
				<div class="col-sm-12">
					<h1><?php echo htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) ;?></h1>
				</div>
			</div>
			
			<!-- Contact Details -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3>Contact Details</h3>
				</div>
			</div>
			<br>
			
			<!-- Job Seeker Contact Details -->
			<div class="row">
				<div class="col-sm-2">
					<p><strong>Area Code: </strong><?php echo htmlspecialchars($phoneAreaCode); ?></p>
				</div>
				<div class="col-sm-5">
					<p><strong>Phone Number: </strong><?php echo htmlspecialchars($phoneNumber); ?></p>
				</div>
				<div class="col-sm-5">
					<p><strong>Mobile Number: </strong><?php echo htmlspecialchars($mobileNumber); ?></p>
				</div>
				<div class="col-sm-5">
					<p><strong>Address 1: </strong><?php echo htmlspecialchars($address1); ?></p>
				</div>
				<div class="col-sm-5">
					<p><strong>Address 2: </strong><?php echo htmlspecialchars($address2); ?></p>
				</div>
				<div class="col-sm-3">
					<p><strong>Post code: </strong><?php echo htmlspecialchars($postcode); ?></p>
				</div>
				<div class="col-sm-6">
					<p><strong>City: </strong><?php echo htmlspecialchars($city); ?></p>
				</div>
				<div class="col-sm-3">
					<p><strong>State: </strong><?php echo htmlspecialchars($state); ?></p>
				</div>
			</div>
			
			<!-- Additional Info -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3>Additional Info</h3>
				</div>
			</div>
			<br>
			
			<!-- Job Seeker Additional Info -->
			<div class="row mt-6">
				<div class="col-sm-3">
					<p><strong>Age Group: </strong><?php echo htmlspecialchars($ageGroup); ?></p>
				</div>
				<div class="col-sm-6">
					<p><strong>Actively Looking?: </strong><?php echo htmlspecialchars($active); ?></p>
				</div>
			</div>
			
			<!-- Education -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3>Education</h3>
				</div>
			</div>
			<br>
			
			<!-- Job Seeker Education -->
			<div class="row mt-6">
				<div class="col-sm-8">
					<p><strong>Highest Level of schooling completed: </strong><?php echo htmlspecialchars($highestLevelCompleted); ?></p>
				</div>
			</div>
			<div class="row mt-6">
				<div class="col-sm-3">
					<p><strong>Currently Studying: </strong><?php echo htmlspecialchars($currentlyStudying); ?></p>
				</div>
				<div class="col-sm-5">
					<p><strong>Currently Studying Level: </strong><?php echo htmlspecialchars($currentStudyLevel); ?></p>
				</div>
				<div class="col-sm-6">
					<p><strong>Sign Up Reason: </strong><?php echo htmlspecialchars($signUpReason); ?></p>
				</div>
				<div class="col-sm-6">
					<p><strong>Job Change Speed: </strong><?php echo htmlspecialchars($jobChangeSpeed); ?></p>
				</div>
			</div>
			
			<!-- Skills -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3>Skills</h3>
				</div>
			</div>
			<br>
			
			<!-- Skills Category and loop skills-->
			<div class="row mt-6">
				<div class="col-sm-6">
						<p><strong>Skill Category: </strong><?php echo htmlspecialchars($skillCategoryName); ?><p>
				</div>
			</div>
			<div id="skillsSection">
				<div class="col-sm-12">
					<?php					
						// Loop through array of skills to display skill name
						foreach($selectedSkills as $skill){
							echo "<span class='badge badge-info jobSkillDisplay'>$skill->skillName</span>";
						}
					?>
				</div>
			</div>
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>