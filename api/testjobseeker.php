<?php

	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/user.php';
		require_once './classes/jobseeker.php';
	}
	
	
	$token = $_SERVER['HTTP_TOKEN'];
	
	$user = \Classes\User::GetUserByApiToken($token);
	
	if ($user == null || $user->userType != 2) {
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}
	else {
		
		
		$jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
		
		//create a temp object to filter data we pass back
		
		$object = new StdClass;
		$object->title = $jobSeeker->title;
		$object->firstName = $jobSeeker->firstName;
		$object->lastName = $jobSeeker->lastName;
		echo json_encode($object);
	
	}
	
	

?>