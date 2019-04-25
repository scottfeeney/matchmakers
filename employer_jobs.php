<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';		
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/employer.php';
		require_once './classes/job.php';
	}
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 1) {
		// not employer, send them back to home
        header("Location: home.php");
		die();				
	}
	
	$employer = \Classes\Employer::GetEmployerByUserId($user->userId);
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
	
	
	$jobs = \Classes\Job::GetJobsByEmployer($employer->employerId);
				
				
					
	
	
	
?>	


        <section>
		
			<h2>Jobs</h2>
			
			
		
			<?php
			
				echo GetCardDetail("<strong>Employer: </strong>" . htmlspecialchars($employer->companyName));
			
				foreach ($jobs as $job) {
					echo GetJobCard($job);
				} 
				
				
			?>
			
		</section>
    
<?php
	
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	
	function GetJobCard($job) {
		
		$html = '<div class="card listing-card">
			<div class="card-body">
			
				<div class="row">	
					<div class="col-sm-12 list-title">' . htmlspecialchars($job->jobName) . '</div>
				</div>
				
				<div class="row">	
					<div class="col-sm-3">Posted: ' . \Utilities\Common::DisplayDate($job->created) . '</div>					
					<div class="col-sm-3 text-center"><a href="/create_job.php?j=' . $job->jobId . '"><i class="far fa-edit"></i> Edit</a></div>
					<div class="col-sm-3 text-center"><a href="/view_job.php?j=' . $job->jobId . '"><i class="far fa-eye"></i> View Job</a></div>
					<div class="col-sm-3 text-center"><a href="/job_matches.php?j=' . $job->jobId . '"><i class="fas fa-users"></i> View Matches</a></div>
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
