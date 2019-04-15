<?php
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/header.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/footer.php';
	} else {
		require_once './classes/header.php';
		require_once './classes/footer.php';
	}
	
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
						
							<h2>Sign Up</h2>
							<br />
							<div class="alert alert-success" role="alert">
								Thank you for signing up.
								<br />You have been sent an email with a link to verify your account and create your password.
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
    
<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>
