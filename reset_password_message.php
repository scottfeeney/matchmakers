<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './wwwroot/classes/header.php';
		require_once './wwwroot/classes/footer.php';
	}
	
	$header = new \Template\Header();
	echo $header->Bind();
	
?>	

        <section>

			<h2>Password Reset</h2>
			
			<p>Your password has been reset.</p>
			
			<p><a href="/default.php">Click here</a> to sign in.</p>

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
