<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiResult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/employer.php';
        require_once './apiResult.php';
    }

    if (!isset($_SERVER['HTTP_TOKEN'])) {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","Token not supplied"))->getJSON();
    }

    $token = $_SERVER['HTTP_TOKEN'];
    $user = \Classes\User::GetUserByApiToken($token);
    
    if ($user != null) {
        $employer = \Classes\Employer::GetEmployerByUserId($user->userId);
        if ($employer != null) {
            echo (new \api\APIResult("success", json_encode($employer), true))->getJSON();
        } else {
            echo (new \api\APIResult("failure","You are not logged in as an employer"))->getJSON();
        }
    } else {
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

    }

    
?>