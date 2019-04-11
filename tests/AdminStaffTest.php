<?php

use PHPUnit\Framework\TestCase;


final class AdminStaffTest extends TestCase {

    private $uidsToDelete;

    public function testConstructor() {
        $adminStaff = new \Classes\AdminStaff(0);
        $this->assertSame(0, $adminStaff->adminStaffId);
    }

    public function testSaveUser() {
        extract($this->createUserAndadminStaff());
        $this->assertEquals($adminStaff, new \Classes\AdminStaff($asid));
    }

    /**
     * Helper function as multiple tests will want to work on a record already in the database
     */

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

    public function testGetAdminStaffByUserIdFailure() {
        $this->assertSame(null, \Classes\AdminStaff::GetAdminStaffByUserId(0));
    }

    public function testGetAdminStaffByUserId() {
        extract($this->createUserAndAdminStaff());
        $this->assertEquals(\Classes\AdminStaff::GetAdminStaffByUserId($oid), $adminStaff);
    }

    protected function setUp(): void {
        parent::setUp();
        $this->uidsToDelete = array();
    }

    protected function tearDown(): void {
        $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Connection failed: " . $conn->connect_error);
        foreach ($this->uidsToDelete as $idd) {
            foreach (array('delete from adminStaff where userid = ?', 'delete from user where UserId = ?') as $sql) {
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $idd);
                    $stmt->execute();
                    $result = mysqli_stmt_get_result($stmt);
                    $stmt->close();
                } else {
                    var_dump($errorMessage = $conn->errno . ' ' . $conn->error);
                }
            }
        }
        $conn->close();
        parent::tearDown();
    }


}

?>