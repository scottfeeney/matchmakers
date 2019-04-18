<?php

	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/skill.php';
	}

	$user = \Utilities\Common::GetSessionUser();
	

	$rest_json = file_get_contents("php://input");
	$_POST = json_decode($rest_json, true);

	$mode = isset($_POST['Mode']) ? $_POST['Mode'] : null;
	
	if ($mode == "control") {
		
		$skillCategoryId = isset($_POST['SkillCategoryId']) ? $_POST['SkillCategoryId'] : null;
		
		if ($skillCategoryId == "") { 
			echo json_encode("");
		}
		else {
			$skills = \Classes\Skill::GetSkillsBySkillCategory($skillCategoryId);
			echo json_encode(\Utilities\Common::GetSkillsControl($skills, "")); 
		}
		
	}
	
?>

