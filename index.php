<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once "tools.php";

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
			echo "Logged In";
			header('Location: index.php');
			exit;
		}
		
	}
		// Add head section to page from tools.php
	add_head();

    bootstrap_optional();		
?>

<body>
	
<header>
    <div class="container-fluid">
		<div id=headerTxt class="text-center">
		    <h1>Job Matcher</h1>
		    <p>Find the perfect employer/employee today</p>
	    </div>
	</div>
</header>

<!-- Contains modified code from https://getbootstrap.com/docs/4.1/components/navbar/#nav -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
  <a class="navbar-brand" href="#">jobMatcher</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <a class="nav-item nav-link" href="index.php">Home</a>
      <a class="nav-item nav-link" href="#about">About</a>
      <a class="nav-item nav-link" href="#login">Login</a>
    </div>
  </div>
</nav>

<main class="container">
	
	<section>
		<div id="about">
			<h2>About Job Matcher</h2>
			<div class="row">
				<div class="col-lg-6">
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc venenatis est dolor. Aliquam tincidunt metus ac felis varius euismod. 
					Ut ac arcu pulvinar, pretium massa tristique, condimentum erat. Curabitur tempor consectetur mi ac congue. Mauris leo sapien, gravida 
					in leo nec, vestibulum feugiat metus. Donec consequat semper pulvinar. Praesent vitae diam non ipsum ultricies porttitor eu quis 
					sapien.</p>
		
					<p>Fusce bibendum turpis nunc, et imperdiet eros maximus volutpat. Proin blandit mauris at metus eleifend, sed mattis orci tempor. 
					Nulla imperdiet mi eu neque vehicula faucibus. Nam vitae quam vestibulum, lacinia arcu quis, tempor purus. Cras tincidunt, nisl at 
					feugiat imperdiet, turpis turpis venenatis dui, in viverra metus quam eu velit. Donec vel sapien ligula. Nullam rhoncus diam id 
					fringilla dictum.</p>
				</div>
				
				<div class="col-lg-6">
					<!-- https://www.pexels.com/photo/man-and-woman-shaking-hands-1249158/ -->
					<img src="images/accomplishment-agreement-business-12491582.jpg" alt="People shaking hands in agreement" class="img-fluid">
				</div>
			</div>
		</div>
	</section>
	
	<section>
		<div id="login">
			<h2>Login</h2>
		</div>
		
		<form action="default.php" method="post">
			
			<input type="hidden" name="SubmitForm" value="1">
			
			<?php if ($errorMessage != "") { ?>
				<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
			<?php } ?>
	
			<div class="form-group">
				<label for="Password">Email:</label>
				<input type="email" class="form-control" name="Email" id="Email" maxlength="250" value="<?php echo htmlspecialchars($email) ?>">
			</div>
			
			<div class="form-group">
				<label for="Password">Password:</label>
				<input type="password" class="form-control" name="Password" id="Password" maxlength="50" value="<?php echo htmlspecialchars($password) ?>">
			</div>
			
			<button type="submit" class="btn btn-primary">Login</button>
		</form>
	
		<br/>
		<a href="signup.php" class="btn btn-primary">Sign up</a>

		<div class="mt-2">
				<a href="forgotpassword.php">Forgot Your Password?</a>
		</div>

	</section>
</main>

<footer class="container-fluid">
	<div class="row mx-auto justify-content-center">
		<div class="col-sm-3 footerCol">
			<p>Contact info?</p>
		</div>
		
		<div class="col-sm-3 footerCol">
			<p>List of pages here</p>
		</div>
		
		<div class="col-sm-3 footerCol">
			<p>Something else?</p>
		</div>
	</div>
</footer>
</body>
</html>
