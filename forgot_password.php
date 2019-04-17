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
			} else {
				$user = \Classes\User::GetUnverifiedUserByEmailAddress($email);
				if ($user != null) {
					$message = "Thank you for signing up with Job Matcher.\n\n";
					$message .= "It appears the link in your original verification email was not activated.\n\n";
					$message .= "Before you can continue, please verify your email address by clicking on the following link: ".SITE_URL."/verify_account.php?v=v".$user->verifyCode."\n\n";
					$message .= "\n\nIf for some reason the link does not work for you, please visit on the following link ".SITE_URL."/verify_account.php and enter the following verification code: ".$user->verifyCode;
					$message .= "\n\nJob Matcher Team\n";
					
					\Utilities\Common::SendEmail($email, "Job Matcher - verification email resend", $message);
					
				}
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
			
				<div class="row">
				
					<div class="col-lg-6 ml-lg-auto mr-lg-auto">
					
						<h1>Forgot Password</h1>

						<p>Enter your email address below and you will be sent an email with a link to reset your password.</p>
									
						<form action="forgot_password.php" method="post">
								
							<input type="hidden" name="SubmitForm" value="1">
							
							<?php if ($errorMessage != "") { ?>
								<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
							<?php } ?>
						
							
							<div class="form-group">
								<label for="Email">Email:</label>
								<input type="email" class="form-control" name="Email" id="Email" maxlength="250" value="<?php echo htmlspecialchars($email) ?>">
							</div>
										
							<button type="submit" class="btn btn-success">Send Link</button>  
						</form>
						
					</div>
				</div>
			</div>
		</div>
	</section>

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>