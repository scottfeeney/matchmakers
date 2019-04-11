<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill_category.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/api_error.php';
    } else {
        require_once './classes/skill.php';
        require_once './classes/skill_category.php';
        require_once './api/api_error.php';
    }

	$rest_json = file_get_contents("php://input");
    $_POST = json_decode($rest_json, true);

    $skillCategoryId = isset($_POST['SkillCategoryId']) ? $_POST['SkillCategoryId'] : null;

    if ($skillCategoryId != null) {
        $categories = \Classes\SkillCategory::GetSkillCategories();
        foreach ($categories as $category) {
            if ($skillCategoryId == $category->skillCategoryId) {
                echo json_encode(\Classes\Skill::GetSkillsBySkillCategory($skillCategoryId));
                die;
            }
        }
        //ID given does not match skill category currently in use
        echo (new APIError("failure","invalid skill category ID"))->getJSON();
    } else {
        echo (new APIError("failure","must specify skill category ID"))->getJSON();
    }


?>