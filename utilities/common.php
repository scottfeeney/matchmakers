<?php
	
	//----------------------------------------------------------------
	// Common class - Common utility functions used throughout the website
	//----------------------------------------------------------------
	
	namespace Utilities;
	
	if ($_SERVER['DOCUMENT_ROOT'] != '') {
		require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
	} else {
		require_once './classes/user.php';
	}
	

	class Common {

		/*
		* GetRequest - returns HTTP Request variable value
		*/	
		public static function GetRequest($Value) {
			return (isset($_REQUEST[$Value]) ? trim($_REQUEST[$Value]) : "");
		}

		/*
		* IsSubmitForm - checks if form was submitted
		*/	
		public static function IsSubmitForm() {
			if (Common::GetRequest("SubmitForm") == "1") {
				return true;
			}
			else {
				return false;			
			}
		}
		
		/*
		* GetSessionUser - returns user based session
		* If user has not entered details, redirects them to appropriate page
		* If user not found, redirects to login form
		*/	
		public static function GetSessionUser() {
			
			session_start();
			if(isset($_SESSION["UserId"]) && !empty($_SESSION["UserId"])) {
				$user = new \Classes\User($_SESSION["UserId"]);
				
				$pageName = basename($_SERVER['PHP_SELF']);
				
				if ($user->enteredDetails == false) 
				{
					if ($user->userType == 1 && $pageName != "employer_details.php")
					{
						header('Location: /employer_details.php');
						exit;
					}
					else if ($user->userType == 2 && ($pageName != "job_seeker_details.php" && $pageName != "skills_control.php"))
					{
						header('Location: /job_seeker_details.php');
						exit;
					}
				}
				
				return $user;
				
			}
			else {
				
				header('Location: /');
				exit;
			}
		}

		/*
		* IsValidEmail - checks for a valid email
		*/			
		public static function IsValidEmail($emailAddress) {
		
			if (strlen($emailAddress) <= 6) {
				return False;
			}
		
			$atPos = strpos($emailAddress, "@");
			
			if ($atPos === False) {
				return false;
			}
			
			if ($atPos == 0) {
				return false;
			}
			
			if ((strlen($emailAddress) -( $atPos + 1)) < 5) {
				return false;
			}

			if (strpos($emailAddress, "@", ($atPos + 1)) !== false) {
				return False;
			}
			
			if (strpos($emailAddress, ".", ($atPos + 1)) === false) {
				return False;
			}
			
			if (strpos($emailAddress, "@.") !== false) {
				return false;
			}
		
			if (strpos($emailAddress, ".@") !== false) {
				return false;
			}
			
			if (strpos($emailAddress, "..") !== false) {
				return false;
			}
		
			if (substr($emailAddress, 0, 1) == ".") {
				return false;
			}
		
			if ((strlen($emailAddress) - (strrpos($emailAddress, ".") +1 )) < 2) {
				return false;
			}
			
			for ($i=0; $i < strlen($emailAddress); $i++) {
								
				$asciiNum = ord(substr($emailAddress, $i, 1));
				
				if ($asciiNum < 32) {
					return false;
				}
				
				if ($asciiNum > 126) {
					return false;
				}
				
				if ($asciiNum == 32 || $asciiNum == 34 || $asciiNum == 40 || $asciiNum == 41 || $asciiNum == 44 || $asciiNum == 58 || $asciiNum == 59 || $asciiNum == 60 || $asciiNum == 62 || $asciiNum == 91 || $asciiNum == 93) {
					return false;
				}
		
			}
		
			return true;
		}
		
		/*
		* GetGuid - creates a guid value
		* From: http://php.net/manual/en/function.com-create-guid.php
		*/	
		public static function GetGuid()
		{
			if (function_exists('com_create_guid') === true)
			{
				return trim(com_create_guid(), '{}');
			}

			return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		}
		
		/*
		* SendEmail - sends a plain text email
		*/			
		public static function SendEmail($to, $subject, $message)
		{
			
			// to help testing, anything after the pipe before the @ symbol is ignored
			$toEmail = $to;
			
			$toParts = explode("@", $to);
			if (strpos($toParts[0], '|') !== false) {
				$toNameParts = explode("|", $toParts[0]);
				$toEmail = $toNameParts[0]."@".$toParts[1];
			}
			
			$headers = array(
				'From' => 'Job Matcher <jobmatcher@ec2-18-221-135-88.us-east-2.compute.amazonaws.com>',
				'MIME-Version' => '1.0',
				'Content-Type' => 'text/plain; charset=us-ascii',
				'Content-Transfer-Encoding' => 'quoted-printable'
			);

			mail($toEmail, $subject, $message, $headers);
		}
		
		/*
		* GetSkillsControl - generates a skill control. Used on job and job seeker forms
		*/			
		public static function GetSkillsControl($skills, $selectedSkills) {
		
			$html = '<div class="dropdown skills-control-dropdown">
						  <button class="btn btn-secondary dropdown-toggle skills-control-dropdown-button" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
						  <div class="dropdown-menu skills-control-dropdown-menu"></div>
					</div>

					<div class="skills-control-selected-skills"></div>
			
				<input type="hidden" id="SkillsControlSelectedSkills" name="SkillsControlSelectedSkills" value="' . $selectedSkills . '" />
				<script>
					var skills = [';
					
						// create a javascript array containing skills
					
						$javaScriptSkills = Array();
							
						foreach ($skills as $skill) {
							
							$javaScriptSkills[] = '{"skillId": "' . $skill->skillId . '", "skillName": "' . htmlspecialchars(str_replace("\"","\\\"", $skill->skillName)) . '"}';
							
						}
						$html .= join(",", $javaScriptSkills);
				
					$html .= '];
				
				</script>';
			
			return $html;
			
		}
		
		/*
		* DisplayDate - displays a formated date
		*/	
		public static function DisplayDate($value) {
			
			if (empty($value)) {
				return "";
			}
			else {
				return date('d-M-Y', strtotime($value));
			}
			
		}
		
		/*
		* GetCheckedSelectedSkills - checks supplied skill ids are vaild
		* and return a string of vaild skill ids
		*/	
		public static function GetCheckedSelectedSkills($selectedSkills) {
			
			$checkedSelectedSkills = Array();
			
			foreach (explode(",", $selectedSkills) as $selectedSkill) {
				if (Common::IsInteger($selectedSkill)) {
					$checkedSelectedSkills[] = $selectedSkill;
				}
			}
			
			return join(",", $checkedSelectedSkills);
			
		}
		
		/*
		* IsInteger - checks if the value is an int
		*/	
		public static function IsInteger($value){
			return (ctype_digit(strval($value)));
		}
		
	}
	
?>		
