<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once "tools.php";

	$errorMessage = "";
	$email = "";
	
    if (\Utilities\Common::IsSubmitForm())
	{
		
		$email = \Utilities\Common::GetRequest("Email");
		
		if ($email == "")
		{
			$errorMessage = "Please enter your email address";
		}
		else {
			$user = \Classes\User::GetUserByEmailAddress($email);
			
			if ($user != null) {
				// save user
				
				$resetCode = \Utilities\Common::GetGuid();
				
				$user->resetCode = $resetCode;
				$objectSave = $user->Save();
				
				$message = "To reset your password click the following link: ".SITE_URL."/reset_password.php?r=".$resetCode;
				$message = $message."\n\n\nCode Url: /reset_password.php?r = ".$resetCode;
				$message = $message."\n\n\nCode: ".$resetCode;
				
				\Utilities\Common::SendEmail($user->email, "Reset Password", $message);
			}
			
			header("Location: forgot_password_message.php");
			die();
			
		}
		

		
	}
		// Add head section to page from tools.php
	add_head();

	bootstrap_optional();	
?>

<body>
<header>
</header>
<nav>
</nav>
	<div>
		<main class="container">
	<section>
	<br/>
	<h1>Forgot Password</h1>

		
				
		<form action="forgot_password.php" method="post">
			
			<input type="hidden" name="SubmitForm" value="1">
			
			<?php if ($errorMessage != "") { ?>
				<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
			<?php } ?>
	
			
			<div class="form-group">
				<label for="Password">Email:</label>
				<input type="email" class="form-control" name="Email" id="Email" maxlength="250" value="<?php echo htmlspecialchars($email) ?>">
			</div>
						
			<button type="submit" class="btn btn-primary">Send Link</button>  
		</form>
	</section>
	</div>
</main>
<footer>
</footer>
</body>
</html>