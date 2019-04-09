<?php

	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/skill.php';
	}

	$user = \Utilities\Common::GetSessionUser();
	
	if ($user->userType != 3) {
		// not staff
		die();		
	}
	
	$rest_json = file_get_contents("php://input");
	$_POST = json_decode($rest_json, true);

	$mode = isset($_POST['Mode']) ? $_POST['Mode'] : null;
	
	if ($mode == "save") {
		
		$skillId = isset($_POST['SkillId']) ? $_POST['SkillId'] : null;
		$skillCategoryId = isset($_POST['SkillCategoryId']) ? $_POST['SkillCategoryId'] : null;
		$skillName = isset($_POST['SkillName']) ? $_POST['SkillName'] : null;
		
		
		// save skill
		$skill = new \Classes\Skill($skillId);
		$skill->skillCategoryId = $skillCategoryId;
		$skill->skillName = trim($skillName);
		$objectSave = $skill->Save($user);
		
		echo json_encode($objectSave);
		
	}
	else if ($mode == "list") {
		
		$skillCategoryId = isset($_POST['SkillCategoryId']) ? $_POST['SkillCategoryId'] : null;
		echo json_encode(\Classes\Skill::GetSkillsBySkillCategory($skillCategoryId));
	}
	else if ($mode == "delete") {
		
		$skillId = isset($_POST['SkillId']) ? $_POST['SkillId'] : null;
		echo json_encode(\Classes\Skill::DeleteSkill($skillId));
	}
?>

