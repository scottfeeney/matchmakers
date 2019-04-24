<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/jobtype.php';
		require_once './classes/skill.php';
		require_once './classes/job.php';
		require_once './classes/location.php';
	}
	
	$user = \Utilities\Common::GetSessionUser();
	
	$jobId = \Utilities\Common::GetRequest("j");
	$job = new \Classes\Job($jobId);
	
	
	$positionName = $job->jobName;
	$numberAvailable = $job->numberAvailable;	
	$companyName = "Omnitech";	
	$referenceNumber = $job->referenceNumber;
	$positionAvailability = $job->positionAvailability;	
	$positionDescription = $job->jobDescription;		
	$skillCategoryId = $job->skillCategoryId;	
	$active = $job->active;
	
	// Use $selectedSkills to make array of skill names
	$selectedSkills = \Classes\Job::GetSkillsByJobString($job->jobId);
	$selectedSkills = explode(",", $selectedSkills);
	$skills = array();	
	
	//foreach ($selectedSkills as $skill) {
	//	$name = "$skill->skillName";
	//	$skills[] = $name;
	//}	
	
	// Use $jobTypeId to get job type name
	$jobTypes = \Classes\JobType::GetJobTypes();
	$jobTypeId = $job->jobTypeId;
	
	foreach ($jobTypes as $jobType) {
		$jtID = $jobType->jobTypeId;		
		if($jtID == $jobTypeId){
			$jobTypeName = $jobType->jobTypeName;
			break;
		}
	}
	
	// Use $locationId to get location name
	$locations = \Classes\Location::GetLocations();
	$locationId = $job->locationId;
	
	foreach ($locations as $location) {
		if($location->locationId == $locationId){
			$locationName = $location->name;
			break;
		}
	}
	
	// Associative array of maps for each location
	$maps = array();
	$maps['Adelaide'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-34.9242062953014~138.59462275247796&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Brisbane'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-27.468007725655156~153.0220862218403&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8" ;
	$maps['Canberra'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-35.30746246270736~149.12195478297167&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Darwin'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-12.457115077105016~130.8404146521356&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Hobart'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-42.88270972627814~147.33020267077376&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Melbourne'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-37.81607137280399~144.9636397932391&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Perth'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-31.913550691161007~115.88006194312811&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";
	$maps['Sydney'] = "https://www.bing.com/maps/embed?h=400&w=400&cp=-33.86960029335556~151.2059159744571&lvl=13&typ=d&sty=r&src=SHELL&FORM=MBEDV8";	
	
	
	// Display header section	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>
			<!-- Position name -->
			<div class="row">
				<div class="col-sm-12">
					<h2><?php echo "$positionName (x$numberAvailable)"; ?></h2>
				</div>
			</div>
			
			<!-- Company name -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h3><?php echo $companyName; ?></h3>
				</div>
			</div>
			
			<!-- Job Type, Start period, and Reference No.-->
			<div class="row mt-2">
				<div class="col-sm-4">
					<p>Job Type: <?php echo $jobTypeName; ?><p>
				</div>
				<div class="col-sm-4">
					<p>Start Period: <?php echo $positionAvailability; ?></p>
				</div>				
				<div class="col-sm-4">
					<p><?php echo "Reference Number: $referenceNumber"; ?></p>
				</div>
			</div>
			
			<!-- Description -->
			<div class="row mt-2">
				<div class="col-sm-12">
					<h2>Job Description</h2>
					<p><?php echo $positionDescription; ?></p>
				</div>
			</div>
			
			<!-- Skills -->
			<div id="skillsSection">
				<h2>Required Skills</h2>
				<div>
					<ul>
						<li>TODO</li>
						<?php 							
							// Loop through array of skill names to display skills
							//foreach($skills as $skill){
							//	echo "<li>$skill</li>";
							//}
						?>
					</ul>					
				</div>				
			</div>
			
			<!-- Location-->
			<div id="locationSection">
				<div>
					<h2>Location</h2>
					<?php 
						echo $locationName;
					?>
					<div>
						<iframe width="400" 
								height="400" 
								src=<?php echo $maps[$locationName]; ?> 
								scrolling="no">
						</iframe-->
					</div>
				</div>
			</div>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>