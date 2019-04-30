<?php
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
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 2) {
		// not job seeker, send them back to home
        header("Location: home.php");
		die();				
	}
	
	$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
	
	
	$jobMatches = \Classes\Job::GetJobMatchesByJobSeeker($jobSeeker->jobSeekerId);
				
				
					
	
	
	
?>	


        <section>
		
			<h2>Job Matches</h2>
			
			
		
			<?php
			
				echo GetCardDetail("<strong>Job Seeker: </strong>" . htmlspecialchars($jobSeeker->firstName . " " . $jobSeeker->lastName));
			
				foreach ($jobMatches as $jobMatch) {
					echo GetJobMatchCard($jobMatch);
				}
				
				
			?>
			
		</section>
    
<?php
	
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	
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
	
	function GetCardDetail($text) {
		
		$html = '<div class="card dashboard-detail-card">
			<div class="card-body">' . $text . '</div>
		</div>';
		
		return $html;
		
	}
	
	
	
?>
