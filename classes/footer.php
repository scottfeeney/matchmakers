<?php
	
	//----------------------------------------------------------------
	// Footer class - Website page footer Html
	//----------------------------------------------------------------
	
	namespace Template;
	
	class Footer {
	
		function Bind() {

			$html = '	</div>
			
					</div>
					<footer>
						<div>Job Matcher © 2019 - <a href="/signup.php">Sign Up</a> | <a href="/api/external/index.php">API</a></div>
					</footer>
		
					<script
					  src="https://code.jquery.com/jquery-3.3.1.min.js"
					  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
					  crossorigin="anonymous"></script>
					<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" 
							integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" 
							crossorigin="anonymous">
					</script>
					<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" 
							integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" 
							crossorigin="anonymous">
					</script>
					<script src="/js/site.js"></script>
				</body>
				</html>';
				

			return $html;
		
		}

	}
	
?>	