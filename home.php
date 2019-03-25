<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	
	
	$user = \Utilities\Common::GetSessionUser();
	
	
	$header = new \Template\Header();
	$header->isSignedIn = true;
	echo $header->Bind();
	
?>	

        <section>

			<h2>Home Page</h2>
			
			

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
