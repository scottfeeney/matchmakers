<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/jobseeker.php';
        require_once '../../classes/job.php';
        require_once '../../classes/location.php';
        require_once './apiresult.php';
    }

    $tokenFound = false; 

    foreach ($_SERVER as $key => $value) {
		if ($key == 'HTTP_TOKEN') { 
			$tokenFound = true;
		}
	}

	if (!$tokenFound) {
		//exit early to prevent HTML error messages being returned
		header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","Token not supplied"))->getJSON();
        die();
    }

    $token = $_SERVER['HTTP_TOKEN'];
    $user = \Classes\User::GetUserByApiToken($token);
    
    if ($user != null) {
        $jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
        if ($jobSeeker != null) {
            $jobMatches = \Classes\Job::GetJobMatchesByJobSeeker($jobSeeker->jobSeekerId);
            echo (new \api\APIResult("success", json_encode($jobMatches), true))->getJSON();
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo (new \api\APIResult("failure","You are not logged in as an jobseeker"))->getJSON();
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();
    }

    
?>
