<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobSeeker.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiResult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/jobSeeker.php';
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
            echo (new \api\APIResult("success", json_encode($jobSeeker), true))->getJSON();
        } else {
            echo (new \api\APIResult("failure","You are not logged in as an jobseeker"))->getJSON();
        }
    } else {
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

    }

    
?>