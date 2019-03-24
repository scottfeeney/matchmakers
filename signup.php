<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	
	$errorMessage = "";
	$email = "";
	$userType = "";
	
	if (\Utilities\Common::IsSubmitForm())
	{
		$email = \Utilities\Common::GetRequest("Email");
		$userType = \Utilities\Common::GetRequest("UserType");
		
		// form submitted
		if (\Utilities\Common::IsValidEmail($email)) {
			
			// save user
			$user = new \Classes\User();
			$user->email = $email;
			$user->userType = $userType;
			$objectSave = $user->Save();
			
			if ($objectSave->hasError) {
				$errorMessage = $objectSave->errorMessage;
			}
			else {
				
				//get updated user and send email
				$user = new \Classes\User($objectSave->objectId);
				//Blair - v added to start of link to avoid issues with email clients interpreting start of sequence as unicode char
				
				$message = "Thank you for signing up with Job Matcher.\n";
				$message .= "Before you can continue, please verify your email address by clicking on the following link: ".SITE_URL."/verify_account.php?v=v".$user->verifyCode."\n";
				$message .= "\nIf for some reason the link does not work for you, please visit on the following link ".SITE_URL."/verify_account.php and enter the following verification code: ".$user->verifyCode;
				$message .= "\nJob Matcher Team\n";
				
				\Utilities\Common::SendEmail($email, "Joining Job Matcher", $message);
				
				header("Location: signup_message.php");
				die();
			}
			
		}
		else {
			$errorMessage = "The email entered is invalid";
		}
		
	}
		
	$header = new \Template\Header();
	echo $header->Bind();
?>	


        <section>
            
			<h2>Sign Up</h2>
			
			<p>Select your account type and enter your email address below.</p>

			<form action="signup.php" method="post">
				
				<input type="hidden" name="SubmitForm" value="1">
				
				<?php if ($errorMessage != "") { ?>
					<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
				<?php } ?>

				<div class="form-group">
					<label for="email">User Type:</label>
					<select class="form-control" name="UserType" id="usertype">
						<option value="1">Employer</option>
						<option value="2">Job Seeker</option>
					</select>
				</div>
				
				<div class="form-group">
					<label for="email">Email address:</label>
					<input type="email" class="form-control" name="Email" id="email" value="<?php echo htmlspecialchars($email) ?>">
				</div>
				
				<button type="submit" class="btn btn-primary">Submit</button>
				
			</form>
		</section>

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>

