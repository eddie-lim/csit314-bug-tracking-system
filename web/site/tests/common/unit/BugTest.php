<?php

namespace tests\common;

use common\models\Bug;
use common\models\User;

class BugTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_TITLE         = 'Test Bug';
    const DEFAULT_DESC          = 'vanilla bug model';
    const DEFAULT_BUG_STATUS    = 'new';
    const DEFAULT_PRIORITY      = '1';
    const DEFAULT_DEL_STATUS    = 'enabled';
    const DEFAULT_CREATE_TIME   = 1577836800;   // 2020-01-01T00:00:00.000
    const DEFAULT_CREATOR       = 1;

    const MAX_TITLE_LEN         = 128;
    const MAX_NOTES_LEN         = 1028;

    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;

    protected $bug;

    protected function _before()
    {
        $this->bug = new Bug();
        $this->bug->title           = SELF::DEFAULT_TITLE;
        $this->bug->description     = SELF::DEFAULT_DESC;
        $this->bug->bug_status      = SELF::DEFAULT_BUG_STATUS;
        $this->bug->priority_level  = SELF::DEFAULT_PRIORITY;
        $this->bug->delete_status   = SELF::DEFAULT_DEL_STATUS;
        $this->bug->created_at      = SELF::DEFAULT_CREATE_TIME;
        $this->bug->created_by      = SELF::DEFAULT_CREATOR;
    }

    protected function _after()
    {
    }

    /*********
     * TESTS *
     *********/

    public function testVanillaBugIsValid()
    {
        $this->assertTrue($this->bug->validate());
    }

    // Test bug title

    public function testTitleIsPresent()
    {
        $this->fieldIsPresent($this->bug, 'title', $addBlankStrings = true);
    }

    public function testTitleIsString()
    {
        $this->fieldHasType($this->bug, 'title', 'string');
    }

    public function testTitleMax128Chars()
    {
        $this->fieldHasMaxLength($this->bug, 'title', SELF::MAX_TITLE_LEN);
    }

    // Test bug description

    public function testDescriptionIsPresent()
    {
        $this->fieldIsPresent($this->bug, 'description', $addBlankStrings = true);
    }

    public function testDescriptionIsString()
    {
        $this->fieldHasType($this->bug, 'description', 'string');
    }

    // Test bug status

    public function testBugStatusIsPresent()
    {
        $this->fieldIsPresent($this->bug, 'bug_status');
    }

    public function testBugStatusHasPermittedValue()
    {
        $permit = [
            'new', 'assigned', 'fixing', 'pending_review',
            'completed', 'rejected', 'reopen'
        ];

        $reject = [
            'unknown_status', 'complete', 'pending', 're-open', 'fixed'
        ];

        $this->fieldHasPermittedValue($this->bug, 'bug_status', $permit, $reject);
    }

    // Test priority level

    public function testPriorityLevelIsPresent()
    {
        $this->fieldIsPresent($this->bug, 'priority_level');
    }

    public function testPriorityLevelHasPermittedValue()
    {
        $permit = [ '1', '2', '3' ];
        $reject = [ 1, 2, 3, 'low', 'medium', 'high' ];

        $this->fieldHasPermittedValue($this->bug, 'priority_level', $permit, $reject);
    }

    // Test developer user id

    public function testDevUIDIsInteger()
    {
        $this->fieldHasType($this->bug, 'developer_user_id', 'integer');
    }

    public function testDevUIDReferencesExistingDev()
    {
        $devIds = [ 10, 11, 12, 13, 14 ];
        $nonDevIds = [
            -1, 0,      // non-existent users
             1, 2, 3,   // users that are not developers
        ];

        $this->fieldHasValidReference(
            $this->bug, 'developer_user_id', $devIds, $nonDevIds
        );
    }

    // Test notes

    public function testNotesIsString()
    {
        $this->fieldHasType($this->bug, 'notes', 'string');
    }

    public function testNotesMax1028Chars()
    {
        $this->fieldHasMaxLength($this->bug, 'notes', SELF::MAX_NOTES_LEN);
    }

    // Test delete status

    public function testDeleteStatusHasPermittedValues()
    {
        $permit = [ 'enabled', 'disabled' ];
        $reject = [ 'enable', 'disable', 'yes', 'no', 'delete' ];

        $this->fieldHasPermittedValue($this->bug, 'delete_status', $permit, $reject);
    }

    public function testDeleteStatusEmptySavedAsEnabled()
    {
        unset($this->bug->delete_status);
        $this->assertTrue($this->bug->save());

        $this->bug->refresh();
        $this->assertEquals('enabled', $this->bug->delete_status);
    }

    // Test created by

    public function testCreatedByReferencesExistingUser()
    {
        $userIds = [ 1, 2, 10, 25, 50 ];
        $nonUserIds = [ -1, 0, 1000];

        $this->fieldHasValidReference($this->bug, 'created_by', $userIds, $nonUserIds);
    }

    // Test created at

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bug->save();
        $this->bug->refresh();

        $this->assertEqualsWithDelta($time, $this->bug->created_at, 1);
    }

    // Test updated by

    public function testUpdatedByReferencesExistingUser()
    {
        $userIds = [ 1, 2, 10, 25, 50 ];
        $nonUserIds = [ -1, 0, 1000];

        $this->fieldHasValidReference($this->bug, 'updated_by', $userIds, $nonUserIds);
    }

    // Test updated at

    public function testUpdatedAtInsertCurrentTimeOnUpdate()
    {
        $this->bug->save();
        sleep(1);

        $this->bug->description = "test update time";
        $time = time();
        $this->bug->save();
        $this->bug->refresh();

        $this->assertEqualsWithDelta($time, $this->bug->updated_at, 1);
        $this->assertNotEquals($this->bug->updated_at, $this->bug->created_at);
    }

    // Test attributes labels

    public function testAttributeLabelsAreCorrect()
    {
        $labels = $this->bug->attributeLabels();

        $this->assertEquals($labels['id'], 'ID');
        $this->assertEquals($labels['title'], 'Title');
        $this->assertEquals($labels['description'], 'Description');
        $this->assertEquals($labels['bug_status'], 'Bug Status');
        $this->assertEquals($labels['priority_level'], 'Priority Level');
        $this->assertEquals($labels['developer_user_id'], 'Developer');
        $this->assertEquals($labels['notes'], 'Notes');
        $this->assertEquals($labels['delete_status'], 'Delete Status');
        $this->assertEquals($labels['created_at'], 'Created At');
        $this->assertEquals($labels['created_by'], 'Created By');
        $this->assertEquals($labels['updated_at'], 'Updated At');
        $this->assertEquals($labels['updated_by'], 'Updated By');
    }

    // Test get bug status badge color

    public function testFnGetbugstatusbadgecolorReturnsCorrectClass()
    {
        $this->bug->bug_status = Bug::BUG_STATUS_FIXING;
        $this->assertEquals('badge-warning', $this->bug->getBugStatusBadgeColor());

        $this->bug->bug_status = Bug::BUG_STATUS_COMPLETED;
        $this->assertEquals('badge-success', $this->bug->getBugStatusBadgeColor());

        $this->bug->bug_status = Bug::BUG_STATUS_ASSIGNED;
        $this->assertEquals('badge-info', $this->bug->getBugStatusBadgeColor());

        $otherStatuses = [
            Bug::BUG_STATUS_NEW,
            Bug::BUG_STATUS_PENDING_REVIEW,
            Bug::BUG_STATUS_REJECTED,
            Bug::BUG_STATUS_REOPEN,
        ];

        foreach($otherStatuses as $status) {
            $this->bug->bug_status = $status;
            $this->assertEquals('badge-light', $this->bug->getBugStatusBadgeColor());
        }
    }

}
