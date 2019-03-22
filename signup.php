<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
	require_once "tools.php";
	
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
				$message = "To verify your email address click the following link: ".SITE_URL."/verify_account.php?v=".$user->verifyCode."\n\n\nCode Url: /verify_account.php?v = ".$user->verifyCode."\n\n\nCode: ".$user->verifyCode;
				\Utilities\Common::SendEmail($email, "Joining Job Matcher", $message);
				
				header("Location: signup_message.php");
				die();
			}
			
		}
		else {
			$errorMessage = "The email entered is invalid";
		}
		
	}
		
	// Add head section to page from tools.php
	add_head();
?>	

<body>
    <header>

    </header>
    
    <nav>
        
    </nav>
        
    <main class="container">
        <section>
            <h1>Welcome!</h1>

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
    </main>
    
    <footer>
        
    </footer>
    
    <?php 
        // Add optional bootstrap jQuery, popper.js, and bootstrap.js to page from tools.php
        bootstrap_optional();
    ?>
</body>
</html>

