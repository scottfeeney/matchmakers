<?php
	
	namespace Template;
	
	class Header {

		public $isHomePage = false;
		public $showMainBanner = true;
		public $isSignedIn = false;
	
		function Bind() {
	
			$html = '<!doctype html>
				<html lang="en">
					<head>
						<!-- Required meta tags -->
						<meta charset="utf-8">
						<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
						<link rel="shortcut icon" type="image/png" href="/images/favicon.png"/>
						
						<!-- Project Team members -->
						<meta name="author" content="Blair Fraser">
						<meta name="author" content="Danny Aad">
						<meta name="author" content="Nick Kennedy">
						<meta name="author" content="Scott Feeney">
						<meta name="author" content="Shane West">
						

						<!-- Bootstrap CSS -->
						<link rel="stylesheet" 
							  href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" 
							  integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" 
							  crossorigin="anonymous">
				        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
						<link rel="stylesheet" href="/styles/style.css">
						<title>Job Matcher</title>
					</head>
					
					
					<body';
					
						if($this->isHomePage) {
							$html .= ' class="home-page"';
						}
					
						$html .= '>
						<header>
						
							<div class="row">
							
								<div class="col-6">
									<div class="logo"><a href="/home.php"><img src="/images/logo.png" alt="Job Matcher Logo" /></a></div>
								</div>

								<div class="col-6">';
								
									if ($this->isSignedIn)
									{
										$html .= '<div class="float-right"><a href="signout.php" class="btn btn-signup">Sign Out</a></div>';
									}
									else
									{
										$html .= '<div class="float-right"><a href="signup.php" class="btn btn-signup">Sign Up</a></div>';
									}
									
								$html .= '</div>
							</div>

						</header>

						<div class="main-container">';
						
						
							if ($this->showMainBanner) {
								$html .= '<div class="main-banner">
									<div class="page-banner-text-outer">
										<div class="page-banner-text">
											<h1>Finding the job that\'s right for you</h1>
										</div>
									</div>
								</div>';
							}
							
							
							$html .= '<div class="page-container">';
			
	
			
			return $html;
		
		}
		

	}
	
?>	