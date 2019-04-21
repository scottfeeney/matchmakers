<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
    } else {
        require_once '../../classes/skill.php';
        require_once '../../classes/skillcategory.php';
        require_once './apiresult.php';
        require_once '../../utilities/common.php';
    }

    $categories = \Classes\SkillCategory::GetSkillCategories();

    $allSkills = array();

    foreach ($categories as $skill) {
        $allSkills[$skill->skillCategoryName] = \Classes\Skill::GetSkillsBySkillCategory($skill->skillCategoryId);
    }

    echo (new APIResult("success",json_encode($allSkills), true))->getJSON();
?>
