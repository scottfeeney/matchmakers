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
			<div class="row">
				<!-- Page heading -->
				<div class="col-6">
					<h2>Jobs</h2>
				</div>
				<!-- Back Button -->
				<div class="col-6">
					<a class="float-right btn btn-primary mb-3 backButton" href="home.php" role="button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
				</div>
			</div>
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
				
		$active = "";
		
		if($job->active == 0){
			$active = '<span class="badge badge-danger job-inactive">Inactive</span>';
		}
		
		$html = '<div class="card listing-card">
			<div class="card-body">
			
				<div class="row">	
					<div class="col-sm-12 list-title">' . htmlspecialchars($job->jobName) . $active .'</div>
				</div>
				
				<div class="row">	
					<div class="col-sm-3">Posted: ' . \Utilities\Common::DisplayDate($job->created) . '</div>					
					<div class="col-sm-3"><div class="card-link"><a href="/create_job.php?j=' . $job->jobId . '"><i class="far fa-edit"></i> Edit</a></div></div>
					<div class="col-sm-3"><div class="card-link"><a href="/view_job.php?j=' . $job->jobId . '"><i class="far fa-eye"></i> View Job</a></div></div>
					<div class="col-sm-3"><div class="card-link"><a href="/job_matches.php?j=' . $job->jobId . '"><i class="fas fa-users"></i> View Matches</a></div></div>
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
