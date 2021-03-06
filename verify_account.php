<?php

	//----------------------------------------------------------------
	// Verify Account Page - Includes form to set password
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
	
	
	//get verify code
	$verifyCode = \Utilities\Common::GetRequest("v");

	/*
	 * now trim the 'v' at the start of the verify code, placed there to prevent issues with email clients interpreting
	 * a sequence starting with = then hexadecimal characters as a unicode char
	 * (check not already done as this page will be loaded more than once during password setting process)
	 */
	if (substr($verifyCode,0,1) == 'v') {
		$verifyCode = substr($verifyCode,1);
	}


    // get user based on Verify Code where not already verified
	$user = \Classes\User::GetUserByVerifyCode($verifyCode);
	
	// found user based on verify code
	if ($user != null) {
		
		// functionality to process password form
	
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
				$errorMessage = "Password length must be at least 6 charcaters";
			}
			
			if ($errorMessage == "")
			{
				//update user password
				$user->password = password_hash($password, PASSWORD_BCRYPT);
				$user->verified = true;
				$objectSave = $user->Save();
				
				if ($objectSave->hasError) {
					$errorMessage = $objectSave->errorMessage;
				}
				else {
					header("Location: verify_message.php");
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
						
					<h2>Verify Account</h2>

					<?php
					
						if ($user == null) { 
						
							/* 
							 * Verify code is not vaild. Display message if verify code supplied was not empty.
							 * Also, display a form to allow user to enter the verify code in case
							 * there is an issue using the link in the email
							 */
						
					?>

						<?php if ($verifyCode != "") { ?>
							<div class="alert alert-danger" role="alert">Invalid code supplied.</div>
						<?php } ?>	
						
						<form action="verify_account.php" method="get">
							<div class="form-group">
								<label for="v">Verification Code:</label>
								<input type="text" class="form-control" name="v" maxlength="36" value="<?php echo htmlspecialchars($verifyCode) ?>">
							</div>
							<button type="submit" class="btn btn-success">Submit</button>
						</form>

					<?php 
					
						} else { 
						
						// Verify code is vaild. Display password form
						
						
					?>

						<p>Your account has been verified, please create your password below.</p>
						
						
						<form action="verify_account.php" method="post">
							
							<input type="hidden" name="SubmitForm" value="1">
							<input type="hidden" name="v" value="<?php echo htmlspecialchars($verifyCode) ?>">
							
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
</section>
	
<?php
	// website page footer
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
	



