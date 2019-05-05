<?php

    if ($_SERVER['DOCUMENT_ROOT'] != '') {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/employer.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/api/external/apiresult.php';
    } else {
        require_once '../../classes/user.php';
        require_once '../../classes/employer.php';
        require_once '../../classes/location.php';
        require_once './apiresult.php';
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
        $employer = \Classes\Employer::GetEmployerByUserId($user->userId);

        if ($employer != null) {

            //Remove fields not relevant outside the system
            unset($employer->userId);
            unset($employer->employerId);

            //Convert locationId to name (if able)
            $location = new \Classes\Location($employer->locationId);
            if (!isset($location->locationId)) {
                //In retrospect, this should never execute as the foreign key constraint will
                //prevent the locationId from ever being set to something not present in
                //the location table
                $employer->location = "Error: Not able to retrieve location";
            } else {
                $employer->location = $location->name;
            }

            unset($employer->locationId);

            $employer->email = $user->email;
            echo (new \api\APIResult("success", json_encode($employer), true))->getJSON();
        } else {
            header("HTTP/1.1 401 Unauthorized");
            echo (new \api\APIResult("failure","You are not logged in as an employer"))->getJSON();
        }
    } else {
        header("HTTP/1.1 401 Unauthorized");
        echo (new \api\APIResult("failure","You are not logged in"))->getJSON();
    }

    
?>
