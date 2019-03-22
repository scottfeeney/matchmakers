<?php
    // Contains database connection info
	require "config.php";
	
	// Contains utility functions
	require "tools.php";
	
	
	// Add head section to page from tools.php
	add_head();
?>
<body>
    <header>
    </header>
    
    <nav>
        
    </nav>
        
    <main class="container">
        <div class="alert alert-success" role="alert">
        	If the email you entered is associated with a user account in our system, you can expect to receive a password reset email shortly.
        </div>
    </main>
    
    <footer>
        
    </footer>
    
    <?php 
        // Add optional bootstrap jQuery, popper.js, and bootstrap.js to page from tools.php
        bootstrap_optional();
    ?>
</body>
</html>
