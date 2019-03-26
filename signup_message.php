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
            
			<h2>Sign Up</h2>
				
			<div class="alert alert-success" role="alert">
				You have been sent an email.
			</div>
    
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
