<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/employer.php';
        require_once '../../classes/job.php';
        require_once '../../classes/jobseeker.php';
        require_once '../../classes/skillcategory.php';
        require_once '../../../utilities/common.php';
        require_once '../../classes/jobtype.php';
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
        $employer = \Classes\Employer::GetEmployerByUserId($user->userId);
        if ($employer != null) {
            $jobid = \Utilities\Common::GetRequest("jobId");
            if ($jobid == "") {
                echo (new \api\APIResult("failure","Must provide jobId via POST"))->getJSON();
                exit();
            }
            $job = new \Classes\Job($jobid);
            if ($job == null) {
                echo (new \api\APIResult("failure","Provided jobId does not match a job in the system."))->getJSON();
            } elseif ($job->employerId != $employer->employerId) {
                echo (new \api\APIResult("failure","Job with provided jobId was posted by another employer. You can only view matches for jobs you have posted."))->getJSON();
            } else {
                $matches = \Classes\JobSeeker::GetJobSeekerMatchesByJob($jobid);
                echo (new \api\APIResult("success", json_encode($matches), true))->getJSON();                
            }
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo (new \api\APIResult("failure","You are not logged in as an employer"))->getJSON();
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

    }

    
?>
