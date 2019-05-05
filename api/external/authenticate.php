<?php

	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	} else {
		require_once '../../utilities/common.php';
		require_once '../../classes/user.php';
	}

	$emailFound = false;
	$passwordFound = false;

	foreach ($_SERVER as $key => $value) {
		if ($key == 'HTTP_EMAIL') { 
			$emailFound = true;
		} elseif ($key == 'HTTP_PASSWORD') {
			$passwordFound = true;
		}
	}

	if (!$emailFound || !$passwordFound) {
		//exit early to prevent HTML error messages being returned
		header("HTTP/1.1 401 Unauthorized");
		//var_dump($_SERVER);
		exit;
	}

	$email = $_SERVER['HTTP_EMAIL'];
	$password = $_SERVER['HTTP_PASSWORD'];
	
	$user = \Classes\User::GetUserLogin($email, $password);
	
	if ($user == null) {
		header("HTTP/1.1 401 Unauthorized");
		//var_dump($_SERVER);
		exit;
	}
	else {
		$token = $user->CreateApiToken();
		header("Token: " . $token);
	}
	
	
	

?>
