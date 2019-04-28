

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
                            <div class="alert alert-light" role="alert">
    <?php foreach ($apiDesc as $authType => $authArr): ?>
                            <HR>
                            <h2><?php echo $authType; ?></h2>
                            <br />
                            <?php echo $authArr['authDesc']; ?>

        <?php foreach ($authArr['endpoints'] as $relativeURL => $endpointArr): ?>
                            <br />
                            <br />
                            <br />
                            <br />
                            <h3><?php echo $endpointArr['desc']; ?></h3>
                            <br />
                            <pre>
                                <code>
                                    <?php echo "\n"."GET ".$relativeURL; ?>
                                </code>
                            </pre>
            <?php if (isset($endpointArr['headerInput'])): ?>
                            <br />
                            <br />
                            HTTP Header input required<?php if ($authType != "No Authentication required") { 
                                                                echo " (excluding authentication token required to be sent via 'TOKEN' header)"; 
                                                            } ?>:

                <?php foreach ($endpointArr['headerInput'] as $headerName => $headerDesc): ?>
                            <br />
                            <br />
                            <?php echo $headerName.": ".$headerDesc; ?>
                <?php endforeach; ?>

                            <br />
            <?php endif; ?>

            <?php if (isset($endpointArr['headerOutput'])): ?>
                            <br />
                            <br />
                            HTTP Header output returned:

                <?php foreach ($endpointArr['headerOutput'] as $headerName => $headerDesc): ?>
                            <br />
                            <br />
                            <?php echo $headerName.": ".$headerDesc; ?>
                <?php endforeach; ?>

                            <br />
            <?php endif; ?>

            <?php if (isset($endpointArr['input'])): ?>
                            <br />
                            <br />
                            GET Parameter input required:                
                <?php foreach ($endpointArr['input'] as $paramName => $paramDesc): ?>

                            <br />
                            <br />
                            <?php echo $paramName.": ".$paramDesc; ?>
                <?php endforeach; ?>

            <?php endif; ?>

            <?php if (isset($endpointArr['output'])): ?>
                            <br />
                            <br />
                            Output: <?php echo $endpointArr['output']; ?>
                <?php if (isset($endpointArr['exampleOutput'])): ?>
                            <br />
                            <br />
                            Example:
                            <pre>
                                <code>
                                    <?php echo $endpointArr['exampleOutput']; ?>
                                </code>
                            </pre>

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