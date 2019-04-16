<?php

	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/user.php';
	}
	
	$email = $_SERVER['HTTP_EMAIL'];
	$password = $_SERVER['HTTP_PASSWORD'];
	
	$user = \Classes\User::GetUserLogin($email, $password);
	
	if ($user == null) {
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}
	else {
		$token = $user->CreateApiToken();
		header("Token: " . $token);
	}
	
	
	

?>