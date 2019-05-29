<?php

	//----------------------------------------------------------------
	// Job Seeker Matches - displays jobs matches to a job seeker
	//----------------------------------------------------------------
	
	// include required php files, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';		
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/jobseeker.php';
		require_once './classes/job.php';
	}
	
	// get user from session
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 2) {
		// not job seeker, send them back to home
        header("Location: home.php");
		die();				
	}
	
	// get the job seeker object
	$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
	
	// website page header
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
	
?>	


        <section>
			<div class="row">
				<!-- Page heading-->
				<div class="col-9">
					<h2>Job Matches</h2>
				</div>
				<!-- Back Button -->
				<div class="col-3">
					<a class="float-right btn btn-primary mb-3 backButton" href="home.php" role="button"><i class="fas fa-arrow-left"></i><span class="back-button-text"> Back to Dashboard</span></a>
				</div>
			</div>
		
			<?php
			
				// display job seeker details
				echo GetCardDetail("<strong>Job Seeker: </strong>" . htmlspecialchars($jobSeeker->firstName . " " . $jobSeeker->lastName));
			
				if ($jobSeeker->active) {
			
					//Get and display a job seeker's job matches if job seeker is active
					$jobMatches = \Classes\Job::GetJobMatchesByJobSeeker($jobSeeker->jobSeekerId);
					
					if(!empty($jobMatches)){
						foreach ($jobMatches as $jobMatch) {
							echo GetJobMatchCard($jobMatch);
						}
					}
					else{
						echo '<div class="alert alert-warning mt-3" role="alert">There are no job matches for your profile. Please check back later.</div>';
					}
					
					
				}
				else {
					// job seeker inactive message
					echo '<div class="alert alert-warning mt-3" role="alert">Your profile is currently inactive (Employer\'s won\'t see it). <a class="alert-link" href="job_seeker_details.php">Update your details</a> to reactivate it.</div>';					
				}
				
				
			?>
			
		</section>
    
<?php
	
	// website page footer
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	//card to display jobs matched to a job seeker
	function GetJobMatchCard($jobMatch) {
		
		$html = '<div class="card listing-card">
			<div class="card-body">
			
				<div class="row">	
					<div class="col-sm-10">
			
						<div class="row">	
							<div class="col-sm-12 list-title">' . htmlspecialchars($jobMatch->jobName) . '</div>
						</div>
						
						<div class="row mt-2">	
							<div class="col-sm-12">Employer: ' . htmlspecialchars($jobMatch->employerName) . '</div>
						</div>
						
						<div class="row mt-2">	
							<div class="col-sm-4">Location: ' . htmlspecialchars($jobMatch->locationName) . '</div>
							<div class="col-sm-4">Job Type: ' . htmlspecialchars($jobMatch->jobTypeName) . '</div>
							<div class="col-sm-4"><div class="card-link"><a href="/view_job.php?j=' . $jobMatch->jobId . '"><i class="far fa-eye"></i> View Job</a></div></div>
						</div>
				
					</div>
				
					<div class="col-sm-2"><div class="card-match"><span>Match</span><br /><span style="font-size: 3em;">' . round($jobMatch->score) . '%</span></div></div>
				
				</div>
				
			</div>
		</div>';
		
		
		return $html;
		
	}
	
	// job seeker details card
	function GetCardDetail($text) {
		
		$html = '<div class="card dashboard-detail-card">
			<div class="card-body">' . $text . '</div>
		</div>';
		
		return $html;
		
	}
	
	
	
?>
