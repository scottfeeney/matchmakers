<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './wwwroot/utilities/common.php';
		require_once './wwwroot/classes/header.php';
		require_once './wwwroot/classes/footer.php';
	}
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>Home Page</h2>
			
			<p>Welcome to Job Matcher</p>
			
			<?php if ($user->userType == 1) { ?>
			
				<p><a href="employer_details.php">Click here</a> to update your details.</p>

			<?php } ?>
			
			

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
