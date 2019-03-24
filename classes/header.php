<?php
	
	namespace Template;
	
	class Header {

		public $showHomeBanner = false;
		public $showMainBanner = true;
	
		function Bind() {
	
			$html = '<!doctype html>
				<html lang="en">
					<head>
						<!-- Required meta tags -->
						<meta charset="utf-8">
						<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
						
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
							  
						<link rel="stylesheet" href="/styles/style.css">

						<title>Job Matcher</title>
					</head>
					
					
					<body>
						<header>
						
							<div class="row">
							
								<div class="col-6">
									<div class="logo"><a href="/"><img src="/images/logo.png" alt="Job Matcher" width="" height="" alt="" /></a></div>
								</div>

								<div class="col-6">
									<div class="float-right"><a href="signup.php" class="btn btn-signup">Sign up</a></div>
								</div>
							</div>

						</header>

						<div class="main-container">';
						
							if ($this->showHomeBanner) {
								$html .= '<div class="home-banner">
							
								</div>';
							}
							
							if ($this->showMainBanner) {
								$html .= '<div class="main-banner">
							
								</div>';
							}
							
							
							$html .= '<div class="page-container">';
			
	
			
			return $html;
		
		}
		

	}
	
?>	