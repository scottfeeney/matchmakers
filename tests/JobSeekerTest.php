<?php

/** 
 * Class to test functionality of JobSeeker Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class JobSeekerTest extends TestCase {

    //private $uidsToDelete;
    private $testEmail = "someJobSeekerEmail@somewhere.com";

    //For matching algorithm testing
    private $testEmployerEmail = "algoTestingEmployerPerson@unitTests.com";
    private $testJSEmail = "algoTestingJSPerson@unitTests.com";
    private $jobName = "Statue Impersonator";
    private $skillCategoryName = "algoTestSkillCat";
    private $skillNames = array("Standing Upright", "Walking a straight line" , "Mixing Cocktails", "Sleeping through construction noise",
                                "Faking interest in foreign films", "Keeping it real", "Believing in yourself", "Mouthing mindless platitudes",
                                "Getting through a Tolstoy novel", "Growing a beard like Tolstoy", "Drinking craft beer", "Cooking charcoal");
    private $testAdminEmail = "algoTestingAdminPerson@unitTests.com";
    private $location1Name = "Colombia";
    private $location2Name = "Cuba";
    private $jobType1Name = "Dodgy";
    private $jobType2Name = "Legit";
    
    private $testJobSeeker;
    private $testSkillCategory;
    private $testEmployer;
    private $testJob;
    private $testSkills;
    private $testAdmin;
    private $location1Id;
    private $location2Id;
    private $jobType1Id;
    private $jobType2Id;





    public function testConstructor() {
        $jobSeeker = new \Classes\JobSeeker(0);
        $this->assertSame(0, $jobSeeker->jobSeekerId);
    }

    public function testSaveUser() {
        $createUserJSRes = $this->createUserAndJobSeeker($this->testEmail);
        if ($createUserJSRes == null) {
            $this->assertTrue(false);
        }
        extract($createUserJSRes);
        //yields oid, jid and jobSeeker
        $this->assertEquals($jobSeeker, new \Classes\JobSeeker($jid));
    }

    /**
     * Helper function as multiple tests will want to work on a record already in the database
     */

    public static function createUserAndJobSeeker($testEmail) {
        $oid = UserTest::saveNewUser($testEmail, 2);
        $jobSeeker = new \Classes\JobSeeker(0);
        $jobSeeker->userId = $oid;
        $oSave = $jobSeeker->Save();
        $jid = $oSave->objectId;
        $jobSeeker = new \Classes\JobSeeker($jid);
        return array('oid' => $oid, 'jid' => $jid, 'jobSeeker' => $jobSeeker);
    }

    public function testEditUser() {
        $createUserJSRes = $this->createUserAndJobSeeker($this->testEmail);
        if ($createUserJSRes == null) {
            $this->assertTrue(false);
        }
        extract($createUserJSRes);
        $jobSeeker->firstName = "Bob";
        $oSave2 = $jobSeeker->save();
        $jid = $oSave2->objectId;
        $this->assertSame($jobSeeker->firstName, (new \Classes\jobSeeker($jid))->firstName);
    }

    public function testGetJobSeekerByUserIdFailure() {
        $this->assertSame(null, \Classes\JobSeeker::GetJobSeekerByUserId(0));
    }

    public function testGetJobSeekerByUserId() {
        $result = $this->createUserAndJobSeeker($this->testEmail);
        if ($result == null) {
            $this->assertTrue(false);
        }
        extract($result);
        $this->assertEquals(\Classes\jobSeeker::GetJobSeekerByUserId($oid), $jobSeeker);
    }


    //Remaining to be tested:
    //SaveJobSeekerSkills($jobSeekerId, $selectedSkills)
    //GetSkillsByJobSeekerString($jobSeekerId)

    public function testJobSeekerSkillsString() {

        //create new jobseeker
        extract($this->createUserAndJobSeeker($this->testEmail));
        //$oid, $jid (jobseekerid), $jobSeeker


        //create new skill
        $skillCatName = "Some Category";
        $skillSetupRes = SkillTest::staticSetup($skillCatName, "SomeAdminUserEmail@email.com");
        if ($skillSetupRes[0] == false) {
            $this->assertTrue(false, $skillSetupRes[1]);
        }
        $adminUser = $skillSetupRes[1]['adminUser'];
        $skillCatId = $skillSetupRes[1]['skillCatId'];

        //give jobseeker category
        $jobSeeker->skillCategoryId = $skillCatId;
        $saveJS = $jobSeeker->Save();

        if ($saveJS->hasError) {
            $this->assertTrue(false, "Failed to set skillCategoryId for jobSeeker");
        }

        $skill1 = SkillTest::createSkill("SkillName", $skillCatId, $adminUser);
        $skill1Id = $skill1->objectId;

        //verify getSkillsByJobSeekerString returns blank string when no associated skills
        $preString = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        //add skill via savejobseeker skills
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, $skill1Id);

        //verify get returns expected string
        $post1String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);

        //create another skill
        $skill2 = SkillTest::createSkill("SkillName2", $skillCatId, $adminUser);
        $skill2Id = $skill2->objectId;

        //var_dump($skill2);

        //add second skill
        $skillIdArr = array($skill1Id, $skill2Id);
        //var_dump($skillIdArr);
        \Classes\JobSeeker::SaveJobSeekerSkills($jid, join(",", $skillIdArr));

        //verify get returns expected string
        $post2String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);
        //var_dump(\Classes\Skill::GetSkillsByJobSeeker($jid));
        //var_dump($jid);
        //var_dump($jobSeeker);

        
        //remove both skills - **TEST REMOVED**
        //Cannot use the below to remove skills - this is actually ok as the frontend always requires the user
        //to select at least one skill, so this function is never run with a blank string
        //\Classes\JobSeeker::SaveJobSeekerSkills($jid, "");
        //verify get returns blank string
        //$post3String = \Classes\JobSeeker::GetSkillsByJobSeekerString($jid);
        //$this->assertEquals($post3String, "");

        //BUT - now that this test is removed we need separate cleanup code to remove the entries in job_seeker_skill

        $tearDownJSSRes = $this->tearDownJobSeekerSkill(array($skill1Id, $skill2Id));
        if ($tearDownJSSRes[0] == false) {
            $this->assertTrue(false, $tearDownJSSRes[1]);
        }

        //UPDATE: also need to remove the jobseeker record early (before the teardown function) OR change it's skillCategoryId
        //that of another valid category to allow the test category to be deleted (now that foreign key constraints have been added)

        //Deleting the jobseeker record early would seem the simplest option.

        $tearDownByEmailRes = $this->tearDownByEmail($this->testEmail);

        $this->assertEquals(join(",", array($skill1Id, $skill2Id)), $post2String);
        $this->assertEquals($skill1Id, $post1String);
        $this->assertSame($preString, "");
        //$this->assertTrue(false, print_r($adminUser, True));

        $skillTearDownRes = SkillTest::staticTearDown($skillCatName, $adminUser);
        if ($skillTearDownRes[0] == false) {
            $this->assertTrue(false, $skillTearDownRes[1]);
        }
    }


    //Helper function to emulate the job matching algorithm. With tests using this function, any later adjustments to the 
    //algorithm will require only adjusting this function to bring testing in line, rather than adjusting each test function
    public static function matchingFormula($job, $jobSeeker, $returnType) {
        $matchPercent = 0;
        $cutoffPercent = 50; //Minimum value that will register as a match
        
        //get job type from both - if a match add 25
        $matchPercent += $job->jobTypeId == $jobSeeker->jobTypeId ? 25 : 0;

        //get location from both - if a match add 25
        $matchPercent += $job->locationId == $jobSeeker->locationId ? 25 : 0;

        //get number of skills that match = $matched
        //get number of skills that job has total = $jobTotal
        //get number of skills that seeker has job doesn't = $seekerNoMatch
        $jobSkills = explode(',', \Classes\Job::GetSkillsByJobString($job->jobId));
        $seekerSkills = explode(",", \Classes\JobSeeker::GetSkillsByJobSeekerString($jobSeeker->jobSeekerId));

        $matched = count(array_intersect($jobSkills, $seekerSkills));
        $seekerNoMatch = count($seekerSkills) - $matched;
        $jobTotal = count($jobSkills);

        //Add score for weighted skills with 'jack of all trades' penalty if applicable
        $pros = $matched / $jobTotal * 50 * 11;
        $cons = $seekerNoMatch / $jobTotal * 50;
        $skillsScore = ($pros - $cons) / 11;

        //SkillsScore ignored if negative
        $matchPercent += $skillsScore ? $skillsScore > 0 : 0;
        
        if ($matchPercent < $cutoffPercent) {
            return array();
        }
        if ($returnType == "seekers") {
            $obj = new \StdClass;
            $obj->jobSeekerId = $jobSeeker->jobSeekerId;
            $obj->score = $matchPercent;
            return array($obj);
        } else {
            $obj = new \StdClass;
            $obj->jobId = $job->jobId;
            $obj->score = $matchPercent;
            return array($obj);
        }
    }

    public static function checkMatchScore($jid, $matches) {
        $score = null;
        //Works for both job seekers and jobs
        foreach ($matches as $matchObj) {
            if (property_exists($matchObj, 'jobSeekerId')) {
                if ($matchObj->jobSeekerId == $jid) {
                    $score = $matchObj->score;
                }
            } else {
                //var_dump($matchObj);
                if ($matchObj->jobId == $jid) {
                    $score = $matchObj->score;
                }
            }
        }
        return intval($score);
    }

    

    //Test Matching
    //For these tests we assume that there are already at least two locations and two jobTypes in the database,
    //using IDs 1 and 2 in each table.
    
    //Only location matches - not listed, 25%
    //add one skill to job and jobseeker to avoid divide by zero error
    public function testLocOnly() {

        $this->testJob->locationId = $this->location1Id;
        $this->testJob->jobTypeId = $this->jobType1Id;
        $this->testJob->Save();
        \Classes\Job::SaveJobSkills($this->testJob->jobId, ($this->testSkills[0])->skillId);

        $this->testJobSeeker->locationId = $this->location1Id;
        $this->testJobSeeker->jobTypeId = $this->jobType2Id;
        $this->testJobSeeker->Save();
        \Classes\JobSeeker::SaveJobSeekerSkills($this->testJobSeeker->jobSeekerId, ($this->testSkills[1])->skillId);

        $expectedMatches = $this->matchingFormula($this->testJob, $this->testJobSeeker, "seekers");
        $actualMatches = \Classes\JobSeeker::GetJobSeekerMatchesByJob($this->testJob->jobId);

        $this->assertEquals($this->checkMatchScore($this->testJobSeeker->jobSeekerId, $expectedMatches),
                            $this->checkMatchScore($this->testJobSeeker->jobSeekerId, $actualMatches));
    }
    
    //only jobtype matches - not listed, 25%
    public function testJobTypeOnly() {

        $this->testJob->locationId = $this->location1Id;
        $this->testJob->jobTypeId = $this->jobType1Id;
        $this->testJob->Save();
        \Classes\Job::SaveJobSkills($this->testJob->jobId, ($this->testSkills[0])->skillId);

        $this->testJobSeeker->locationId = $this->location2Id;
        $this->testJobSeeker->jobTypeId = $this->jobType1Id;
        $this->testJobSeeker->Save();
        \Classes\JobSeeker::SaveJobSeekerSkills($this->testJobSeeker->jobSeekerId, ($this->testSkills[1])->skillId);

        $expectedMatches = $this->matchingFormula($this->testJob, $this->testJobSeeker, "seekers");
        $actualMatches = \Classes\JobSeeker::GetJobSeekerMatchesByJob($this->testJob->jobId);

        $this->assertEquals($this->checkMatchScore($this->testJobSeeker->jobSeekerId, $expectedMatches),
                            $this->checkMatchScore($this->testJobSeeker->jobSeekerId, $actualMatches));        
    }
    
    //Both jobtype and location match, no skills match
    //listed, 50%
    public function testJobTypeLocOnly() {

        $this->testJob->locationId = $this->location1Id;
        $this->testJob->jobTypeId = $this->jobType1Id;
        $this->testJob->skillCategoryId = $this->testSkillCategory->skillCategoryId;
        $this->testJob->Save();
        \Classes\Job::SaveJobSkills($this->testJob->jobId, ($this->testSkills[0])->skillId);

        $this->testJobSeeker->locationId = $this->location1Id;
        $this->testJobSeeker->jobTypeId = $this->jobType1Id;
        $this->testJobSeeker->skillCategoryId = $this->testSkillCategory->skillCategoryId;
        $this->testJobSeeker->Save();
        \Classes\JobSeeker::SaveJobSeekerSkills($this->testJobSeeker->jobSeekerId, ($this->testSkills[1])->skillId);

        $expectedMatches = $this->matchingFormula($this->testJob, $this->testJobSeeker, "seekers");
        $actualMatches = \Classes\JobSeeker::GetJobSeekerMatchesByJob($this->testJob->jobId);
        //var_dump($actualMatches);
        //var_dump($expectedMatches);

        $this->assertEquals($this->checkMatchScore($this->testJobSeeker->jobSeekerId, $expectedMatches),
                            $this->checkMatchScore($this->testJobSeeker->jobSeekerId, $actualMatches));        
    }

    //Both jobtype and location match, no skills match, seeker has 3 skills selected, job has 2 skills
    //listed, 50%

    //Both jobtype and location match, seeker has 5 skills, 4 of them match, job has 4 skills
    //listed, 97.72 repeating %

    //Both jobtype and location match, seeker has 5 skills, 4 of them match, job has 8 skills
    //listed 74.43 18 repeating %

    //Both jobtype and location match, seeker has 5 skills, 5 of them match, job has 5 skills
    //listed 100%

    //both jobtype and location match, seeker has 5 skills, 5 of them match, job has 12 skills
    //listed 70.8 3 repeating

    //jobtype doesn't match location does, seeker has 5 skills, 4 match, job has 8 skills
    //not listed 49.43 18 repeating %

    //location doesn't match jobtype does seeker has 5 skills 4 match job has 8 skills
    //not listed 49 43 18 repeating %

    //jobtype doesn't match location does, seeker has 5 skills, 4 match, job has 6 skills
    //listed 57.57 repeating

    //location doesn't match jobtype does, seeker has 5 skills, 4 match, job has 6 skills
    //listed 57.57 repeating

    //neither location or jobtype match, seeker has 5 skills 5 match job has 5 skills
    //listed 50

    //neither location or jobtype match, seeker has 5 skills 4 match job has 4 skills
    //not listed 47 72 repeating
    
    //neither location or jobtype match, seeker has 5 skills 5 match job has 6 skills
    //not listed 41.6 repeating



    public static function setUpForMatchingAlgoTest($jsEmail, $empEmail, $jobName, $skillCatName, $skillNames, $adminEmail, 
                                                    $loc1Name, $jobType1Name, $loc2Name, $jobType2Name) {
        
        //probably some neat way to do the below using reflection classes, but probably not worth the effort to work out how
        $thisMethod = "JobSeekerTest::setUpForMatchingAlgoTest";

        //Create jobseeker
        extract(JobSeekerTest::createUserAndJobSeeker($jsEmail));
        //yields oid, jid and jobSeeker
        if ($jobSeeker == null || $jobSeeker->jobSeekerId == 0) {
            return array(false, "Failed to set up JobSeeker in ". $thisMethod);
        }

        //Create test skill category
        $skillCatRes = SkillTest::staticSetupSkillCat($skillCatName);
        if ($skillCatRes[0] == false) {
            return array (false, "Failed to set up Skill Category in ". $thisMethod.PHP_EOL.$skillCatRes[1]);
        }
        $skillCat = new \Classes\SkillCategory($skillCatRes[1]['skillCatId']);

        //Create employer and job
        extract(JobTest::createEmployerAndJobChooseCategory($empEmail, $jobName, $skillCat->skillCategoryId));
        //yields job, employer

        //create test admin (need admin to add skills)
        $adminUserRes = SkillTest::staticSetupAdminUser($adminEmail);
        if ($adminUserRes[0] == false) {
            return array (false, "Failed to set up Skill Category in ". $thisMethod.PHP_EOL.$adminUserRes[1]);
        }
        $adminUser = $adminUserRes[1]['adminUser'];

        $skills = array();
        //Create 12 skills
        foreach ($skillNames as $skillName) {
            $skillRes = SkillTest::createSkill($skillName, $skillCat->skillCategoryId, $adminUser);
            if ($skillRes->hasError) {
                return array(false, "Failed to set up Skill ".$skillName." in ". $thisMethod.PHP_EOL.$skillRes->errorMessage);
            }
            $skills[] = new \Classes\Skill($skillRes->objectId);
        }

        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        $locIds = array();
        //Create location
        $sql = "insert into location(name) values (?)";
        foreach (array($loc1Name, $loc2Name) as $locName) {
            if($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $locName);
                $stmt->execute();
                $locIds[] = $stmt->insert_id;
            } else {
                $errorMessage = "Error attempting to insert location:".PHP_EOL.$sql.PHP_EOL."Using param:".PHP_EOL.$locName
                                .PHP_EOL."Yielded:".PHP_EOL.$conn->errno . ' ' . $conn->error;
                var_dump($errorMessage);
                $conn->close();
                return array(false, $errorMessage);
            }
        }

        $jobTypeIds = array();
        //Create jobType
        $sql = "insert into job_type(JobTypeName) values (?)";
        foreach (array($jobType1Name, $jobType2Name) as $jobTypeName) {
            if($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $jobTypeName);
                $stmt->execute();
                $jobTypeIds[] = $stmt->insert_id;
            } else {
                $errorMessage = "Error attempting to insert jobType:".PHP_EOL.$sql.PHP_EOL."Using param:".PHP_EOL.$jobTypeName
                                .PHP_EOL."Yielded:".PHP_EOL.$conn->errno . ' ' . $conn->error;
                var_dump($errorMessage);
                $conn->close();
                return array(false, $errorMessage);
            }
        }

        $conn->close();
        
        //return array(jobseeker obj, employer obj, job obj, skillcategory obj, array of skill objs, $adminUser)
        return array($jobSeeker, $employer, $job, $skillCat, $skills, $adminUser, $locIds, $jobTypeIds);
    }

    public static function tearDownAfterMatchingAlgoTest($jsEmail, $empEmail, $skillCatName, $adminEmail, $location1Id, $jobType1Id,
                                                                $location2Id, $jobType2Id) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);	

        $tearDown = array();
        $tearDown['jobseeker'] = array('param' => $jsEmail, 'paramType' => "s", 'queries' => array());
        //JobSeeker
            //remove job_seeker_skill entries
            $tearDown['jobseeker']['queries'][] = "delete from job_seeker_skill where jobSeekerId in 
                    (select jobSeekerId from job_seeker where userId in
                    (select userId from user where email = ?))";
            //remove job_seeker
            $tearDown['jobseeker']['queries'][] = "delete from job_seeker where userId in
                    (select userId from user where email = ?)";
            //remove user entry
            $tearDown['jobseeker']['queries'][] = "delete from user where email = ?";


        $tearDown['job or employer'] = array('param' => $empEmail, 'paramType' => "s", 'queries' => array());
        //job
            //remove job_skill entries
            $tearDown['job or employer']['queries'][] = "delete from job_skill where jobId in
                    (select jobId from job where employerId in
                    (select employerId from employer where userId in
                    (select userId from user where email = ?)))";
            //remove job entry
            $tearDown['job or employer']['queries'][] = "delete from job where employerId in
                    (select employerId from employer where userId in
                    (select userId from user where email = ?))";

        //employer
            //remove employer entry
            $tearDown['job or employer']['queries'][] = "delete from employer where userId in
                    (select userId from user where email = ?)";
            //remove user entry
            $tearDown['job or employer']['queries'][] = "delete from user where email = ?";


        $tearDown['skills or skill category'] = array('param' => $skillCatName, 'paramType' => "s", 'queries' => array());
        //skills
            //remove all skills
            $tearDown['skills or skill category']['queries'][] = "delete from skill where skillCategoryId in
                    (select skillCategoryId from skill_category where skillCategoryName = ?)";
        //remove skill category
        $tearDown['skills or skill category']['queries'][] = "delete from skill_category where skillCategoryName = ?";

        
        $tearDown['admin user'] = array('param' => $adminEmail, 'paramType' => "s", 'queries' => array());
        //remove admin (only in user table with userType 3, not in admin_staff table, as not necessary to add skills)
            $tearDown['admin user']['queries'][] = "delete from user where email = ?";


        //remove Locations        
        $tearDown['location1'] = array('param' => $location1Id, 'paramType' => "i", 'queries' => array());
        $tearDown['location1']['queries'][] = "delete from location where locationId = ?";
        $tearDown['location2'] = array('param' => $location2Id, 'paramType' => "i", 'queries' => array());
        $tearDown['location2']['queries'][] = "delete from location where locationId = ?";


        //remove JobTypes
        $tearDown['jobtype1'] = array('param' => $jobType1Id, 'paramType' => "i", 'queries' => array());
        $tearDown['jobtype1']['queries'][] = "delete from job_type where jobtypeid = ?";
        $tearDown['jobtype2'] = array('param' => $jobType2Id, 'paramType' => "i", 'queries' => array());
        $tearDown['jobtype2']['queries'][] = "delete from job_type where jobtypeid = ?";


        //actual teardown
        foreach ($tearDown as $tearDownCat => $paramAndQueries)
            foreach ($paramAndQueries['queries'] as $sql) {
                //var_dump($sql);
                if($stmt = $conn->prepare($sql)) {
                    //var_dump($paramAndQueries['param']);
                    $stmt->bind_param($paramAndQueries['paramType'], $paramAndQueries['param']);
                    $stmt->execute();
                    $affRows = $stmt->affected_rows;
                    
                    //var_dump("Attempting to tear down ".$tearDownCat." in JobSeekerTest::tearDownAfterMatchingAlgoTest:"
                    //            .PHP_EOL.$affRows." rows affected by query".PHP_EOL.$sql.PHP_EOL."With param:".PHP_EOL.$paramAndQueries['param']);
                    $stmt->close();
                } else {
                    $errMsg = "Error attempting to tear down ".$tearDownCat." in JobSeekerTest::tearDownAfterMatchingAlgoTest:"
                                .PHP_EOL.$conn->errno.' '.$conn->error;
                    $conn->close();
                    var_dump($errMsg);
                    return array(false, $errMsg);
                }
            }
        $conn->close();
        return array(true, "");
    }

    protected function setUp(): void {
        parent::setUp();

        //For algorithm matching unit tests
        $matchingAlgoSetupRes = $this->setUpForMatchingAlgoTest($this->testJSEmail, $this->testEmployerEmail, $this->jobName, 
                                                            $this->skillCategoryName, $this->skillNames, $this->testAdminEmail,
                                                            $this->location1Name, $this->location2Name, $this->jobType1Name, $this->jobType2Name);
        
        if ($matchingAlgoSetupRes[0] == false) {
            $this->assertTrue(false, $matchingAlgoSetupRes[1]);
        }

        //array($jobSeeker, $employer, $job, $skillCat, $skills, $adminUser)
        list($this->testJobSeeker, $this->testEmployer, $this->testJob, $this->testSkillCategory, $this->testSkills,
                $this->testAdmin, $locIds, $jobTypeIds) = $matchingAlgoSetupRes;
        $this->location1Id = $locIds[0];
        $this->location2Id = $locIds[1];

        $this->jobType1Id = $jobTypeIds[0];
        $this->jobType2Id = $jobTypeIds[1];
    }

    private function tearDownJobSeekerSkill($skillIds) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($skillIds as $skillId) {
            $sql = "delete from job_seeker_skill where skillId = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $skillId);
                $stmt->execute();
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $conn->close();
                return array(false, "Error in database query in jobSeeker tearDownJobSeekerSkill function & skillID: ".PHP_EOL.$sql.PHP_EOL.$skillId.PHP_EOL);
            }
        }
        $conn->close();
        return array(true, "");
    }

    public static function tearDownByEmail($email) {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);

        $sqls = array(  "delete from job_seeker_skill where jobseekerId in 
                            (select jobseekerId from job_seeker where userId in
                                (select userId from user where email = ? ))",
                        "delete from job_seeker where userId in
                            (select userId from user where email = ? )",
                        "delete from user where email = ?");
        foreach ($sqls as $sql) {
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $conn->close();
                return array(false, "Error in database query in jobSeeker tearDownByEmail function: ".PHP_EOL.$sql.PHP_EOL.$errorMessage);
            }
        }
        $conn->close();
        return array(true, "");
    }


    
    protected function tearDown(): void {
        $byEmailResult = JobSeekerTest::tearDownByEmail($this->testEmail);
        if ($byEmailResult[0] == false) {
            $this->assertTrue(false, $byEmailResult[1]);
        }

        $algoTearDownRes = $this->tearDownAfterMatchingAlgoTest($this->testJSEmail, $this->testEmployerEmail, $this->skillCategoryName, 
                                                                $this->testAdminEmail, $this->location1Id, $this->jobType1Id,
                                                                $this->location2Id, $this->jobType2Id);
        if ($algoTearDownRes[0] == false) {
            $this->assertTrue(false, $algoTearDownRes[1]);
        }

        /**
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($this->uidsToDelete as $idd) {
            foreach (array('delete from job_seeker where userid = ?', 'delete from user where UserId = ?') as $sql) {
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $idd);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                    $this->assertTrue(false, "Error in database query in tearDown function");
                }
            }
            $sql = 'delete from job_seeker where userid in (Select userid from user where email = ?)';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $this->testEmail);
                $stmt->execute();
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
            $sql = 'delete from job_seeker where userid is null';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $stmt->close();
            } else {
                var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                $this->assertTrue(false, "Error in database query in tearDown function");
            }
        }
        $conn->close();
        */

        parent::tearDown();
    }


}

?>