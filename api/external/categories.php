<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    } else {
        require_once './apiresult.php';
        require_once './classes/skillcategory.php';
    }

    if (!isset($_SERVER['HTTP_TOKEN'])) {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","Token not supplied"))->getJSON();
        die();
    }

    echo (new \api\APIResult("success", json_encode(\Classes\SkillCategory::GetSkillCategories()), true))->getJSON();


?>
