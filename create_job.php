<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill_category.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job_type.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/location.php';
		require_once './classes/employer.php';
		require_once './classes/skill_category.php';
		require_once './classes/jobseeker.php';
		require_once './classes/job_type.php';
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
	
	$positionName = "";
	$positionDescription = "";
	
	$jobChangeSpeed = "";
	$jobType = "";
	
	$skillCategoryId = 0;
	$skillsSelection = [];
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$positionName = \Utilities\Common::GetRequest("positionName");
		$positionDescription = \Utilities\Common::GetRequest("positionDescription");
		$jobChangeSpeed = \Utilities\Common::GetRequest("jobChangeSpeed");
		$jobType = \Utilities\Common::GetRequest("jobType");
		$skillCategoryId = \Utilities\Common::GetRequest("SkillCategoryId");
		$skillsSelection = \Utilities\Common::GetRequest("SkillsSelection");
		
		if ($positionName == "") {
			$errorMessages[] = "Please enter a position name";
		}
		
		if ($positionDescription == "") {
			$errorMessages[] = "Please enter your postition description";
		}
		
		if ($jobChangeSpeed == "") {
			$errorMessages[] = "Please select a Speed of job change";
		}

		if ($jobType == "") {
			$errorMessages[] = "Please select a type of work";
		}
		
		if ($skillCategoryId == 0) {
			$errorMessages[] = "Please select a Field of Expertise";
		}
		
		if (count($skillsSelection) == 0) {
			$errorMessages[] = "Please select up to 10 skills";
		}
		elseif (count($skillsSelection) > 10) {
			$errorMessages[] = "Please select no more than 10 skills";
		}
		
		
		if (count($errorMessages) == 0) {
		
			// save job
			
			// if (count($errorMessages) == 0) {
			// //	no errors, send to view job page;
			// //  $jobID = "";
			// 	header("Location: view_job.php?jobID=$jobID");
			// 	die();	
			// }
		}	
		
	}
	else {
	
		//first load - load job information
	
	}
	
	//get arrys list for dropdown
	$locations = \Classes\Location::GetLocations();
	$jobTypes = \Classes\JobType::GetJobTypes();
	$skillCategories = \Classes\SkillCategory::GetSkillCategories();
	$jobChangeSpeeds = \Classes\JobSeeker::GetJobChangeSpeeds();
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>New Job listing</h2>
			
			<p>Please enter your position details below.</p>
			
			<form action="employer_details.php" method="post" class="needs-validation" novalidate>
			
				<input type="hidden" name="SubmitForm" value="1">
				
				<?php if (count($errorMessages) > 0) { ?>
					<div class="alert alert-danger" role="alert"><?php echo join("<br />", $errorMessages); ?></div>
				<?php } ?>
				
				<div class="form-section">Job Details</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="positionName">*Position Title:</label>
							<input type="text" class="form-control" name="positionName" id="positionName" maxlength="70" value="<?php echo htmlspecialchars($positionName) ?>" required>
							<div class="invalid-feedback">Please enter the position name.</div>
						</div>
					</div>
					
					<div class="col-sm-12">
						<div class="form-group">
							<label for="positionDescription">*Postion description:</label>
							<textarea type="text" class="form-control" rows="5" name="positionDescription" id="positionDescription" value="<?php echo htmlspecialchars($positionDescription) ?>" maxlength="300" required></textarea>
							<div id="count"></div>
							<div class="invalid-feedback">Please enter your postition description</div>
						</div>
					</div>
					
					<div class="col-sm-6">
						<div class="form-group">
							<label for="JobType">*Job Type:</label>
							<select name="JobType" id="JobType" class="form-control" required>
								<option value=""></option>
								<?php foreach ($jobTypes as $currJobType) { ?>
									<option value="<?php echo $currJobType->jobTypeId; ?>" <?php if ($currJobType->jobTypeId == $jobType) {echo "selected";} ?>><?php echo $currJobType->jobTypeName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Job Type</div>
						</div>
					</div>
					
					<div class="col-sm-6">
						<div class="form-group">
							<label for="JobChangeSpeed">*Position availability in:</label>
							<select name="JobChangeSpeed" id="JobChangeSpeed" class="form-control" required>
								<option value=""></option>
								<?php foreach ($jobChangeSpeeds as $jobChangeSpeedItem) { ?>
									<option value="<?php echo $jobChangeSpeedItem; ?>" <?php if ($jobChangeSpeedItem == $jobChangeSpeed) {echo "selected";} ?>><?php echo $jobChangeSpeedItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select an intended timeframe for position availability</div>
						</div>
					</div>
					
					<div class="col-sm-12">
						<div class="form-group">
							<label for="SkillCategoryId">*Field of Expertise:</label>
							<select name="SkillCategoryId" id="SkillCategoryId" class="form-control" required>
								<option value=""></option>
								<?php foreach ($skillCategories as $skillCategory) { ?>
									<option value="<?php echo $skillCategory->skillCategoryId; ?>" <?php if ($skillCategory->skillCategoryId == $skillCategoryId) {echo "selected";} ?>><?php echo $skillCategory->skillCategoryName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Field of Expertise</div>
						</div>
					</div>
					
					<div class="col-sm-12">
						<div class="form-group">
							<label for="SkillsSelection">*Skills:</label>
							<br />
							<?php
							
								// foreach($skillsList as $skill){
								// 	$skillID = $skill["ID"];
								// 	$skillName = $skill["Name"];
								// 	echo "<input type='checkbox' name='SkillsSelection[]' value='$skillID'><label>$skillName</label>";
								// }
								
							?>
							<div class="main-form">
								<div class="skill-checkbox-skills-list"></div>
								<div class="skill-checkbox-skills-loading">
									<i class="fas fa-spinner fa-spin"></i>
								</div>
								<input type="hidden" id="hidSkillId" value="" />
								<input type="hidden" id="hidSkillName" value="" />
								
							</div>
							<div class="invalid-feedback">Please select up to 10 skills</div>
						</div>
					</div>
				</div>
				
				<div class="form-group mt-3">
					<button class="btn btn-primary">Post Job</button>  
				</div>
				
			</form>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>