<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/jobtype.php';
		require_once './classes/skill.php';
		require_once './classes/job.php';
		require_once './classes/location.php';
		require_once './classes/employer.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	// Get job id from get reqeuest and load job details
	$jobId = \Utilities\Common::GetRequest("j");
	$job = new \Classes\Job($jobId);
	
	// Create variables of job details for more reusable code.
	$positionName = $job->jobName;
	$numberAvailable = $job->numberAvailable;
	$companyName = (new \Classes\Employer($job->employerId))->companyName;
	$jobTypeName = (new \Classes\JobType($job->jobTypeId))->jobTypeName;
	$positionAvailability = $job->positionAvailability;
	$referenceNumber = $job->referenceNumber;
	$positionDescription = $job->jobDescription;
	$selectedSkills = \Classes\Skill::GetSkillsByJob($jobId);
	$locationName = (new \Classes\Location($job->locationId))->name;

	// Associative array of maps for each location
	$maps = array();
	$maps['Adelaide'] = "cp=-34.9242062953014~138.59462275247796&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Brisbane'] = "cp=-27.468007725655156~153.0220862218403&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8" ;
	$maps['Canberra'] = "cp=-35.30746246270736~149.12195478297167&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Darwin'] = "cp=-12.457115077105016~130.8404146521356&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Hobart'] = "cp=-42.88270972627814~147.33020267077376&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Melbourne'] = "cp=-37.81607137280399~144.9636397932391&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Perth'] = "cp=-31.913550691161007~115.88006194312811&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Sydney'] = "cp=-33.86960029335556~151.2059159744571&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";	

	// Display header section	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();	
?>	

        <section>
		<div class="card listing-card">
			<div class="card-body">
			<!-- Position name -->
			<div class="row">
				<div class="col-sm-12">
					<h2 class="job-view-title"><?php echo htmlspecialchars($positionName); ?></h2>					
					<?php 	if($numberAvailable > 1){
								echo '<span class="badge badge-success job-positions">' . $numberAvailable . ' positions</span>';
							}
						?>
				</div>
			</div>
			
			<!-- Company name -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<p><strong>Employer: </strong><?php echo $companyName; ?></p>
				</div>
			</div>
			
			<!-- Job Type, Start period, and Reference No.-->
			<div class="row mt-2">
				<div class="col-sm-4">
					<p><strong>Job Type: </strong><?php echo $jobTypeName; ?></p>
				</div>
				<div class="col-sm-4">
					<p><strong>Start Period: </strong><?php echo $positionAvailability; ?></p>
				</div>				
				<div class="col-sm-4">
					<p><strong>Reference Number: </strong><?php echo htmlspecialchars($referenceNumber); ?></p>
				</div>
			</div>
			
			<!-- Description -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h2>Job Description</h2>
					<p><?php echo htmlspecialchars($positionDescription); ?></p>
				</div>
			</div>
			
			<!-- Skills -->
			<div id="skillsSection">
				<h3>Required Skills</h3>
				<div class="col-sm-12 jobSkillsList">
					<?php					
						// Loop through array of skills to display skill name
						foreach($selectedSkills as $skill){
							echo "<span class='badge badge-info jobSkillDisplay'>" . htmlspecialchars($skill->skillName) . "</span>";
						}
					?>
				</div>
			</div>
			
			<!-- Location-->
			<div id="locationSection">
				<div>
					<h2>Location: <?php echo $locationName; ?></h2>
					
					<div class="map-container">
						<iframe width="485" 
								height="300" 
								src="<?php echo "https://www.bing.com/maps/embed?h=300&w=485&" . $maps[$locationName]; ?>"
								scrolling="no"
								frameborder="0" >
						</iframe>
					</div>
				</div>
			</div>
		</div>
		</div>
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
