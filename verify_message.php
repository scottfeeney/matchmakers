<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	
	$header = new \Template\Header();
	echo $header->Bind();
	
?>	

        <section>

			<h2>Verify Account</h2>
			
			<p>Your password has been saved.</p>
			
			<p><a href="/default.php">Click here</a> to sign in.</p>

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
