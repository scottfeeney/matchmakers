<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobSeeker.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiResult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/jobSeeker.php';
        require_once '../../classes/location.php';
        require_once './apiResult.php';
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
                $jobSeeker->location = "Error: Not able to retrieve location";
            } else {
                $jobSeeker->location = $location->name;
            }
 
            unset($jobSeeker->locationId);
 
            $jobSeeker->email = $user->email;
            echo (new \api\APIResult("success", json_encode($jobSeeker), true))->getJSON();
        } else {
            echo (new \api\APIResult("failure","You are not logged in as an jobseeker"))->getJSON();
        }
    } else {
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

    }

    
?>