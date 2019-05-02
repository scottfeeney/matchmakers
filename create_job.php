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
	$referenceNumber = "";
	$locationId = "";
	$jobTypeId = "";
	$numberAvailable = "";
	$positionAvailability = "";
	$jobDescription = "";
	$skillCategoryId = "";
	$selectedSkills = "";
	$active = "1";
	
	
	if (\Utilities\Common::IsSubmitForm())
	{
		//form submitted
		$jobName = \Utilities\Common::GetRequest("JobName");
		$referenceNumber = \Utilities\Common::GetRequest("ReferenceNumber");
		$locationId = \Utilities\Common::GetRequest("LocationId");
		$jobTypeId = \Utilities\Common::GetRequest("JobTypeId");
		$numberAvailable = \Utilities\Common::GetRequest("NumberAvailable");
		$positionAvailability = \Utilities\Common::GetRequest("PositionAvailability");
		$jobDescription = \Utilities\Common::GetRequest("JobDescription");
		$skillCategoryId = \Utilities\Common::GetRequest("SkillCategoryId");
		$selectedSkills = \Utilities\Common::GetRequest("SkillsControlSelectedSkills");
		$active = (\Utilities\Common::GetRequest("Active") == "1" ? "1" : "0");
		
		if ($jobName == "") {
			$errorMessages[] = "Please enter the Position Title";
		}
		
		if ($jobDescription == "") {
			$errorMessages[] = "Please enter the Position Description";
		}
		
		if (strlen($jobDescription) > 600 ) {
			$errorMessages[] = "Please only enter a Position Description of 600 characters";
		}
		
		if ($positionAvailability == "") {
			$errorMessages[] = "Please select the Position Availability";
		}

		if ($numberAvailable == "") {
			$errorMessages[] = "Please select the Number of Positions Available";
		}
		
		if ($jobTypeId == "") {
			$errorMessages[] = "Please select a Job Type";
		}
		
		if ($locationId == "") {
			$errorMessages[] = "Please select a Location";
		}
		
		if ($skillCategoryId == "") {
			$errorMessages[] = "Please select the Job Field";
		}
		
		if ($selectedSkills == "") {
			$errorMessages[] = "Please select at least one skill";
		}
		
		
		if (count($errorMessages) == 0) {
		
			// save job
			
			$job->employerId = $employer->employerId;
			$job->jobName = $jobName;
			$job->referenceNumber = $referenceNumber;
			$job->locationId = $locationId;
			$job->jobTypeId = $jobTypeId;
			$job->numberAvailable = $numberAvailable;
			$job->positionAvailability = $positionAvailability;
			$job->jobDescription = $jobDescription;
			$job->skillCategoryId = $skillCategoryId;
			$job->active = $active;
			$objectSave = $job->Save();
			
			if ($objectSave->hasError) {
				$errorMessages[] = $objectSave->errorMessage;
			}
			else {
			
				$jobId = $objectSave->objectId;
				
				\Classes\Job::SaveJobSkills($jobId, $selectedSkills);
			}
			
			if (count($errorMessages) == 0) {
				//	no errors, send to employer_jobs list;
				header("Location: employer_jobs.php");
				die();	
			}
		}	
		
	}
	else {
	
		//first load - load job information
		
		$jobName = $job->jobName;
		$referenceNumber = $job->referenceNumber;
		$locationId = $job->locationId;
		$jobTypeId = $job->jobTypeId;;
		$numberAvailable = $job->numberAvailable;
		$positionAvailability = $job->positionAvailability;
		$jobDescription = $job->jobDescription;
		$skillCategoryId = $job->skillCategoryId;
		$selectedSkills = \Classes\Job::GetSkillsByJobString($job->jobId);
		$active = $job->active;
	}
	
	//get arrys list for dropdown
	$locations = \Classes\Location::GetLocations();
	$jobTypes = \Classes\JobType::GetJobTypes();
	$skillCategories = \Classes\SkillCategory::GetSkillCategories();
	$positionAvailabilities = \Classes\Job::GetPositionAvailabilities();
	$numberAvailables = \Classes\Job::GetNumberAvailables();
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2><?php echo ($jobId == "0" ? "New" : "Edit") ?> Job Listing</h2>
			
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
							<label for="ReferenceNumber">Company Reference Number:</label>
							<input type="text" class="form-control" name="ReferenceNumber" id="ReferenceNumber" maxlength="70" value="<?php echo htmlspecialchars($referenceNumber) ?>">
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="JobName">*Position Title:</label>
							<input type="text" class="form-control" name="JobName" id="JobName" maxlength="70" value="<?php echo htmlspecialchars($jobName) ?>" required>
							<div class="invalid-feedback">Please enter the Position Title</div>
						</div>
					</div>
				</div>
				
				<div class="row">	
					<div class="col-sm-12">
						<div class="form-group">
							<label for="JobDescription">*Position Description:</label>
							<textarea type="text" class="form-control textarea-limit" rows="5" name="JobDescription" id="JobDescription" maxlength="600" data-limit="600" required><?php echo htmlspecialchars($jobDescription) ?></textarea>
							<div class="text-limit-remain"></div>
							<div class="invalid-feedback">Please enter the Position Description</div>
						</div>
					</div>
				</div>
				
				
				<div class="row">		
				
					<div class="col-sm-6">
						<div class="form-group">
							<label for="PositionAvailability">*Position Availability:</label>
							<select name="PositionAvailability" id="PositionAvailability" class="form-control" required>
								<option value=""></option>
								<?php foreach ($positionAvailabilities as $positionAvailabilitiyItem) { ?>
									<option value="<?php echo $positionAvailabilitiyItem; ?>" <?php if ($positionAvailabilitiyItem == $positionAvailability) {echo "selected";} ?>><?php echo $positionAvailabilitiyItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select the Position Availability</div>
						</div>
					</div>
					
					<div class="col-sm-6">
						<div class="form-group">
							<label for="NumberAvailable">*Number of Positions Available:</label>
							<select name="NumberAvailable" id="NumberAvailable" class="form-control" required>
								<option value=""></option>
								<?php foreach ($numberAvailables as $numberAvailableItem) { ?>
									<option value="<?php echo $numberAvailableItem; ?>" <?php if ($numberAvailableItem == $numberAvailable) {echo "selected";} ?>><?php echo $numberAvailableItem; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select the Number of Positions Available</div>
						</div>
					</div>
				
				</div>
				
				<div class="row">					
					<div class="col-sm-6">
						<div class="form-group">
							<label for="JobType">*Job Type:</label>
							<select name="JobTypeId" id="JobTypeId" class="form-control" required>
								<option value=""></option>
								<?php foreach ($jobTypes as $jobType) { ?>
									<option value="<?php echo $jobType->jobTypeId; ?>" <?php if ($jobType->jobTypeId == $jobTypeId) {echo "selected";} ?>><?php echo $jobType->jobTypeName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select a Job Type</div>
						</div>
					</div>
					
					<div class="col-sm-6">
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
					</div>
										
				</div>
				
				<div class="row">	
					<div class="col-sm-12">
						<div class="form-group">
							<label for="SkillCategoryId">*Job Field:</label>
							<select name="SkillCategoryId" id="SkillCategoryId" class="form-control skills-control-skills-category" required>
								<option value=""></option>
								<?php foreach ($skillCategories as $skillCategory) { ?>
									<option value="<?php echo $skillCategory->skillCategoryId; ?>" <?php if ($skillCategory->skillCategoryId == $skillCategoryId) {echo "selected";} ?>><?php echo $skillCategory->skillCategoryName; ?></option>
								<?php } ?>
							</select>
							<div class="invalid-feedback">Please select the Job Field</div>
						</div>
					</div>
				</div>
				
				<div class="row">		
					<div class="col-sm-12">
						<div class="form-group">
							<label>*Skills:</label>
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
				
				<div class="row">
					<div class="col-sm-12">
						<div class="form-check-inline">
							<label class="form-check-label">
								<input type="checkbox" class="form-check-input" id="Active" name="Active" value="1" <?php if ($active == "1") { echo "checked"; } ?>> Active
							</label>
						</div>
					</div>
				</div>
					
				<div class="row">	
					<div class="col-12">
						<div class="form-group mt-3">
							<button class="btn btn-primary"><?php echo ($job->jobId == 0 ? "Post Job" : "Save"); ?></button>
							<a class="btn btn-secondary ml-3" href="home.php" role="button">Cancel</a>
						</div>						
					</div>
				</div>
				
			</form>

			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>