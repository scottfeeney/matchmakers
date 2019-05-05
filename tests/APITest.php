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

    private $curlObj;
    private $baseURL;
    private $baseProdURL;
    private $testEmployerEmail = "JustTestingAnEmployer__123#@321.com";
    private $testJobSeekerEmail = "JustTestingAJobSeeker__123#@321.com";
    private $testAdminEmail = "JustTestingAnAdmin__123#@321.com";
    private $testEmployer;
    private $testJobSeeker;
    private $testAdminStaff;
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

    public static function authenticateGetToken($uid, $email, $curlObj, $baseURL) {
        //set password
        $user = new \Classes\User($uid);
        $user->password = password_hash($email, PASSWORD_BCRYPT);
        $user->verified = 1;
        $objSave = $user->Save();
        //var_dump($objSave);

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
                                                $checkType = false, $typeExpected = "", $userTypes = array()) {

        $token = APITest::authenticateGetToken($uid, $email, $curlObj, $baseURL)[1];
        curl_setopt($curlObj, CURLOPT_URL, $endpointURL);
        $sendHeaders = array("TOKEN: ".$token);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $sendHeaders);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        $data = curl_exec($curlObj);
        $returnCode = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        $dataArr = (array)json_decode($data);
        //var_dump($data);
        //var_dump($token);
        if (!APITest::isAPIResult($data, $baseProdURL)) {
            return array(false, 'Response not in APIResult form');
        }
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
                return array(true,"");
            }
        }
        if ($returnCode != 200) {
            return array(false, 'Incorrect return code given for legitimate attempt should be 200 is '.$returnCode);
        }
        if ($dataArr['result'] != 'success') {
            return array(false, 'Access to endpoint with legitimate token supplied erroneously failed');
        }
        return array(true, "");
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
     * Test if token given, if token legit, if token for employer
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


    protected function setUp(): void {
        parent::setUp();
        $this->curlObj = curl_init();
        curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, 1);
        $this->baseURL = "http://localhost:8080/api/external/";
        $this->baseProdURL = "http://localhost/api/external/";
        $employerRes = EmployerTest::createUserAndEmployer($this->testEmployerEmail);
        $this->testEmployer = new \Classes\Employer($employerRes['eid']);
        $jsRes = JobSeekerTest::createUserAndJobSeeker($this->testJobSeekerEmail);
        $this->testJobSeeker = new \Classes\JobSeeker($jsRes['jid']);
        $this->testAdminStaff = new \Classes\User(UserTest::saveNewUser($this->testAdminEmail,3));
    }

    protected function tearDown(): void {
        curl_close($this->curlObj);
        EmployerTest::tearDownByEmail($this->testEmployerEmail);
        JobSeekerTest::tearDownByEmail($this->testJobSeekerEmail);
        SkillTest::tearDownAdminByEmail($this->testAdminEmail);
        parent::tearDown();
    }

}

?>