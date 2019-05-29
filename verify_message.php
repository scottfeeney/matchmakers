<?php

	//----------------------------------------------------------------
	// Verify Account Message
	//----------------------------------------------------------------
	
	// include required php files, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './classes/header.php';
		require_once './classes/footer.php';
	}
	
	// website page header
	$header = new \Template\Header();
	$header->isHomePage = true;
	$header->showMainBanner = false;
	echo $header->Bind();
	
?>	

        <section>
			<div class="jumbotron jumbotron-fluid">
			
				<div class="container">
		
					<div class="row">
					
						<div class="col-lg-6 ml-lg-auto mr-lg-auto">

						
							<h2>Verify Account</h2>
							
							<br />
							
							<div class="alert alert-success" role="alert">
							
								Your account has been verified and password created.
							
								<br /><a href="/default.php">Click here</a> to sign in.
							
							</div>
							
						</div>
						
					</div>
					
				</div>
				
			</div>

		</section>
    
<?php
	// website page footer
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
