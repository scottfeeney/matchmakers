<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/adminstaff.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';		
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/adminstaff.php';
		require_once './classes/employer.php';
		require_once './classes/jobseeker.php';
		require_once './classes/job.php';
	}
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>
		
			<h2>Welcome to Job Matcher</h2>
		
			<?php
				if ($user->userType == 1) {
					
					//employer
					
					$employer = \Classes\Employer::GetEmployerByUserId($user->userId);
					
					echo GetCardDetail("<strong>Employer: </strong>" . htmlspecialchars($employer->companyName));
					
					echo GetCard("fa-building", "Click here to update your details", "/employer_details.php");
					
					echo GetCard("fa-hard-hat", "Click here to post a new job listing", "/create_job.php");
					
					echo GetCard("fa-stream", "Click here to view your job listings", "/employer_jobs.php");
					


				}
				else if ($user->userType == 2) {
					
					//job seeker
					
					$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
					
					echo GetCardDetail("<strong>Job Seeker: </strong>" . htmlspecialchars($jobSeeker->firstName . " " . $jobSeeker->lastName));
					
					echo GetCard("fa-user-tie", "Click here to update your details", "/job_seeker_details.php");
					
					echo GetCard("fa-stream", "Click here to view your job matches", "/job_seeker_matches.php");
					

				}
				else if ($user->userType == 3) {
					
					//staff
					
					$adminStaff = \Classes\AdminStaff::GetAdminStaffByUserId($user->userId);
					
					echo GetCardDetail("<strong>Staff member: </strong>" . htmlspecialchars($adminStaff->firstName . " " . $adminStaff->lastName));
					
					echo GetCard("fa-user", "Click here to manage skills", "/skills_manage.php");
					
				}
			?>
			
			
			
			
			
		</section>
    
<?php
	
	$footer = new \Template\Footer();
	echo $footer->Bind();
	
	
	function GetCard($icon, $text, $url) {
		
		$html = '<div class="card dashboard-card" onclick="window.location.href=\'' . $url .'\';">
			<div class="card-body">
				<div class="row">	
					<div class="col-sm-2 dashboard-icon">
						<i class="fas ' . $icon . '"></i>
					</div>
					<div class="col-sm-10 dashboard-text">' . $text . '</div>
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
