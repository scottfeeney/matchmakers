<?php
	
	// sign out user and redirect to home page.
	
	session_start();
    session_unset();
    session_destroy();
	header("Location: /");
	exit;
?>
