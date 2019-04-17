<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/employer.php';
		require_once './classes/jobseeker.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	$employer = \Classes\Employer::GetEmployerByUserId($user->userId);
	$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
	
	
	// Supplied in url, used to query database for job details
	$jobID = $_GET['jobID'];
	
	// Hardcoded vars for testing, these would be set from a query result
	// eg. while($jobDetails = mysqli_fetch_assoc($result)){ <set vars> }	
	$positionName = "Advertising Campaign Director";
	$companyName = "Omnitech";
	$positionDescription = "OmniTech is seeking a Advertising Campaign Director to help manage the
							long-term marketing strategy for the company. They’ll inform and 
							execute upon our social and conference strategy, while simultaneously
							providing logistical support for our demand generation activities.";	
	
	$locationId = "Hobart";
	$jobChangeSpeed = "Immediate";
	$jobType = "Full-Time";
	
	$skillCategoryId = "Marketing";
	$skillsSelection = ["Analytics", "Business to Consumer Marketing", "Campaign Management", "CRM Tool Knowledge", 
						"Google Ads", "Market Analysis", "Project Management", "SEO Tool Knowledge", 
						"Social Media Marketing", "Website Management"];
		
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>
			<!-- Position name -->
			<div class="row">
				<div class="col-sm-12">
					<h2><?php echo $positionName; ?></h2>
				</div>
			</div>
			
			<!-- Company name -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3><?php echo $companyName; ?></h3>
				</div>
			</div>
			
			<!-- Job Type and Start period -->
			<div class="row mt-4">
				<div class="col-sm-6">
					<h4>Job Type: <?php echo $jobType; ?></h4>
				</div>
				<div class="col-sm-6">
					<h4>Start date: <?php echo $jobChangeSpeed; ?></h4>
				</div>
			</div>
			
			<!-- Description -->
			<div class="row">
				<div class="col-sm-12">
					<p><?php echo $positionDescription; ?></p>
				</div>
			</div>
			
			<!-- Skills -->
			<div id="skillsSection">
				<div>
					<h2>Required <?php echo $skillCategoryId; ?> Skills</h2>
				</div>
				<div>
					<ul>
						<?php
							foreach($skillsSelection as $skill){
								echo "<li>$skill</li>";
							}						
						?>
					</ul>					
				</div>				
			</div>
			
			<!-- Location-->
			<div id="locationSection">
				<div>
					<h2>Location</h2>
				</div>
			</div>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>