<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './classes/header.php';
		require_once './classes/footer.php';
	}
	
	$header = new \Template\Header();
	echo $header->Bind();
	
?>	

        <section>

			<h2>Forgot Password</h2>
			
			<div class="alert alert-success" role="alert">
        	If the email you entered is associated with a user account in our system, you can expect to receive a password reset email shortly.
        </div>

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
