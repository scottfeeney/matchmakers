

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


   // $test = array("1" => array("1" => "a\"b", 2 => "b"), "c" => 3, "d" => "4", 5 => array("a" => array("b" => 1, "d" => 4), "c" => 2, "f" => array("a" => 1, "b" => array("a" => 1, "b" => 2))));
  //  var_dump(json_encode($test));
 //   echo "<HR>";
//    var_dump(json_decode(json_encode($test)));

    $jsonData = file_get_contents("./index.json");
    $apiDesc = json_decode($jsonData, TRUE);
//    var_dump($jsonData);
   // var_dump($apiDesc);
   
   $mainSectionCount = 0;

?>

    <section>
			<div class="jumbotron jumbotron-fluid jumbotron-api">
				<div class="container">
					<div class="row">
						
						<div class="col-lg-8 ml-lg-auto mr-lg-auto">

<?php if ($jsonData === null): ?>
							<h2>Server Error</h2>
							<br />
                            <div class="alert alert-danger" role="alert">
								Could not read file containing API description data.
							</div>

<?php else: ?>                    

                            <div class="alert alert-light api-documentation" role="alert">
							

		<h1>JobMatcher API Documentation</h1>
		
		
		
		<?php 
			/* links */
			
			foreach ($apiDesc as $authType => $authArr): 
			
				$mainSectionCount++;
			
			?>
		
			<div class="api-section-link"><a href="#ApiMainSection<?php echo $mainSectionCount; ?>"><span style="margin-right: 10px;"><i class="fas fa-caret-square-right"></i></span><?php echo $authType; ?></a></div>
		<?php endforeach; ?>
		


							
    <?php 
	
			$mainSectionCount=0;
	
			foreach ($apiDesc as $authType => $authArr): 
			
				$mainSectionCount++;
			?>
			
				
                            
                            <div class="api-section-heading" id="ApiMainSection<?php echo $mainSectionCount; ?>"><?php echo $authType; ?></div>
                            <div class="card api-section">
								<div class="card-body"><?php echo $authArr['authDesc']; ?></div>
							</div>
							
							

        <?php foreach ($authArr['endpoints'] as $relativeURL => $endpointArr): ?>
                           <div class="card api-main-title">
								<div class="card-body"><?php echo $endpointArr['desc']; ?></div>
							</div>
                            
                            <div class="section-title">Resource URL</div>
							<div class="card api-section">
								<div class="card-body"><pre><?php echo "\n"."GET ".$relativeURL; ?></pre></div>
							</div>
							
							
							
            <?php if (isset($endpointArr['headerInput'])): ?>
			
					<div class="section-title">HTTP Headers (Required)</div>
					<div class="card api-section">
						<div class="card-body">
							<?php if ($authType != "No Authentication required") { 
								echo " (excluding authentication token required to be sent via 'TOKEN' header)"; 
							} ?>

							<?php foreach ($endpointArr['headerInput'] as $headerName => $headerDesc): ?>

								<div><?php echo "<code class=\"api-code\">" . $headerName . "</code> ".$headerDesc; ?></div>
							<?php endforeach; ?>
					
						</div>
					</div>
					
            <?php endif; ?>

            <?php if (isset($endpointArr['headerOutput'])): ?>
			
					<div class="section-title">HTTP Headers Returned</div>
					<div class="card api-section">
						<div class="card-body">
							<?php foreach ($endpointArr['headerOutput'] as $headerName => $headerDesc): ?>
								<div><?php echo "<code class=\"api-code\">" . $headerName . "</code>  ".$headerDesc; ?></div>
							<?php endforeach; ?>
					
						</div>
					</div>
			
            <?php endif; ?>

            <?php if (isset($endpointArr['input'])): ?>
			
					<div class="section-title">GET Parameters (Required)</div>
					<div class="card api-section">
						<div class="card-body">
							<?php foreach ($endpointArr['input'] as $paramName => $paramDesc): ?>

                            <div><?php echo "<code class=\"api-code\">" . $paramName . "</code> ".$paramDesc; ?></div>
                <?php endforeach; ?>
						
						</div>
					</div>
			
			

            <?php endif; ?>

            <?php if (isset($endpointArr['output'])): ?>

							
						<div class="section-title">Output</div>
						<div class="card api-section">
							<div class="card-body"><?php echo $endpointArr['output']; ?></div>
						</div>
							
							
                <?php if (isset($endpointArr['exampleOutput'])): ?>
				
						<div class="section-title">Example</div>
						<div class="card api-section">
							<div class="card-body"><pre><?php echo $endpointArr['exampleOutput']; ?></pre></div>
						</div>
	

                <?php endif; ?>

            <?php endif; ?>

        <?php endforeach; ?><!-- end foreach ($authArr['endpoints'] as $relativeURL => $endpointArr) -->
    <?php endforeach; ?><!-- end foreach ($apiDesc as $authType => $authArr) -->
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