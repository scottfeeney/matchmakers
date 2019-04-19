<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/location.php';
		require_once './classes/employer.php';
		require_once './classes/skillcategory.php';
		require_once './classes/jobseeker.php';
		require_once './classes/jobtype.php';
		require_once './classes/skill.php';
		require_once './classes/job.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 1) {
		// not employer, send them back to home
        header("Location: home.php");
		die();				
	}
	
	$employer = \Classes\Employer::GetEmployerByUserId($user->userId);
	
	$jobId = \Utilities\Common::GetRequest("j");
	
	echo "JobId: " . $jobId;
	
	$job = new \Classes\Job($jobId);
	
	if ($job->jobId != 0) {
		//check employer
		if ($job->employerId != $employer->employerId) {
			header("Location: home.php");
			die();		
		}
	}
	

	// empty fields
	$errorMessages = [];
	
	$jobName = "";
	
	$positionDescription = "";
	$locationId = "";
	$jobChangeSpeed = "";
	$jobType = "";
	
	$skillCategoryId = "";
	$selectedSkills = "";
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$jobName = \Utilities\Common::GetRequest("JobName");
		/*$positionDescription = \Utilities\Common::GetRequest("positionDescription");
		$jobChangeSpeed = \Utilities\Common::GetRequest("jobChangeSpeed");
		$jobType = \Utilities\Common::GetRequest("jobType");
		$locationId = \Utilities\Common::GetRequest("LocationId");*/
		$skillCategoryId = \Utilities\Common::GetRequest("SkillCategoryId");
		
		$selectedSkills = \Utilities\Common::GetRequest("SkillsControlSelectedSkills");
		
		if ($jobName == "") {
			$errorMessages[] = "Please enter a position name";
		}
		
		/*if ($positionDescription == "") {
			$errorMessages[] = "Please enter your postition description";
		}
		
		if ($jobChangeSpeed == "") {
			$errorMessages[] = "Please select a Speed of job change";
		}

		if ($jobType == "") {
			$errorMessages[] = "Please select a type of work";
		}
		
		if ($locationId == "") {
			$errorMessages[] = "Please select a Location";
		}*/
		
		if ($skillCategoryId == 0) {
			$errorMessages[] = "Please select a Field of Expertise";
		}
		
		if ($selectedSkills == "") {
			$errorMessages[] = "Please select at least one skill";
		}
		
		/*if (count($skillsSelection) == 0) {
			$errorMessages[] = "Please select up to 10 skills";
		}
		elseif (count($skillsSelection) > 10) {
			$errorMessages[] = "Please select no more than 10 skills";
		}*/
		
		
		//$errorMessages[] = "Test Error";
		
		if (count($errorMessages) == 0) {
		
			// save job
			
			$job->employerId = $employer->employerId;
			$job->jobName = $jobName;
			$job->skillCategoryId = $skillCategoryId;
			$objectSave = $job->Save();
			
			if ($objectSave->hasError) {
				$errorMessages[] = $objectSave->errorMessage;
			}
			else {
			
				$jobId = $objectSave->objectId;
				
				\Classes\Job::SaveJobSkills($jobId, $selectedSkills);
			}
			
			if (count($errorMessages) == 0) {
				//	no errors, send to view job page;
				header("Location: home.php");
				die();	
			}
		}	
		
	}
	else {
	
		//first load - load job information
		
		$jobName = $job->jobName;
		$skillCategoryId = $job->skillCategoryId;
		$selectedSkills = \Classes\Job::GetSkillsByJobString($job->jobId);
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
			
			<form action="create_job.php" method="post" class="needs-validation" novalidate>
			
				<input type="hidden" name="SubmitForm" value="1">
				<input type="hidden" name="j" value="<?php echo htmlspecialchars($jobId) ?>">
				
				<?php if (count($errorMessages) > 0) { ?>
					<div class="alert alert-danger" role="alert"><?php echo join("<br />", $errorMessages); ?></div>
				<?php } ?>
				
				<div class="form-section">Job Details</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="JobName">*Position Title:</label>
							<input type="text" class="form-control" name="JobName" id="JobName" maxlength="70" value="<?php echo htmlspecialchars($jobName) ?>" required>
							<div class="invalid-feedback">Please enter the position name.</div>
						</div>
					</div>
				</div>
				
				<div class="row">	
					<div class="col-sm-12">
						<div class="form-group">
							<label for="positionDescription">*Postion description:</label>
							<textarea type="text" class="form-control" rows="5" name="positionDescription" id="positionDescription" value="<?php echo htmlspecialchars($positionDescription) ?>" maxlength="300" ></textarea>
							<div id="count">Characters Left: 300</div>
							<div class="invalid-feedback">Please enter your postition description</div>
						</div>
					</div>
				</div>
				
				<div class="row">					
					<div class="col-sm-4">
						<div class="form-group">
							<label for="JobType">*Job Type:</label>
							<select name="JobType" id="JobType" class="form-control" >
								<option value=""></option>
								<?php foreach ($jobTypes as $currJobType) { ?>
									<option value="<?php echo $currJobType->jobTypeId; ?>" <?php if ($currJobType->jobTypeId == $jobType) {echo "selected";} ?>><?php echo $currJobType->jobTypeName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Job Type</div>
						</div>
					</div>
					
					<div class="col-sm-4">
						<div class="form-group">
							<label for="Location">*Location:</label>
							<select name="LocationId" id="LocationId" class="form-control" >
								<option value=""></option>
								<?php foreach ($locations as $location) { ?>
									<option value="<?php echo $location->locationId; ?>" <?php if ($location->locationId == $locationId) {echo "selected";} ?>><?php echo $location->name; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Location</div>
						</div>
					</div>
					
					<div class="col-sm-4">
						<div class="form-group">
							<label for="JobChangeSpeed">*Position available in:</label>
							<select name="JobChangeSpeed" id="JobChangeSpeed" class="form-control" >
								<option value=""></option>
								<?php foreach ($jobChangeSpeeds as $jobChangeSpeedItem) { ?>
									<option value="<?php echo $jobChangeSpeedItem; ?>" <?php if ($jobChangeSpeedItem == $jobChangeSpeed) {echo "selected";} ?>><?php echo $jobChangeSpeedItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select an intended timeframe for position availability</div>
						</div>
					</div>					
				</div>
				
				<div class="row">	
					<div class="col-sm-12">
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
				</div>
				
				<div class="row">		
					<div class="col-sm-12">
						<div class="form-group">
							<label>*Skills:</label>
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