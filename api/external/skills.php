<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiResult.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
    } else {
        require_once '../../classes/skill.php';
        require_once '../../classes/skillcategory.php';
        require_once './apiResult.php';
        require_once '../../utilities/common.php';
    }

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




?>
