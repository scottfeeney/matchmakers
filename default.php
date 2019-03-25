<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';

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
	$header->showHomeBanner = true;
	$header->showMainBanner = false;
	echo $header->Bind();
	
?>


	<section>

	
		<h2>Have an account, Login</h2>

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

		<div class="mt-2">
			<a href="forgot_password.php">Forgot Your Password?</a>
		</div>
		
	</section>		

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>