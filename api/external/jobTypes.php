<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/jobtype.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiResult.php';
    } else {
        require_once './apiResult.php';
        require_once './classes/jobtype.php';
    }

    $jobTypes = \Classes\JobType::GetJobTypes();



    echo (new \api\APIResult("success", json_encode(\Classes\JobType::GetJobTypes()), true))->getJSON();


?>
