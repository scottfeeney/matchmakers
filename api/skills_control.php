<?php

	//----------------------------------------------------------------
	// API - Returns skills data in json format to Skills Control
	//----------------------------------------------------------------
	
	// include required php files, for website and PHPUnit
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/utilities/common.php';
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/skill.php';
	} else {
		require_once './utilities/common.php';
		require_once './classes/skill.php';
	}

	// get user from session
	$user = \Utilities\Common::GetSessionUser();
	
	$rest_json = file_get_contents("php://input");
	$_POST = json_decode($rest_json, true);

	// get mode supplied by ajax call
	$mode = isset($_POST['Mode']) ? $_POST['Mode'] : null;
	
	if ($mode == "control") {
		
		// return skills for a category
		
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

