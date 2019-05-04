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
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class APITest extends TestCase {

    private $curlObj;
    private $baseURL;
    private $testEmail = "JustTestingAUser__123#@321.com";

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
        UserTest::saveNewUser($this->testEmail);
        curl_setopt($this->curlObj, CURLOPT_URL, $this->baseURL. 'authenticate.php');
        $sendHeaders = array("EMAIL" => $this->testEmail, "PASSWORD" => ""); //null is not blank
        $data = curl_exec($this->curlObj);
        UserTest::staticTearDown(array($this->testEmail));
        $this->assertEquals(401, curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE), "Failed to return 401 code when authenticate called with legit username and invalid password");
    }

    public function testAuthenticateSuccess() {
        $user = new \Classes\User(UserTest::saveNewUser($this->testEmail));
        $user->password = password_hash($this->testEmail, PASSWORD_BCRYPT);
        $user->verified = 1;
        $user->Save();
        curl_setopt($this->curlObj, CURLOPT_HEADER, 1);
        curl_setopt($this->curlObj, CURLINFO_HEADER_OUT, 1);
        curl_setopt($this->curlObj, CURLOPT_URL, $this->baseURL. 'authenticate.php');
        $sendHeaders = array("EMAIL: ".$this->testEmail, "PASSWORD: ".$this->testEmail);

        curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, $sendHeaders);
        $data = curl_exec($this->curlObj);
        //var_dump(curl_getinfo($this->curlObj));
        UserTest::staticTearDown(array($this->testEmail));

        $this->assertEquals(curl_getinfo($this->curlObj, CURLINFO_HTTP_CODE), 200, "Failed to return 200 code when authenticate called with correct username and password");

        $gotToken = false;
        foreach (explode("\r\n", $data) as $line) { //No body, only headers
            if (strpos($line, ": ") === FALSE) {
                continue;
            }
            $headerBits = explode(": ", $line);
            if ($headerBits[0] != "Token") {
                continue;
            }
            if (strlen($headerBits[1]) != 60) {
                $this->assertTrue(false,"Wrong length token provided - should be 60 characters");
            }
        }
        $this->assertFalse($gotToken, "Correct username and password given but no token sent in response");
    }

    protected function setUp(): void {
        parent::setUp();
        $this->curlObj = curl_init();
        curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, 1);
        $this->baseURL = "http://localhost:8080/api/external/";
    }

    protected function tearDown(): void {
        curl_close($this->curlObj);
        parent::tearDown();
    }

}

?>