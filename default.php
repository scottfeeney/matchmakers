<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/user.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
	}

	$errorMessage = "";
	$email = "";
	$password = "";
	
    if (\Utilities\Common::IsSubmitForm())
	{
		
		$email = \Utilities\Common::GetRequest("Email");
		$password = \Utilities\Common::GetRequest("Password");
		
		
		$user = \Classes\User::GetUserLogin($email, $password);
		
		
		if ($user == null) {
			$errorMessage = "Invalid Login";
		}
		else {
			
			session_start();
			session_unset();
			$_SESSION["UserId"] = $user->userId;
			header('Location: home.php');
			exit;
		}
		
	}
	
	$header = new \Template\Header();
	$header->isHomePage = true;
	$header->showMainBanner = false;
	echo $header->Bind();
	
?>

<section>
	<div class="jumbotron jumbotron-fluid jumbotron-main-home">
	
		
		
			
			<div class="home-top">
			
				<div class="container">

			
					<div class="row">
					
						<div class="col-lg-6">
						
							<div class="page-banner-text">
									<h1>Finding the job that's right for you</h1>
								</div>
						
							</div>
					
						<div class="col-lg-6">
						

							<div class="login-heading">
								<h2>Login to your Account</h2>
							</div>

							<form action="default.php" method="post" class="mt-3">
								
								<input type="hidden" name="SubmitForm" value="1">
								
								<?php if ($errorMessage != "") { ?>
									<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
								<?php } ?>
						
								<div class="form-group">
									<input type="email" class="form-control" name="Email" id="Email" maxlength="250" placeholder="Email" value="<?php echo htmlspecialchars($email) ?>">
								</div>
								
								<div class="form-group">
									<input type="password" class="form-control" name="Password" id="Password" maxlength="50" placeholder="Password" value="<?php echo htmlspecialchars($password) ?>">
								</div>
								
								<button type="submit" class="btn btn-success signin-button">Login</button>
							
							</form>

							<div class="mt-2 forgot-password">
								<a href="forgot_password.php">Forgot Your Password?</a>
							</div>
							
						</div>
					
					</div>
				
				</div>
			
			</div>
			
			
			<div class="container home-text-content-panel-container">
			
			
			
				<div id="about" class="home-text-content-panel">
				
					<div class="row">
					
						<div class="home-text-spacer"></div>
					
						<div class="home-text-content">
					
								<h2>About Job Matcher</h2>
								<p>Job Matcher matches Employers seeking staff with Job Seekers looking for work. To enable the site to work at its optimum, we have limited our industries to: </p>
								<ul>
									<li>Finance</li>
									<li>Health</li>
									<li>Information Technology</li>
									<li>Marketing</li>
									<li>Sales</li>
								</ul>
								<p>Not on the list?  Please check back, we will be adding more very soon. So whether you're an Employer looking for your next star employee, or a Job Seeker after a new challenge, this is the website for you.</p> 
						</div>
						
						<div class="home-text-spacer"></div>
						
						<div class="home-text-content home-text-content-two">
						
								<h2>How it Works</h2>
								
								<p>Our system uses state of the art matchmaking algorithms to match the Job Seekers to jobs posted by Employers.</p>

								<p>We match using:</p>
								
								<ul>
									<li>Skills</li>
									<li>Experience</li>
									<li>Location</li>
									<li>Job Type</li>
								</ul>

								<p>So sign up now to find the job that's right for you.</p>
								
								
								<div style="text-align: center; margin-top: 40px;"><a class="btn btn-primary btn-lg" role="button" href="signup.php">Sign Up</a></div>
							
						</div>
						
						<div class="home-text-spacer"></div>
											
					</div>
					
				</div>
			
			
				
		</div>
	</div>
</section>		

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>