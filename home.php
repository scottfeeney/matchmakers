<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	
	$header = new \Template\Header();
	echo $header->Bind();
	
?>	

        <section>

			<h2>Home</h2>
			
			

		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
