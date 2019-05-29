<?php

	//----------------------------------------------------------------
	// Reset Password - Form to reset password
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
	
	// request reset code
	$resetCode = \Utilities\Common::GetRequest("r");
	
	/*
	 * now trim the 'r' at the start of the reset code, placed there to prevent issues with email clients interpreting
	 * a sequence starting with = then hexadecimal characters as a unicode char
	 * (check not already done as this page will be loaded more than once during password setting process)
	 */
	if (substr($resetCode,0,1) == 'r') {
		$resetCode = substr($resetCode,1);
	}

    // get user based on Verify Code where not already verified
	$user = \Classes\User::GetUserByResetCode($resetCode);
	
	// found user based on reset code
	if ($user != null) {
	
		// functionality to process reset form
	
		// default field values
		$errorMessage = "";
		$password = "";
		$confirmPassword = "";
		
		
		if (\Utilities\Common::IsSubmitForm())
		{
			
			//form submitted, get form data
			$password = \Utilities\Common::GetRequest("Password");
			$confirmPassword = \Utilities\Common::GetRequest("ConfirmPassword");
			
			//validate data
			if ($password != $confirmPassword) {
				$errorMessage = "Passwords do not match";
			}
			
			if ($errorMessage == "" and strlen($password) < 6) {
				$errorMessage = "Password length must be at least 6 characters";
			}
			
			
			if ($errorMessage == "")
			{
				//update user password
				$user->password = password_hash($password, PASSWORD_BCRYPT);
				$user->resetCode = "";
				$objectSave = $user->Save();
				
				if ($objectSave->hasError) {
					$errorMessage = $objectSave->errorMessage;
				}
				else {
					header("Location: reset_password_message.php");
					die();
				}
			}
			
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

						<h2>Reset Password</h2>

						<?php 
						
							if ($user == null) { 
							
								/* 
								 * Reset code is not vaild. Display message if reset code supplied was not empty.
								 * Also, display a form to allow user to enter the reset code in case
								 * there is an issue using the link in the email
								 */
						
						?>

							
						<?php if ($resetCode != "") { ?>
							<div class="alert alert-danger" role="alert">Invalid code supplied.</div>
						<?php } ?>	
						
						<form action="reset_password.php" method="get">
							<div class="form-group">
								<label for="r">Reset Code:</label>
								<input type="text" class="form-control" name="r" maxlength="36" value="<?php echo htmlspecialchars($resetCode) ?>">
							</div>
							<button type="submit" class="btn btn-success">Submit</button>
						</form>
						

						<?php 
							
							} else { 
						
							// Reset code is vaild. Display password reset form
						
						?>

							<p>Reset your password below.</p>
							
							
							<form action="reset_password.php" method="post">
								
								<input type="hidden" name="SubmitForm" value="1">
								<input type="hidden" name="r" value="<?php echo htmlspecialchars($resetCode) ?>">
								
								<?php if ($errorMessage != "") { ?>
									<div class="alert alert-danger" role="alert"><?php echo $errorMessage ?></div>
								<?php } ?>	
								
							
								<div class="form-group">
									<label for="Password">Password:</label>
									<input type="Password" class="form-control" name="Password" id="Password" maxlength="50" value="<?php echo htmlspecialchars($password) ?>">
								</div>
								
								<div class="form-group">
									<label for="ConfirmPassword">Confirm Password:</label>
									<input type="password" class="form-control" name="ConfirmPassword" id="ConfirmPassword" maxlength="50" value="<?php echo htmlspecialchars($confirmPassword) ?>">
								</div>
								
								<button type="submit" class="btn btn-success">Submit</button>
								
							</form>

						<?php } ?>
		
					</div>
				</div>
			</div>
		</div>
		
	<section>	

<?php
	// website page footer
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>	



