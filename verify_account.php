<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	
	
	$verifyCode = \Utilities\Common::GetRequest("v");

    // get user based on Verify Code where not already verified
	$user = \Classes\User::GetUserByVerifyCode($verifyCode);
	
	// found user
	if ($user != null) {
	
		$errorMessage = "";
		$password = "";
		$confirmPassword = "";
		
		
		// form submitted
		if (\Utilities\Common::IsSubmitForm())
		{
			
			$password = \Utilities\Common::GetRequest("Password");
			$confirmPassword = \Utilities\Common::GetRequest("ConfirmPassword");
			
			if ($password != $confirmPassword) {
				$errorMessage = "Passwords do not match";
			}
			
			if ($errorMessage == "" and strlen($password) < 7) {
				$errorMessage = "Password length must be at least 6 charcaters";
			}
			
			//save user
			if ($errorMessage == "")
			{
				
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

?>




<?php if ($user == null) { ?>

	<p>Invalid Code</p>

<?php } else { ?>

	<p>Create Password</p>
	
	
	<form action="verify_account.php" method="post">
		
		<input type="hidden" name="SubmitForm" value="1">
		<input type="hidden" name="v" value="<?php echo htmlspecialchars($verifyCode) ?>">
		
		<?php if ($errorMessage != "") { ?>
			<p><?php echo $errorMessage ?></p>
		<?php } ?>	
		
	
		<div class="form-group">
			<label for="Password">Password:</label>
			<input type="Password" class="form-control" name="Password" id="Password" maxlength="50" value="<?php echo htmlspecialchars($password) ?>">
		</div>
		
		<div class="form-group">
			<label for="ConfirmPassword">Confirm Password:</label>
			<input type="password" class="form-control" name="ConfirmPassword" id="ConfirmPassword" maxlength="50" value="<?php echo htmlspecialchars($confirmPassword) ?>">
		</div>
		
		<button type="submit" class="btn btn-default">Submit</button>
		
	</form>

<?php } ?>



