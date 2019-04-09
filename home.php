<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/adminStaff.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
		require_once './classes/adminStaff.php';
	}
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>
		
			<h2>Welcome to Job Matcher</h2>
		
			<h3>Dashboard</h3>
			
			<?php
				if ($user->userType == 1) {
					$details = "href='employer_details.php'";
					$match = "View Your Jobs";
				}
				else if ($user->userType == 2) {
					$details = "href='job_seeker_details.php'";
					$match = "View Your Matches";
				}
			?>
			
			
			<?php if ($user->userType == 3) { 
			
				$adminStaff = \Classes\AdminStaff::GetAdminStaffByUserId($user->userId);
		
			?>
			
					<p>Welcome staff member: <?php echo htmlspecialchars($adminStaff->firstName . " " . $adminStaff->lastName) ?>
					
					
					<p><a href="skills_manage.php">Click here</a> to manage skills.</p>
			
			
				<?php } else { ?>
				
				<div id="dashboard" class="row">
					<div class="col-sm-3">
						<a <?php echo $details; ?> data-toggle="tooltip" data-placement="top" title="View or update your details">
							<div class="card p-1 mb-2">
								<!-- detail by priyanka from the Noun Project
									 https://thenounproject.com/search/?q=details&i=2336354 -->
								<img class="card-img-top mx-auto img-responsive" src="images/noun_detail_2336354_resized.png" alt="Card image cap">
								<div class="card-body">
									<h5 class="card-title text-center">Your Details</h5>
								</div>
							</div>
						</a>
					</div>
					
					<?php if ($user->userType == 1) { ?>
						<div class="col-sm-3">
							<div class="card p-1 mb-2" data-toggle="tooltip" data-placement="top" title="Create new job listings">
								<!-- Job Search by Thomas' designs from the Noun Project
									 https://thenounproject.com/term/job-search/1018640/ -->
								<img class="card-img-top mx-auto img-responsive" src="images/noun_Job Search_1018640.png" alt="Card image cap">
								<div class="card-body">
									<h5 class="card-title text-center">Create New Job</h5>
								</div>
							</div>
						</div>
					<?php } ?>
					
					<div class="col-sm-3">
						<div class="card p-1 mb-2" data-toggle="tooltip" data-placement="top" title="View and edit your job listings">
							<!-- job by Adrien Coquet from the Noun Project
								 https://thenounproject.com/search/?q=job&i=2043873 -->
							<img class="card-img-top mx-auto img-responsive" src="images/noun_job_2043873.png" alt="Card image cap">
							<div class="card-body">
								<h5 class="card-title text-center"><?php echo $match; ?></h5>
							</div>
						</div>
					</div>
				</div>
			
			<?php } ?>
			
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
