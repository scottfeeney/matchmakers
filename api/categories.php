<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill_category.php';
    } else {
        require_once './classes/skill_category.php';
    }

    echo json_encode(\Classes\SkillCategory::GetSkillCategories());


?>