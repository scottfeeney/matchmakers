

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

    //File intended to grab data from index.json in same directory and display it nicely

    $jsonData = file_get_contents("./index.json");

?>

    <section>
			<div class="jumbotron jumbotron-fluid">
				<div class="container">
					<div class="row">
						
						<div class="col-lg-6 ml-lg-auto mr-lg-auto">
<?php if ($jsonData === null): ?>
							<h2>Server Error</h2>
							<br />
                            <div class="alert alert-danger" role="alert">
								Could not read file containing API description data.
							</div>
<?php else: ?>                    
                            <h1>JobMatcher API documentation</h1>
							<br />
<!-- TODO:  main segments for different required authentication types (h2s).
            HR
            h3s for description of endpoint
            pre and code tags for actual endpoint
            h4 for Response label
            pre and code tags for header and example response (separately)
            HR
-->

							<div class="alert alert-success" role="alert">
								If the email you entered is associated with a user account in our system, you can expect to receive a password reset email shortly.
							</div>
<?php endif; ?>
						</div>
						
					</div>
					
				</div>
				
			</div>
					
		</section>


<?php
	$footer = new \Template\Footer();
	echo $footer->Bind();
?>