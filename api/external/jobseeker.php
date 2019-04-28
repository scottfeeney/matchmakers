<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobseeker.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/skillcategory.php';
        require_once '../../classes/jobseeker.php';
        require_once '../../classes/jobtype.php';
        require_once '../../classes/location.php';
        require_once './apiresult.php';
    }

    if (!isset($_SERVER['HTTP_TOKEN'])) {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","Token not supplied"))->getJSON();
    }

    $token = $_SERVER['HTTP_TOKEN'];
    $user = \Classes\User::GetUserByApiToken($token);
    
    if ($user != null) {
        $jobSeeker = \Classes\JobSeeker::GetJobSeekerByUserId($user->userId);
        if ($jobSeeker != null) {
            //Remove fields not relevant outside the system
            unset($jobSeeker->userId);
            unset($jobSeeker->jobSeekerId);
 
            //Convert locationId to name (if able)
            $location = new \Classes\Location($jobSeeker->locationId);
            if (!isset($location->locationId)) {
                //In retrospect, this should never execute as the foreign key constraint will
                //prevent the locationId from ever being set to something not present in
                //the location table
                $jobSeeker->location = "Error: Not able to retrieve location";
            } else {
                $jobSeeker->location = $location->name;
            }
 
            unset($jobSeeker->locationId);

            //Convert skillCategoryId to name
            $skillCat = new \Classes\SkillCategory($jobSeeker->skillCategoryId);
            $jobSeeker->skillCategory = $skillCat->skillCategoryName;
            unset($jobSeeker->skillCategoryId);

            //Convert jobTypeId to name
            $jobType = new \Classes\JobType($jobSeeker->jobTypeId);
            $jobSeeker->jobType = $jobType->jobTypeName;
            unset($jobSeeker->jobTypeId);
 
            $jobSeeker->email = $user->email;
            echo (new \api\APIResult("success", json_encode($jobSeeker), true))->getJSON();
        } else {
            echo (new \api\APIResult("failure","You are not logged in as an jobseeker"))->getJSON();
        }
    } else {
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

    }

    
?>
