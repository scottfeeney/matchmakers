<?php

    namespace api;

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
    } else {
        require_once './classes/skillcategory.php';
    }

    echo json_encode(\Classes\SkillCategory::GetSkillCategories());


?>
