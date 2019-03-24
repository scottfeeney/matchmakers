<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	
	$resetCode = \Utilities\Common::GetRequest("r");

    // get user based on Verify Code where not already verified
	$user = \Classes\User::GetUserByResetCode($resetCode);
	
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

	$header = new \Template\Header();
	echo $header->Bind();
	
?>	

	<section>

		<h2>Reset Password</h2>

		<?php if ($user == null) { ?>

			
		<?php if ($resetCode != "") { ?>
			<p>Invalid code supplied.</p>
		<?php } ?>	
		
		<form action="reset_password.php" method="get">
			<div class="form-group">
				<label for="r">Reset Code:</label>
				<input type="text" class="form-control" name="r" maxlength="36" value="<?php echo htmlspecialchars($resetCode) ?>">
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
		

		<?php } else { ?>

			<p>Reset your password below.</p>
			
			
			<form action="reset_password.php" method="post">
				
				<input type="hidden" name="SubmitForm" value="1">
				<input type="hidden" name="r" value="<?php echo htmlspecialchars($resetCode) ?>">
				
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
				
				<button type="submit" class="btn btn-primary">Submit</button>
				
			</form>

		<?php } ?>
		
	<section>	

<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>	



