<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/job.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/employer.php';
        require_once '../../classes/job.php';
        require_once '../../classes/skillcategory.php';
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
            $jobs = \Classes\Job::GetJobsByemployer($employer->employerId);
            
            //Get rid of fields the employer doesn't need to know, and replace
            //foreign keys with relevant data
            foreach ($jobs as $key => $job) {
                unset($jobs[$key]->employerId);
                $location = new \Classes\Location($job->locationId);
                $jobs[$key]->location = $location->name;
                unset($jobs[$key]->locationId);
                $jobType = new \Classes\JobType($job->jobTypeId);
                $jobs[$key]->jobType = $jobType->jobTypeName;
                unset($jobs[$key]->jobTypeId);
                $skillCat = new \Classes\SkillCategory($job->skillCategoryId);
                $jobs[$key]->skillCategory = $skillCat->skillCategoryName;
                unset($jobs[$key]->skillCategoryId);
            }

            echo (new \api\APIResult("success", json_encode($jobs), true))->getJSON();
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo (new \api\APIResult("failure","You are not logged in as an employer"))->getJSON();
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();
    }

    
?>
