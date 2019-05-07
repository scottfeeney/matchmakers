<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/skill.php';
		require_once './classes/skillcategory.php';
		require_once './classes/employer.php';
		require_once './classes/jobseeker.php';
		require_once './classes/job.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	// Get job seeker id from get reqeuest and load job details
	$jobseekerId = \Utilities\Common::GetRequest("js");
	$jobseeker = new \Classes\jobseeker($jobseekerId);
	
	// Load Job seeker details
	$firstName = $jobseeker->firstName;
	$lastName = $jobseeker->lastName;
	//$email = (new \Classes\User($jobSeeker->userid))->email;
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
	$selectedSkills = \Classes\Skill::GetSkillsByJobSeeker($jobseekerId);


	// Load job information (for Employer view only)
	if ($user->userType == 1) {
		$jobId = \Utilities\Common::GetRequest("j");
		$jobRequiredSkills = array();
		$missingSkills = array();
		
		if ($jobId != 0) {
			// Load job name and skills
			$jobName = (new \Classes\Job($jobId))->jobName;
			$jobRequiredSkills = \Classes\Skill::GetSkillsByJob($jobId);
			
			// Determine if job seeker is missing any required skills
			foreach($jobRequiredSkills as $skill){
				if (!in_array($skill, $selectedSkills)){
					array_push($missingSkills, $skill);
				}
			}
			
		}
	}
	
	// Display header section	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();	
