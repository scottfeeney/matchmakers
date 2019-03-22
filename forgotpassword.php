<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	require_once "tools.php";

	$errorMessage = "";
	$email = "";
	$password = "";

	
    if (\Utilities\Common::IsSubmitForm())
	{
		
		$email = \Utilities\Common::GetRequest("Email");
		$password = \Utilities\Common::GetRequest("Password");
		
		
		$user = \Classes\User::GetUserLogin($email, $password);
		
		
		if ($user == null) {
			$errorMessage = "Invalid Login";
		}
		else {
			echo "Logged In";
			exit;
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

		
				
		<form action="default.php" method="post">
			
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