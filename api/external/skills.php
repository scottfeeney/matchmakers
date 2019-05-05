<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
    } else {
        require_once '../../classes/skill.php';
        require_once '../../classes/skillcategory.php';
        require_once './apiresult.php';
        require_once '../../classes/user.php';
        require_once '../../utilities/common.php';
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
        $categoryId = \Utilities\Common::GetRequest("categoryId");
        if ($categoryId == "") {
            echo (new APIResult("failure","categoryId not provided"))->getJSON();
            exit();
        }

        $category = new \Classes\SkillCategory($categoryId);
        if ($category->skillCategoryName != null) {
            echo (new APIResult("success",json_encode(\Classes\Skill::GetSkillsBySkillCategory($categoryId)), true))->getJSON();
        } else {
            echo (new APIResult("failure","invalid categoryId"))->getJSON();
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();
    }



?>
