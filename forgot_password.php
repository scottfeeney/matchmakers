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
				
				$message = "We got a request to reset your password.\n\n";
				$message .= "You can reset your password by clicking on the following link: ".SITE_URL."/reset_password.php?r=r".$resetCode."\n\n";
				$message .= "\n\nIf for some reason the link does not work for you, please visit on the following link ".SITE_URL."/reset_password.php and enter the following reset code: ".$resetCode;
				$message .= "\n\nJob Matcher Team\n";
				
				\Utilities\Common::SendEmail($user->email, "Reset Password", $message);
			}
			
			header("Location: forgot_password_message.php");
			die();
			
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
				<h1>Forgot Password</h1>

				<p>Enter your email address below.</p>
							
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
			</div>
		</div>
	</section>

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>