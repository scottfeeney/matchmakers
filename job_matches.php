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
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();

	$jobs = \Classes\Job::GetJobsByEmployer($employer->employerId);
					
?>	
        <section>
		
			<h2>Job Matches</h2>
		
			<?php
			
				echo GetCardDetail("<strong>Employer: </strong>" . htmlspecialchars($employer->companyName));
				//Loop through top matches
				for ($x = 0; $x <= 5; $x++) {
					//echo GetTopJobSeekers($job);
				} 

			?>
			
		</section>
<?php
	
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	function GetCardDetail($text) {
		
		$html = '<div class="card dashboard-detail-card">
			<div class="card-body">' . $text . '</div>
		</div>';
		
		return $html;	
	}
?>
