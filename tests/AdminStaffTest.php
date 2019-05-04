<?php

/** 
 * Class to test functionality of AdminStaff Class
 * 
 * Author(s): Blair
 * 
 */

use PHPUnit\Framework\TestCase;


final class AdminStaffTest extends TestCase {

    private $uidsToDelete;

    public function testConstructor() {
        $adminStaff = new \Classes\AdminStaff(0);
        $this->assertSame(0, $adminStaff->adminStaffId);
    }

    /** save method is commented out in adminstaff class, as we currently have no facility for 
     * adding new adminstaff records except via direct database query
     * So nothing to test
     
    public function testSaveUser() {
        extract($this->createUserAndAdminStaff());
        $this->assertEquals($adminStaff, new \Classes\AdminStaff($asid));
    }
*/
    /**
     * Helper function as multiple tests will want to work on a record already in the database
     *  UPDATE: commenting out as we have commented out the save method in adminstaff
     * While the functionality to manage adminstaff records via the site may be added later,
     * currently adminstaff records can be added/edited/removed by database query only

    private function createUserAndAdminStaff() {
        $oid = UserTest::saveNewUser();
        $this->uidsToDelete[] = $oid;
        $adminStaff = new \Classes\AdminStaff(0);
        $adminStaff->userId = $oid;
        $oSave = $adminStaff->save();
        $asid = $oSave->objectId;
        $adminStaff->adminStaffId = $asid;
        return array('oid' => $oid, 'asid' => $asid, 'adminStaff' => $adminStaff);
    }


    public function testEditUser() {
        extract($this->createUserAndAdminStaff());
        $adminStaff->firstName = "Bob";
        $oSave2 = $adminStaff->save();
        $asid = $oSave2->objectId;
        $this->assertSame($adminStaff->firstName, (new \Classes\AdminStaff($asid))->firstName);
    }

    */

    public function testGetAdminStaffByUserIdFailure() {
        $this->assertSame(null, \Classes\AdminStaff::GetAdminStaffByUserId(0));
    }

    public function testGetAdminStaff() { //ByUserId() {
        //Cannot use the below line as the adminstaff->save method it relies on is currently
        //commented out - instead assume that there is a staff record created with adminstaffid 1
        //extract($this->createUserAndAdminStaff());
        //$this->assertEquals(\Classes\AdminStaff::GetAdminStaffByUserId($oid), $adminStaff);
        $this->assertTrue((new \Classes\AdminStaff(1))->userId > 0);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($this->uidsToDelete as $idd) {
            foreach (array('delete from admin_staff where userid = ?', 'delete from user where UserId = ?') as $sql) {
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $idd);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                    $this->assertTrue(false, "Error in database query in tearDown function");
                }
            }
        }
        $conn->close();
        parent::tearDown();
    }


}

?>