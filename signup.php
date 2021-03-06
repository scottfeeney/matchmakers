<?php

	//----------------------------------------------------------------
	// User Sign Up - Initial sign up form
	//----------------------------------------------------------------
	
	// include required php files, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/user.php';
		require_once './classes/object_save.php';
		require_once './classes/header.php';
		require_once './classes/footer.php';
	}
	
	// default field values for form
	$errorMessage = "";
	$email = "";
	$userType = "";
	
	if (\Utilities\Common::IsSubmitForm())
	{
		$email = \Utilities\Common::GetRequest("Email");
		$userType = \Utilities\Common::GetRequest("UserType");
		
		///form submitted, get form data
		if (\Utilities\Common::IsValidEmail($email)) {
			
			// save user
			$user = new \Classes\User();
			$user->email = $email;
			$user->userType = ($userType == "1" ? "1" : "2"); //make sure user can only enter 1 or 2;
			$objectSave = $user->Save();
			
			if ($objectSave->hasError) {
				$errorMessage = $objectSave->errorMessage;
			}
			else {
				
				//get updated user, send email and redirect to sign up message page
				$user = new \Classes\User($objectSave->objectId);

				//Blair - v added to start of link to avoid issues with email clients interpreting start of sequence as unicode char
				$message = "Thank you for signing up with Job Matcher.\n\n";
				$message .= "Before you can continue, please verify your email address by clicking on the following link: ".SITE_URL."/verify_account.php?v=v".$user->verifyCode."\n\n";
				$message .= "\n\nIf for some reason the link does not work for you, please visit on the following link ".SITE_URL."/verify_account.php and enter the following verification code: ".$user->verifyCode;
				$message .= "\n\nJob Matcher Team\n";
				
				\Utilities\Common::SendEmail($email, "Joining Job Matcher", $message);
				
				header("Location: signup_message.php");
				die();
			}
			
		}
		else {
			$errorMessage = "The email entered is invalid";
		}
		
	}
		
	// website page header
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
				
							<h2>Sign Up</h2>
							
							<p>Select your account type and enter your email address below.</p>

							<form action="signup.php" method="post">
								
								<input type="hidden" name="SubmitForm" value="1">
								
								<?php if ($errorMessage != "") { ?>
									<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
								<?php } ?>

								<div class="form-group">
									<label for="email">Account Type:</label>
									<select class="form-control" name="UserType" id="usertype">
										<option value="1">Employer</option>
										<option value="2">Job Seeker</option>
									</select>
								</div>
								
								<div class="form-group">
									<label for="email">Email address:</label>
									<input type="email" class="form-control" name="Email" id="email" value="<?php echo htmlspecialchars($email) ?>">
								</div>
								
								<button type="submit" class="btn btn-success">Submit</button>
								
							</form>
							
						</div>
					</div>
					
				</div>
			</div>
		</section>

<?php
	// website page footer
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>