?>	
        <section>
		
			<div class="row">
				<!-- Page heading-->
				<div class="col-9">
					<h2>Match Profile</h2>
				</div>
				<!-- Back Button -->
				<div class="col-3">
					<a class="float-right btn btn-primary mb-3 backButton" href="job_matches.php?j=<?php echo $jobId; ?>" role="button"><i class="fas fa-arrow-left"></i><span class="back-button-text"> Back to Matches</span></a>
				</div>
			</div>
			
		<div class="card listing-card">
			<div class="card-body">
				<!-- Job Seeker name -->
				<div class="row">
					<div class="col-sm-12">
						<h1><?php echo htmlspecialchars($firstName) . " " . htmlspecialchars($lastName) ;?></h1>
					</div>
				</div>
				
				<!-- Location and field -->
				<div class="row">
					<div class="col-sm-6">
						<p><strong>Location: </strong><?php echo htmlspecialchars($state); ;?></p>
					</div>
					
					<div class="col-sm-6">
						<p><strong>Primary Expertise: </strong><?php echo htmlspecialchars($skillCategoryName);?></p>
					</div>
				</div>
				
				<!-- Contact Details -->
				<div class="row mt-4">
					<div class="col-sm-12">
						<h3>Contact Details</h3>
					</div>
				</div>
				
				<!-- Job Seeker Contact Details -->
				<div class="row mt-2">
					<div class="col-sm-12">
						<p><strong>Email: </strong><?php echo (new \Classes\User($jobseeker->userId))->email; ?></p>
					</div>
					
					<div class="col-sm-6">
						<p><strong>Phone Number: </strong><?php echo "(" . htmlspecialchars($phoneAreaCode) . ") " . htmlspecialchars($phoneNumber); ?></p>
					</div>
					
					<div class="col-sm-6">
						<p><strong>Mobile Number: </strong><?php echo htmlspecialchars($mobileNumber); ?></p>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<p><strong>Address 1: </strong><?php echo htmlspecialchars($address1); ?></p>
					</div>
					<?php if(strlen(htmlspecialchars($address2)) > 0){ ?>
						<div class="col-sm-12">
							<p><strong>Address 2: </strong><?php echo htmlspecialchars($address2); ?></p>
						</div>
					<?php } ?>
				</div>
				
				<div class="row">
					<div class="col-sm-4">
						<p><strong>City: </strong><?php echo htmlspecialchars($city); ?></p>
					</div>
					
					<div class="col-sm-4">
						<p><strong>State: </strong><?php echo htmlspecialchars($state); ?></p>
					</div>
					
					<div class="col-sm-4">
						<p><strong>Post code: </strong><?php echo htmlspecialchars($postcode); ?></p>
					</div>
				</div>
				
				<!-- Additional Info -->
				<div class="row mt-4">
					<div class="col-sm-12">
						<h3>Additional Info</h3>
					</div>
				</div>
				
				<!-- Job Seeker Additional Info -->
				<div class="row mt-2">
					<div class="col-sm-6">
						<p><strong>Age Group: </strong><?php echo htmlspecialchars($ageGroup); ?></p>
					</div>
					
					<div class="col-sm-6">
						<p>
							<strong>Actively Looking: </strong>
							<?php 
								if ($active == 1){
									echo "Yes";
								}
								else {
									echo "No";
								}
							?>
						</p>
					</div>
					
					<div class="col-sm-6">
						<p><strong>Sign Up Reason: </strong><?php echo htmlspecialchars($signUpReason); ?></p>
					</div>
					
					<div class="col-sm-6">
						<p><strong>Availability: </strong><?php echo htmlspecialchars($jobChangeSpeed); ?></p>
					</div>				
				</div>
				
				<!-- Education -->
				<div class="row mt-4">
					<div class="col-sm-12">
						<h4>Education</h4>
					</div>
				</div>
				
				<!-- Job Seeker Education -->
				<div class="row mt-2">
					<div class="col-sm-8">
						<p><strong>Highest Level of schooling completed: </strong><?php echo htmlspecialchars($highestLevelCompleted); ?></p>
					</div>
				</div>
				
				<div class="row mt-6">
					<div class="col-sm-3">
						<p><strong>Currently Studying: </strong><?php echo htmlspecialchars($currentlyStudying); ?></p>
					</div>
					
					<?php if($currentlyStudying == "YES"){ ?>
						<div class="col-sm-5">
							<p><strong>Current Study Level: </strong><?php echo htmlspecialchars($currentStudyLevel); ?></p>
						</div>
					<?php } ?>
					
				</div>
				
				<!-- Skills -->
				<div class="row mt-4">
					<div class="col-sm-12">
						<h4>Skills</h4>
					</div>
				</div>
				
				<!-- Skills Category and loop skills-->
				<div class="row mt-2">
					<div class="col-sm-6">
							<p><strong>Expertise: </strong><?php echo htmlspecialchars($skillCategoryName); ?><p>
					</div>
				</div>
				
				<div id="skillsSection">
					<div class="col-sm-12 jobSkillsList">
						<?php
							// If the user is an employer and the job skills have been loaded display skills in different colours
							if ($user->userType == 1 && sizeof($jobRequiredSkills) > 0) {
								// Loop through array of skills to display skill name
								foreach($selectedSkills as $skill){
									if (in_array($skill, $jobRequiredSkills)){
										// Show skill as green if it's a required skill
										echo "<span class='badge badge-success jobSkillDisplay'>$skill->skillName</span>";
									}
									else {
										// Show skill as blue if it's not
										echo "<span class='badge badge-info jobSkillDisplay'>$skill->skillName</span>";
									}
								}
								echo "<p class='skillsInfo'><span style='color:#28a745'>Green</span> skills are your required skills for this position. <span style='color:#17a2b8'>Blue</span> skills are this job seekers additional skills</p>";
							}
							else {
								// Display skills as one colour
								foreach($selectedSkills as $skill){
									echo "<span class='badge badge-info jobSkillDisplay'>$skill->skillName</span>";
								}
							}
						?>
						
						
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($user->userType == 1 && sizeof($missingSkills) > 0) { ?>
			<div class="card mt-2">
				<div class="card-body">
					<div id="jobRequiredSkills">
						<div class="row mt-4">
							<div class="col-sm-12">
								<h4>Missing Skills</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<p>This job seeker does <strong>not</strong> have the following skill(s) required for your <?php echo htmlspecialchars($jobName); ?> position:</p>
							</div>
						</div>
						<div class="col-sm-12 jobSkillsList">
							<?php
								// Loop through array of skills to display skill name
								foreach($missingSkills as $skill){
									echo "<span class='badge badge-warning jobSkillDisplay'>$skill->skillName</span>";
								}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
