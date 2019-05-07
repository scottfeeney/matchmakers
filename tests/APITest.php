<?php

/** 
 * Class to test functionality of API Classes
 * 
 * Having said as much, the core functionality of these classes revolves around functions that have already been tested in
 * the existing unit tests
 * 
 * As such, this class will focus on testing the authentication process, and making sure that the correct error messages
 * are returned in the case of invalid input
 * 
 * Further, as there isn't expected to be that much to test for each class, I'll be putting all the API tests in this
 * testing class rather than creating separate ones for each endpoint
 * 
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class APITest extends TestCase {

    //Basic config data and curl object
    private $curlObj;
    private $baseURL;
    private $baseProdURL;

    //Emails to be used to create test user records
    private $testEmployerEmail = "JustTestingAnEmployer__123#@321.com";
    private $testEmployer2Email = "JustTestingAnEmployr2__123#@321.com";
    private $testJobSeekerEmail = "JustTestingAJobSeeker__123#@321.com";
    private $testAdminEmail = "JustTestingAnAdmin__123#@321.com";

    //Test user records
    private $testEmployer;
    private $testEmployer2;
    private $testJobSeeker;
    private $testAdminStaff;

    //easy access for ids for dummy skill categories and skills
    private $testSkillCatId;
    private $testSkillCat2Id;
    private $skill1cat1Id;
    private $skill2cat1Id;
    private $skill1cat2Id;

    //lookup for userTypes
    private $userTypes = array(1 => "employer", 2 => "jobseeker", 3 => "admin");
    
    /**
     * authenticate.php
     */
    

    public function testAuthenticateNoHeadersFailure() {
        curl_setopt($this->curlObj, CURLOPT_URL, $this->baseURL. 'authenticate.php');
        $data = curl_exec($this->curlObj);
        $this->assertEquals(401, curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE), "Failed to return 401 code when authenticate called without correct headers set");
    }

    public function testAuthenticateIncorrectValues() {
        curl_setopt($this->curlObj, CURLOPT_URL, $this->baseURL. 'authenticate.php');
        $sendHeaders = array("EMAIL" => "", "PASSWORD" => ""); //known to be no good as email cannot be blank, nor password
        $data = curl_exec($this->curlObj);
        $this->assertEquals(401, curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE), "Failed to return 401 code when authenticate called with invalid username and password");
    }

    public function testAuthenticateIncorrectPassword() {
        curl_setopt($this->curlObj, CURLOPT_URL, $this->baseURL. 'authenticate.php');
        $sendHeaders = array("EMAIL" => $this->testEmployerEmail, "PASSWORD" => ""); //null is not blank
        $data = curl_exec($this->curlObj);
        $this->assertEquals(401, curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE), "Failed to return 401 code when authenticate called with legit username and invalid password");
    }

    /**
     * Helper functions
     */

    public static function authenticateGetToken($uid, $email, $curlObj, $baseURL) {
        //set password
        $user = new \Classes\User($uid);
        $user->password = password_hash($email, PASSWORD_BCRYPT);
        $user->verified = 1;
        $objSave = $user->Save();
        //var_dump($objSave);
        if ($objSave->hasError) {
            return array(false, 'Attempt to save user with password set to same as email failed: '.$objSave->errorMessage);
        }

        //set cURL opts and make request
        curl_setopt($curlObj, CURLOPT_HEADER, 1);
        //curl_setopt($curlObj, CURLINFO_HEADER_OUT, 1);
        curl_setopt($curlObj, CURLOPT_URL, $baseURL. 'authenticate.php');
        $sendHeaders = array("EMAIL: ".$email, "PASSWORD: ".$email);

        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $sendHeaders);
        $data = curl_exec($curlObj);
        //var_dump(curl_getinfo($this->curlObj));
        //var_dump($data);

        //check response
        if (curl_getinfo($curlObj, CURLINFO_HTTP_CODE) != 200) {
            return array(false, "Failed to return 200 code when authenticate called with correct username and password");
        }

        $gotToken = false;
        $token = "";
        foreach (explode("\r\n", $data) as $line) { //No body, only headers
            if (strpos($line, ": ") === FALSE) {
                continue;
            }
            $headerBits = explode(": ", $line);
            if ($headerBits[0] != "Token") {
                continue;
            }
            if (strlen($headerBits[1]) != 60) {
                return array(false, "Wrong length token provided - should be 60 characters");
            }
            $gotToken = true;
            $token = $headerBits[1];
        }
        if (!$gotToken) {
            return array(false, "Correct username and password given but no token sent in response");
        }
        return array(true, $token);
    }

    public function testAuthenticateSuccess() {
        $authenticateRes = $this->authenticateGetToken($this->testEmployer->userId, $this->testEmployerEmail, $this->curlObj, $this->baseURL);
        $this->assertTrue($authenticateRes[0], $authenticateRes[1]);
    }


    //Most endpoints return json generated from APIResult objects. This function performs some basic checks of the
    //form and content of the json generated to see if it came from an APIResult object.
    public static function isAPIResult($data, $baseURL) {
        $dataArr = (array)json_decode($data);
        $keys = array_keys($dataArr);
        //var_dump($dataArr);
        //var_dump(strtolower($dataArr['documentation']));
        //var_dump(strtolower($baseURL).'index.php');
        return (in_array('result', $keys) && in_array('details', $keys) && in_array('documentation', $keys) 
                && in_array($dataArr['result'], array('success','failure'))
                && strtolower($dataArr['documentation']) == strtolower($baseURL).'index.php');
    }


    public static function checkNoToken($endpointURL, $curlObj, $baseProdURL) {
        curl_setopt($curlObj, CURLOPT_URL, $endpointURL);
        $data = curl_exec($curlObj);
        $returnCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        $dataArr = (array)json_decode($data);
        //var_dump($data);
        if (!APITest::isAPIResult($data, $baseProdURL)) {
            return array(false, 'Response not in APIResult form');
        }
        if ($returnCode != 401) {
            return array(false, 'Incorrect return code given for tokenless attempt should be 401 is '.$returnCode);
        }
        if ($dataArr['result'] != 'failure') {
            return array(false, 'Access to endpoint with no token supplied erroneously succeeded');
        }
        if ($dataArr['details'] != 'Token not supplied') {
            return array(false, 'Incorrect (or changed) failure message');        
        }
        return array(true, "");
    }

    public static function checkIncorrectToken($endpointURL, $curlObj, $baseProdURL) {
        curl_setopt($curlObj, CURLOPT_URL, $endpointURL);
        $sendHeaders = array("TOKEN: 123");
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $sendHeaders);
        $data = curl_exec($curlObj);
        $returnCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        $dataArr = (array)json_decode($data);
        //var_dump($data);
        if (!APITest::isAPIResult($data, $baseProdURL)) {
            return array(false, 'Response not in APIResult form');
        }
        if ($returnCode != 401) {
            return array(false, 'Incorrect return code given for wrong token attempt should be 401 is '.$returnCode);
        }
        if ($dataArr['result'] != 'failure') {
            return array(false, 'Access to endpoint with wrong token supplied erroneously succeeded');
        }
        if ($dataArr['details'] != 'You are not logged in') {
            return array(false, 'Incorrect (or changed) failure message');        
        }
        return array(true, "");
    }

    //
    public static function checkCurrentToken($endpointURL, $curlObj, $baseProdURL, $uid, $email, $baseURL, 
                                                $checkType = false, $typeExpected = "", $userTypes = array(),
                                                $checkPOSTVars = false, $POSTVarsTypeGiven = "", $POSTVars = array(), $POSTErrorMsgExpected = "") {

        $tokenAttemptRes = APITest::authenticateGetToken($uid, $email, $curlObj, $baseURL);
        if ($tokenAttemptRes[0] == false) {
            return array(false, $tokenAttemptRes[1]);
        }

        $token = $tokenAttemptRes[1];
        
        curl_setopt($curlObj, CURLOPT_URL, $endpointURL);
        $sendHeaders = array("TOKEN: ".$token);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $sendHeaders);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        //For endpoints that require POST vars be passed
        if ($checkPOSTVars) {
            $POSTVarString = "";
            /**
            foreach ($POSTVars as $key => $value) {
                if ($POSTVarString == "") {
                    $POSTVarString = $key."=".$value;
                } else {
                    $POSTVarString .= "&".$key."=".$value;
                }
            } */
            curl_setopt($curlObj, CURLOPT_POST,1);
            curl_setopt($curlObj, CURLOPT_POSTFIELDS, http_build_query($POSTVars));
        }

        $data = curl_exec($curlObj);
        $returnCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        $dataArr = (array)json_decode($data);
        //var_dump($data);
        //var_dump($token);
        if (!APITest::isAPIResult($data, $baseProdURL)) {
            return array(false, 'Response not in APIResult form');
        }

        //For endpoints that require user be logged in as a certain type
        if ($checkType) {
            $user = new \Classes\User($uid);
            $userType = $userTypes[$user->userType];
            if ($userType != $typeExpected) {
                if ($returnCode != 401) {
                    return array(false, 'Incorrect return code for wrong user type - should be 401 is '.$returnCode);
                }
                if ($dataArr['result'] != 'failure') {
                    return array(false, 'Success returned as result type for wrong user type');
                }
                if ($dataArr['details'] != 'You are not logged in as an '.$typeExpected) {
                    return array(false, 'Incorrect (or changed) failure message for wrong user type');
                }
            }
            //For endpoints that require POST vars be passed AND user type checks performed
            if ($checkPOSTVars) {
                if ($POSTVarsTypeGiven != "correct") {
                    if ($dataArr['result'] == 'success') {
                        return array(false, "Incorrect POST vars given but success still returned");
                    }
                    if ($dataArr['details'] != $POSTErrorMsgExpected) {
                        return array(false, "Unexpected error message received when incorrect POST vars given: ".PHP_EOL."'"
                                .$dataArr['details']."'".PHP_EOL."received,".PHP_EOL."'".$POSTErrorMsgExpected."'".PHP_EOL
                                ."expected. POST vars were:".PHP_EOL.print_r($POSTVars,true));
                    }
                } else {
                    if ($dataArr['result'] != 'success') {
                        return array(false, "Correct POST vars given but failure still returned");
                    }
                }
            }
            return array(true,"");
        }
        //For endpoints that don't require user type checks but do require POST var passing (probably should be refactored)
        if ($checkPOSTVars) {
            if ($POSTVarsTypeGiven != "correct") {
                if ($dataArr['result'] == 'success') {
                    return array(false, "Incorrect POST vars given but success still returned");
                }
                if ($dataArr['details'] != $POSTErrorMsgExpected) {
                    return array(false, "Unexpected error message received when incorrect POST vars given: ".PHP_EOL."'"
                            .$dataArr['details']."'".PHP_EOL."received,".PHP_EOL."'".$POSTErrorMsgExpected."'".PHP_EOL
                            ."expected. POST vars were:".PHP_EOL.print_r($POSTVars,true));
                }
            } else {
                if ($returnCode != 200) {
                    return array(false, 'Incorrect return code given for legitimate attempt should be 200 is '.$returnCode);
                }
                if ($dataArr['result'] != 'success') {
                    return array(false, "Correct POST vars given but failure still returned");
                }
            }
            return array(true, "");
        }

        //For endpoints that don't require either user type checks or POST var passing
        if ($returnCode != 200) {
            return array(false, 'Incorrect return code given for legitimate attempt should be 200 is '.$returnCode);
        }
        if ($dataArr['result'] != 'success') {
            return array(false, 'Access to endpoint with legitimate token supplied erroneously failed');
        }
        return array(true, "");
    }


    //Functions to create adminStaff table records and delete them (i.e. to be called by setup and tearDown, as
    //existing functions borrowed from SkillTest only deal with entries in the User table)

    public static function setupAdminStaffRecord($uid) {
        //In most cases would use relevant class to create record, however
        //as our frontend isn't designed to facilitate creation of admin accounts
        //we need to create these records directly
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql =  "insert into admin_staff (userid, firstname, lastname, created) "
                ."values (?, 'Bob', 'Smith', UTC_TIMESTAMP())";

        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            if ($conn->affected_rows != 1) {
                $errMsg = $conn->errno.': '.$conn->error;
                var_dump($errMsg);
                $conn->close();
                return array('false', "Could not verify creation of record in adminstaff table for userId ".$uid.":".PHP_EOL.$errMsg);
            }
            $insertedId = $stmt->insert_id;
        } 
        else {
            $errMsg = $conn->errno.': '.$conn->error;
            var_dump($errMsg);
            $conn->close();
            return array('false', "Could not create record in adminStaff table for userId ".$uid.":".PHP_EOL.$errMsg);
        }
        $conn->close();
        //var_dump("Looks to have successfully set up adminStaff record for ".$uid);
        return array(true, array('adminStaff' => new \Classes\AdminStaff($insertedId)));
    }

    public static function tearDownAdminStaffRecord($uid) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        $sql =  "delete from admin_staff where userId = ?";

        
        if($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            if ($conn->affected_rows != 1) {
                $conn->close();
                return array('false', "Could not verify deletion of record in adminstaff table for userId ".$uid);
            }
        } 
        else {
            $conn->close();
            return array('false', "Could not delete record in adminStaff table for userId ".$uid);
        }
        $conn->close();
        return array(true, '');
    }



    /**
     * categories.php
     * 
     * Simple test for valid token being supplied, and whether form of output looks correct.
     * 
     */

    public function testCategoriesNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'categories.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testCategoriesIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'categories.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testCategoriesCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'categories.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }


    /**
     * Employer endpoint
     * 
     * Test if token given, if token legit, if token for correct user type
     * 
     */

    public function testEmployerNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'employer.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testEmployerIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'employer.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testEmployerWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employer.php', $this->curlObj, $this->baseProdURL, 
                $this->testJobSeeker->userId, $this->testJobSeekerEmail, $this->baseURL, true, "employer", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testEmployerCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employer.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    /** 
     * EmployerJobs endpoint
     * 
     * As per employer endpoint
     */

    public function testEmployerJobsNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'employerJobs.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testEmployerJobsIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'employerJobs.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testEmployerJobsWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employerJobs.php', $this->curlObj, $this->baseProdURL, 
                $this->testJobSeeker->userId, $this->testJobSeekerEmail, $this->baseURL, true, "employer", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testEmployerJobsCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employerJobs.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    /**
     * EmployerMatches endpoint
     * 
     * As per employer endpoint, but needs different CorrectToken check (or existing one needs to be expanded to also check
     * for presence of legitimate jobid when indicated to do so, and whether jobid given is for employerid)
     */

    public function testEmployerMatchesNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'employerMatches.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testEmployerMatchesIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'employerMatches.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testEmployerMatchesWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employerMatches.php', $this->curlObj, $this->baseProdURL, 
                $this->testJobSeeker->userId, $this->testJobSeekerEmail, $this->baseURL, true, "employer", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    //$checkPOSTVars = false, $POSTVarsTypeGiven = "", $POSTVars = array(), $POSTErrorMsgExpected = "") {
    public function testEmployerMatchesNoJobId() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'employerMatches.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "employer", $this->userTypes,
                true, "incorrect", array(), "Must provide jobId via POST");
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testEmployerMatchesInvalidJobId() {
        $invalidJobIdRes = $this->checkCurrentToken($this->baseURL.'employerMatches.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "employer", $this->userTypes,
                true, "incorrect", array("jobId" => -1), "Provided jobId does not match a job in the system.");
        $this->assertTrue($invalidJobIdRes[0], $invalidJobIdRes[1]);
    }

    public function testEmployerMatchesOtherEmployerJobId() {
        //create job using employer2
        //assuming 1 is a valid skill categoryId
        extract(JobTest::createJob($this->testEmployer2->employerId, 1, "SomeJob"));
        //gives jid, objSave
        if ($objSave->hasError) {
            $this->assertTrue(false, "Failed to create test job");
        }

        $diffEmployerRes = $this->checkCurrentToken($this->baseURL.'employerMatches.php', $this->curlObj, $this->baseProdURL, 
            $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "employer", $this->userTypes,
            true, "incorrect", array("jobId" => $jid), "Job with provided jobId was posted by another employer. You can only view matches for jobs you have posted.");

        //cleanup
        $jobDelRes = JobTest::deleteJobTestTempRecords($this->testEmployer2Email);
        if ($jobDelRes[0] == false) {
            $this->assertTrue(false, $jobCreateRes[1]);
        }

        //check result when employer tried to look at it
        $this->assertTrue($diffEmployerRes[0], $diffEmployerRes[1]);
    }

    public function testEmployerMatchesCorrectInput() {
        //create job using employer
        //assuming 1 is a valid skill categoryId
        extract(JobTest::createJob($this->testEmployer->employerId, 1, "SomeJob"));
        //gives jid, objSave
        if ($objSave->hasError) {
            $this->assertTrue(false, "Failed to create test job");
        }

        $diffEmployerRes = $this->checkCurrentToken($this->baseURL.'employerMatches.php', $this->curlObj, $this->baseProdURL, 
            $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "employer", $this->userTypes,
            true, "correct", array("jobId" => $jid), "");

        //cleanup
        $tearDownAPIRes = $this->tearDownAPITokens();
        if ($tearDownAPIRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownAPIRes[1]);
        }
        
        $jobDelRes = JobTest::deleteJobTestTempRecords($this->testEmployerEmail);
        if ($jobDelRes[0] == false) {
            $this->assertTrue(false, $jobDelRes[1]);
        }

        //check result when employer tried to look at it
        $this->assertTrue($diffEmployerRes[0], $diffEmployerRes[1]);
    }

    /**
     * Jobseeker Endpoint
     * 
     * As per employer endpoint
     */

    public function testJobSeekerNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'jobseeker.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testJobSeekerIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'jobseeker.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testJobSeekerWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'jobseeker.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "jobseeker", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testJobSeekerCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'jobseeker.php', $this->curlObj, $this->baseProdURL, 
                $this->testJobSeeker->userId, $this->testJobSeekerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }


    /**
     * JobseekerMatches endpoint
     * 
     * As per employer endpoint
     */


    public function testJobSeekerMatchesNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'jobseekerMatches.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testJobSeekerMatchesIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'jobseekerMatches.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testJobSeekerMatchesWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'jobseekerMatches.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "jobseeker", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testJobSeekerMatchesCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'jobseekerMatches.php', $this->curlObj, $this->baseProdURL, 
                $this->testJobSeeker->userId, $this->testJobSeekerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    /**
     * JobTypes endpoint
     * 
     * Same type of endpoint as categories (in that it doesn't matter what type of user you are logged in as)
     */

    public function testJobTypesNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'jobtypes.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testJobTypesIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'jobtypes.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testJobTypesCorrectToken() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'jobtypes.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    /**
     * Skills endpoint
     * 
     * Same type of endpoint as categories and jobtypes (as in we don't care about userType),
     * but different in that we also need to do POST var-related checking
     */

    public function testSkillsNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'skills.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testSkillsIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'skills.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }

    public function testSkillsNoSkillCatId() {
        $noSkillCatIdRes = $this->checkCurrentToken($this->baseURL.'skills.php', $this->curlObj, $this->baseProdURL, 
            $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, false, "", $this->userTypes,
            true, "incorrect", array(), "categoryId not provided");
        $this->assertTrue($noSkillCatIdRes[0], $noSkillCatIdRes[1]);
    }

    public function testSkillsInvalidSkillCatId() {
        $noSkillCatIdRes = $this->checkCurrentToken($this->baseURL.'skills.php', $this->curlObj, $this->baseProdURL, 
            $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, false, "", $this->userTypes,
            true, "incorrect", array("categoryId" => -1), "invalid categoryId");
        $this->assertTrue($noSkillCatIdRes[0], $noSkillCatIdRes[1]);
    }

    public function testSkillsCorrectInput() {
        //Assuming 1 is a valid skillcategoryId
        $noSkillCatIdRes = $this->checkCurrentToken($this->baseURL.'skills.php', $this->curlObj, $this->baseProdURL, 
            $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, false, "", $this->userTypes,
            true, "correct", array("categoryId" => 1), "");
        $this->assertTrue($noSkillCatIdRes[0], $noSkillCatIdRes[1]);
    }


    /**
     * Admin endpoints
     */

    /**
     * Add skill
     * Need to test token provided, token for user, token for adminUser, categoryId and skillName provided,
     * categoryId is legitimate, skillName doesn't match already existing skill, success
     */
    
    public function testAddSkillNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'admin/addskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testAddSkillIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'admin/addskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }
    
    public function testAddSkillWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'admin/addskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "admin", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testAddSkillPOSTVarsProvided() {
        $noPOSTVarsRes = $this->checkCurrentToken($this->baseURL.'admin/addskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array(), "Must provide both categoryId and skillName via POST");
        $this->assertTrue($noPOSTVarsRes[0], $noPOSTVarsRes[1]);
    }

    public function testAddSkillInvalidCategoryId() {
        $invalidCatRes = $this->checkCurrentToken($this->baseURL.'admin/addskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => -1, "skillName" => "aSkill"), "categoryId does not match a category in our system");
        $this->assertTrue($invalidCatRes[0], $invalidCatRes[1]);
    }

    public function testAddSkillExistingName() {
        $existingNameRes = $this->checkCurrentToken($this->baseURL.'admin/addskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillName" => "Skill To Conflict With"),
                "Error attempting to save skill: Skill with same name already exists in specified category");
        $this->assertTrue($existingNameRes[0], $existingNameRes[1]);
    }

    public function testAddSkillSuccess() {
        $successRes = $this->checkCurrentToken($this->baseURL.'admin/addskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "correct", array("categoryId" => $this->testSkillCatId, "skillName" => "Previously Unused Skill Name"), "");
        $this->assertTrue($successRes[0], $successRes[1]);
    }


    /**
     * Rename skill
     * Need to test token provided, token for user, token for adminUser, categoryId, skillId and new skillName provided,
     * categoryId is legitimate, skillId is legitimate, skillId is in categoryId, skillName doesn't match already existing skill, success
     */

    public function testRenameSkillNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'admin/renameskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testRenameSkillIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'admin/renameskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }
    
    public function testRenameSkillWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "admin", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testRenameSkillPOSTVarsProvided() {
        $noPOSTVarsRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array(), "Must provide categoryId, skillId and newName via POST");
        $this->assertTrue($noPOSTVarsRes[0], $noPOSTVarsRes[1]);
    }

    public function testRenameSkillInvalidCategoryId() {
        $invalidCatRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => -1, "skillId" => -1, "newName" => "aSkill"), "categoryId does not match a category in our system");
        $this->assertTrue($invalidCatRes[0], $invalidCatRes[1]);
    }

    public function testRenameSkillInvalidSkillId() {
        $invalidSkillIdRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillId" => -1, "newName" => "SomeSkill"),
                "skillId does not match a skill in our system");
        $this->assertTrue($invalidSkillIdRes[0], $invalidSkillIdRes[1]);
    }

    public function testRenameSkillNotInSpecifiedCategory() {
        $notInCategoryRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillId" => $this->skill1cat2Id, "newName" => "SomeSkill"),
                "skillId does not represent a skill in the category represented by categoryId");
        $this->assertTrue($notInCategoryRes[0], $notInCategoryRes[1]);
    }

    public function testRenameSkillExistingName() {
        $existingNameRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillId" => $this->skill2cat1Id, "newName" => "Skill To Conflict With"),
                "Skill already exists with new name in specified category");
        $this->assertTrue($existingNameRes[0], $existingNameRes[1]);
    }

    public function testRenameSkillSuccess() {
        $successRes = $this->checkCurrentToken($this->baseURL.'admin/renameskill.php', $this->curlObj, $this->baseProdURL, 
            $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
            true, "correct", array("categoryId" => $this->testSkillCatId, "skillId" => $this->skill2cat1Id, "newName" => "NewSkillName No Conflict"),
            "Skill successfully renamed");
        $this->assertTrue($successRes[0], $successRes[1]);
    }

    /**
     * Delete skill
     * Need to test token provided, token for user, token for adminUser, categoryId and skillId provided,
     * category Id is legitimate, skillId is legitimate, skillId is in categoryId, success
     */

    public function testDeleteSkillNoToken() {
        $noTokenRes = $this->checkNoToken($this->baseURL. 'admin/deleteskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($noTokenRes[0], $noTokenRes[1]);
    }

    public function testDeleteSkillIncorrectToken() {
        $wrongTokenRes = $this->checkIncorrectToken($this->baseURL. 'admin/deleteskill.php', $this->curlObj, $this->baseProdURL);
        $this->assertTrue($wrongTokenRes[0], $wrongTokenRes[1]);
    }
    
    public function testDeleteSkillWrongUserType() {
        $currentTokenRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testEmployer->userId, $this->testEmployerEmail, $this->baseURL, true, "admin", $this->userTypes);
        $this->assertTrue($currentTokenRes[0], $currentTokenRes[1]);
    }

    public function testDeleteSkillPOSTVarsProvided() {
        $noPOSTVarsRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array(), "Must provide categoryId and skillId via POST");
        $this->assertTrue($noPOSTVarsRes[0], $noPOSTVarsRes[1]);
    }

    public function testDeleteSkillInvalidCategoryId() {
        $invalidCatRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => -1, "skillId" => -1), "categoryId does not match a category in our system");
        $this->assertTrue($invalidCatRes[0], $invalidCatRes[1]);
    }

    public function testDeleteSkillInvalidSkillId() {
        $invalidSkillRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillId" => -1), "skillId does not match a skill in our system");
        $this->assertTrue($invalidSkillRes[0], $invalidSkillRes[1]);
    }

    public function testDeleteSkillNotInSpecifiedCategory() {
        $notInCategoryRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
                $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
                true, "incorrect", array("categoryId" => $this->testSkillCatId, "skillId" => $this->skill1cat2Id), "skillId does not represent a skill in the category represented by categoryId");
        $this->assertTrue($notInCategoryRes[0], $notInCategoryRes[1]);
    }

    public function testDeleteSkillSuccess() {
        $successRes = $this->checkCurrentToken($this->baseURL.'admin/deleteskill.php', $this->curlObj, $this->baseProdURL, 
            $this->testAdminStaff->userId, $this->testAdminEmail, $this->baseURL, true, "admin", $this->userTypes,
            true, "correct", array("categoryId" => $this->testSkillCatId, "skillId" => $this->skill1cat1Id), "");
        $this->assertTrue($successRes[0], $successRes[1]);
    }


    protected function setUp(): void {
        parent::setUp();
        $this->curlObj = curl_init();
        curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, 1);
        $this->baseURL = "http://localhost:8080/api/external/";
        $this->baseProdURL = "http://localhost/api/external/";
        $employerRes = EmployerTest::createUserAndEmployer($this->testEmployerEmail);
        $this->testEmployer = new \Classes\Employer($employerRes['eid']);
        $employerRes2 = EmployerTest::createUserAndEmployer($this->testEmployer2Email);
        $this->testEmployer2 = new \Classes\Employer($employerRes2['eid']);
        $jsRes = JobSeekerTest::createUserAndJobSeeker($this->testJobSeekerEmail);
        $this->testJobSeeker = new \Classes\JobSeeker($jsRes['jid']);
        $skillCatRes = SkillTest::staticSetup('SomeRandomTestSkillCategory', $this->testAdminEmail);
        $skillCat2Res = SkillTest::staticSetupSkillCat('SomeOtherTestSkillCategory');
        //var_dump($skillCatRes);
        //$adminUser, $skillCatId
        $adminUser = $skillCatRes[1]['adminUser'];
        //need to set password and verified
        //$adminUser->password = password_hash($adminUser->email, PASSWORD_BCRYPT);
        //$adminUser->verified = 1;
        //$objSave = $adminUser->Save();
        //var_dump($objSave);
        $this->testSkillCatId = $skillCatRes[1]['skillCatId'];
        $this->testSkillCat2Id = $skillCat2Res[1]['skillCatId'];
        $this->testAdminStaff = $adminUser;

        //The above does not actually create a record in the admin_staff table, which is fine for
        //testing the Skill class, but not ok for testing the admin API functions
        $this->setupAdminStaffRecord($this->testAdminStaff->userId);
        $this->skill1cat1Id = (SkillTest::createSkill("Skill To Conflict With", $this->testSkillCatId, $this->testAdminStaff))->objectId;
        $this->skill2cat1Id = (SkillTest::createSkill("Skill To Rename", $this->testSkillCatId, $this->testAdminStaff))->objectId;
        $this->skill1cat2Id = (SkillTest::createSkill("Skill In Other Category", $this->testSkillCat2Id, $this->testAdminStaff))->objectId;
        //var_dump($this->testAdminStaff);

    }

    private function tearDownAPITokens() {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        $sql = "delete from api_token where userid in (Select userid from user where email = ?)";
        foreach (array($this->testEmployerEmail, $this->testEmployer2Email, $this->testJobSeekerEmail, $this->testAdminEmail) as $email) {
            if($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                //var_dump("In tearDownAPITokens, ".$stmt->affected_rows." rows deleted");
            } 
            else {
                $errMsg = $conn->errno . ' ' . $conn->error;
                var_dump("Error tearing down records in api_token for test users: ".PHP_EOL.$errMsg);
                $conn->close();
                return array(false, $errMsg);
            }
            $stmt->close();
        }
        return array(true, "");
        $conn->close();
    }

    protected function tearDown(): void {
        curl_close($this->curlObj);

        //Call relevant teardown methods and check their results, causing an assertion failure if
        //any indicated errors
        $tearDownAPIRes = $this->tearDownAPITokens();
        if ($tearDownAPIRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownAPIRes[1]);
        }

        $tearDownEmpRes = EmployerTest::tearDownByEmail($this->testEmployerEmail);
        if ($tearDownEmpRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownEmpRes[1]);
        }
        
        $tearDownEmpRes2 = EmployerTest::tearDownByEmail($this->testEmployer2Email);
        if ($tearDownEmpRes2[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownEmpRes2[1]);
        }

        $tearDownJobSeekerRes = JobSeekerTest::tearDownByEmail($this->testJobSeekerEmail);
        if ($tearDownJobSeekerRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownJobSeekerRes[1]);
        }

        $tearsDownAdminRes = $this->tearDownAdminStaffRecord($this->testAdminStaff->userId);
        if ($tearsDownAdminRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearsDownAdminRes[1]);
        }

        $tearDownCatAndAdminRes = SkillTest::staticTearDown('SomeRandomTestSkillCategory', $this->testAdminStaff);
        if ($tearDownCatAndAdminRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownCatAndAdminRes[1]);
        }
        
        $tearDownCatRes = SkillTest::staticTearDownSkillCat('SomeOtherTestSkillCategory');
        if ($tearDownCatRes[0] == false) {
            $this->assertTrue(false, "Error in tearDown: ".$tearDownCatRes[1]);
        }
        
        parent::tearDown();
    }

}

?>