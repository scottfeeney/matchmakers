<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';	
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';		
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/employer.php';
		require_once './classes/job.php';
		require_once './classes/jobseeker.php';	
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
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();

	
?>	
        <section>		
			<div class="row">
				<!-- Page heading -->
				<div class="col-9">
					<h2>Job Matches</h2>
				</div>
				<!-- Back Button -->
				<div class="col-3">
					<a class="float-right btn btn-primary mb-3 backButton" href="employer_jobs.php" role="button"><i class="fas fa-arrow-left"></i><span class="back-button-text"> Back to Jobs</span></a>
				</div>
			</div>			
		
			<?php
			
				echo GetCardDetail("<strong>Employer: </strong>" . htmlspecialchars($employer->companyName) . "<br /><strong>Job: </strong>" . htmlspecialchars($job->jobName));
				//Loop through top matches
				
				
				if ($job->active) {
				
	
					$jobSeekerMatches = \Classes\JobSeeker::GetJobSeekerMatchesByJob(22);
		
					if(!empty($jobSeekerMatches)){
						foreach ($jobSeekerMatches as $jobSeekerMatch) {
							echo GetJobSeekerMatchCard($jobSeekerMatch, $jobId);
						}	
					}
					else{
						echo '<div class="alert alert-warning mt-3" role="alert">There are no job seeker matches for this job. Please check back later.</div>';
					}
				
				}
				else {
					
					echo '<div class="alert alert-warning mt-3" role="alert">This job is currently inactive. <a class="alert-link" href="create_job.php?j='  . $job->jobId . '">Edit Job</a> to reactivate it.</div>';
				}
				
				
			?>
			
		</section>
<?php
	
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	
	function GetJobSeekerMatchCard($jobSeekerMatch, $jobId) {
		
		$html = '<div class="card listing-card">
			<div class="card-body">
			
				<div class="row">	
					<div class="col-sm-10">
			
						<div class="row">	
							<div class="col-sm-12 list-title">' . htmlspecialchars($jobSeekerMatch->firstName . " " . $jobSeekerMatch->lastName) . '</div>
						</div>
						
						<div class="row mt-2">	
							<div class="col-sm-12">Field of Expertise: ' . htmlspecialchars($jobSeekerMatch->skillCategoryName) . '</div>
						</div>
					
						<div class="row mt-2">	
							<div class="col-sm-4">Location: ' . htmlspecialchars($jobSeekerMatch->locationName) . '</div>
							<div class="col-sm-4">Job Type: ' . htmlspecialchars($jobSeekerMatch->jobTypeName) . '</div>
							<div class="col-sm-4"><div class="card-link"><a href="/view_job_seeker.php?js=' . $jobSeekerMatch->jobSeekerId . "&j=" . $jobId . '"><i class="far fa-eye"></i> View Job Seeker</a></div></div>
						</div>
				
					</div>
				
					<div class="col-sm-2"><div class="card-match"><span>Match</span><br /><span style="font-size: 3em;">' . round($jobSeekerMatch->score) . '%</span></div></div>
				
				</div>
				
			</div>
		</div>';
		
		
		return $html;
		
		
	}
	
	
	
	function GetCardDetail($text) {
		
		$html = '<div class="card dashboard-detail-card">
			<div class="card-body">' . $text . '</div>
		</div>';
		
		return $html;	
	}
?>
