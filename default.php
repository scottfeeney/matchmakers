<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './wwwroot/utilities/common.php';
		require_once './wwwroot/classes/user.php';
		require_once './wwwroot/classes/header.php';
		require_once './wwwroot/classes/footer.php';
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

	
		<div class="jumbotron jumbotron-fluid">
		
			<div class="container">
				<div id="about" class="mb-3">
					<h1>About Job Matcher</h1>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque maximus lorem sit amet felis accumsan, a maximus erat scelerisque. Nunc cursus
					dolor orci, id venenatis felis imperdiet quis. Curabitur id ante massa. Donec eget sem ac tortor commodo placerat. Praesent pellentesque tincidunt
					ornare. Quisque luctus est ligula, ut rutrum metus ultricies in. Interdum et malesuada fames ac ante ipsum primis in faucibus. Phasellus venenatis
					metus eu pellentesque ullamcorper. Nam laoreet sapien at cursus condimentum. Nulla lobortis in leo in sollicitudin. Donec eu turpis tincidunt, 
					scelerisque ex in, porta sapien. Aliquam dapibus augue ac quam pulvinar, non feugiat risus malesuada.</p>
				</div>
	
				<div class="text-center">
					<div class="row mb-5">
						<div class="col-12">
							<a class="btn btn-primary btn-lg" role="button" href="signup.php">Sign Up</a>
						</div>
					</div>
					
					<h2>Have an account?</h2>
					<h3>Login</h3>

				</div>
	

				<form action="default.php" method="post">
					
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
					
					<button type="submit" class="btn btn-success">Login</button>
				
				</form>

				<div class="mt-2 forgot-password">
					<a href="forgot_password.php">Forgot Your Password?</a>
				</div>
				
			</div>
		
		</div>
		
	</section>		

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>