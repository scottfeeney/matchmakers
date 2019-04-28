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

if (!isset($_SERVER['HTTP_TOKEN'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo (new \api\APIResult("failure","Token not supplied"))->getJSON();
}

$token = $_SERVER['HTTP_TOKEN'];
$user = \Classes\User::GetUserByApiToken($token);

if ($user != null) {
    $adminStaff = \Classes\AdminStaff::GetAdminStaffByUserId($user->userId);
    if ($adminStaff != null) {
        
        //Code to add skill
        $categoryId = \Utilities\Common::GetRequest("categoryId");
        $skillId = \Utilities\Common::GetRequest("skillId");
        if ($categoryId == "" or $skillId == "") {
            echo (new \api\APIResult("failure","Must provide categoryId and skillId via POST or GET"))->getJSON();
            exit();
        }

        $category = new \Classes\SkillCategory($categoryId);
        if ($category != null) {
            $skill = new \Classes\Skill($skillId);
            if ($skill != null) {
                if ($skill->skillCategoryId == $category->skillCategoryId) {
                    $objSave = \Classes\Skill::DeleteSkill($skillId);
                    if ($objSave->hasError) {
                        echo (new \api\APIResult("failure","Error attempting to delete skill: " . $objSave->errorMessage))->getJSON();
                    } else {
                        echo (new \api\APIResult("success", "Skill successfully deleted"))->getJSON();
                    }                
                } else {
                    echo (new \api\APIResult("failure","skillId does not represent a skill in the category represented by categoryId"))->getJSON();
                }

            } else {
                echo (new \api\APIResult("failure","skillId does not match a skill in our system"))->getJSON();
            }
        } else {
            echo (new \api\APIResult("failure","categoryId does not match a category in our system"))->getJSON();
        }

    } else {
        echo (new \api\APIResult("failure","You are not logged in as an admin"))->getJSON();
    }
} else {
    echo (new \api\APIResult("failure","You are not logged in"))->getJSON();

}



?>
