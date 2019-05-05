<?php


if ($_SERVER['DOCUMENT_ROOT'] != '') {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/adminstaff.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skillcategory.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/object_save.php';
} else {
    require_once '../../../classes/user.php';
    require_once '../../../classes/adminstaff.php';
    require_once '../apiresult.php';
    require_once '../../../utilities/common.php';
    require_once '../../../classes/skill.php';
    require_once '../../../classes/skillcategory.php';
    require_once '../../../classes/object_save.php';
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
    $adminStaff = \Classes\AdminStaff::GetAdminStaffByUserId($user->userId);
    if ($adminStaff != null) {
        
        //Code to add skill
        $categoryId = \Utilities\Common::GetRequest("categoryId");
        $skillName = \Utilities\Common::GetRequest("skillName");
        if ($categoryId == "" or $skillName == "") {
            echo (new \api\APIResult("failure","Must provide both categoryId and skillName via POST"))->getJSON();
            exit();
        }

        $category = new \Classes\SkillCategory($categoryId);
        if ($category != null) {
            $skill = new \Classes\Skill();
            $skill->skillCategoryId = $categoryId;
            $skill->skillName = $skillName;
            if (\Classes\Skill::GetSkillExists($skill)) {
                echo (new \api\APIResult("failure","Error attempting to save skill: Skill with same name already exists in specified category"))->getJSON();
            } else {
                $objSave = $skill->save($user);
                if ($objSave->hasError) {
                    echo (new \api\APIResult("failure","Error attempting to save skill: " . $objSave->errorMessage))->getJSON();
                } else {
                    echo (new \api\APIResult("success", "Skill successfully added"))->getJSON();
                }
            }
        } else {
            echo (new \api\APIResult("failure","categoryId does not match a category in our system"))->getJSON();
        }

    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in as an admin"))->getJSON();
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

}



?>
