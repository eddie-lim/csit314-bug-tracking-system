<?php

namespace tests\common;

use common\models\Bug;
use common\models\BugAction;

class BugActionTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_BUG_ID = 500;
    const DEFAULT_ACTION_TYPE = "assigned";
    const DEFAULT_NOTES = "test note";
    const DEFAULT_DELETE_STATUS = "enabled";
    const DEFAULT_CREATED_AT = 1604308755; // 2020-11-02T18:00:00+00:00
    const DEFAULT_CREATED_BY = 24;
    const MAX_NOTES_LEN = 1028;

    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugAction;

    protected function _before()
    {
        $this->bugAction = new BugAction();
        $this->bugAction->bug_id = SELF::DEFAULT_BUG_ID;
        $this->bugAction->action_type = SELF::DEFAULT_ACTION_TYPE;
        $this->bugAction->notes = SELF::DEFAULT_NOTES;
        $this->bugAction->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->bugAction->created_at = SELF::DEFAULT_CREATED_AT;
        $this->bugAction->created_by = SELF::DEFAULT_CREATED_BY;
    }

    protected function _after()
    {
    }

    public function testBugActionIsValid()
    {
        $this->assertTrue($this->bugAction->validate());
    }

    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugAction, 'bug_id', 'integer');
    }

    public function testBugIdReferenceValid()
    {
        $valid = [ SELF::DEFAULT_BUG_ID ];
        $invalid = [ -1, 0, 1000, 10000 ];


        $this->fieldHasValidReference($this->bugAction, 'bug_id', $valid, $invalid, 'bugAction');
    }

    public function testActionTypeIsString()
    {
        $this->fieldHasType($this->bugAction, 'action_type', 'string');
    }

    public function testActionTypeHasPermittedValue()
    {
        $valid = [ 'new', 'assigned', 'fixing', 'pending_review', 'completed', 'rejected', 'reopen' ];
        $invalid = [ 'some', 'random', 'status', 'here' ];

        $this->fieldHasValidReference($this->bugAction, 'action_type', $valid, $invalid, 'bugAction');
    }

    public function testNotesIsString()
    {
        $this->fieldHasType($this->bugAction, 'notes', 'string');
    }

    public function testNotesMax1028Chars()
    {
        $this->fieldHasMaxLength($this->bugAction, 'notes', SELF::MAX_NOTES_LEN);
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugAction->save();
        $this->bugAction->refresh();

        $this->assertEqualsWithDelta($time, $this->bugAction->created_at, 1);
    }

    public function testCreatedByReferencesExistingUser()
    {
        $valid = [ SELF::DEFAULT_CREATED_BY ];
        $invalid = [ -1, 0, 1000, 10000 ];

        $this->fieldHasValidReference($this->bugAction, 'created_by', $valid, $invalid, 'bugAction');
    }

    public function testAttributeLabelsAreCorrect()
    {
        $labels = $this->bugAction->attributeLabels();
        $this->assertEquals($labels['id'], 'ID');
        $this->assertEquals($labels['bug_id'], 'Bug ID');
        $this->assertEquals($labels['action_type'], 'Action Type');
        $this->assertEquals($labels['notes'], 'Notes');
        $this->assertEquals($labels['delete_status'], 'Delete Status');
        $this->assertEquals($labels['created_at'], 'Created At');
        $this->assertEquals($labels['created_by'], 'Created By');
    }

    public function testFnMakeModelReturnsCorrectBugAction()
    {
        $this->assertEquals(SELF::DEFAULT_BUG_ID, $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type)->bug_id);
        $this->assertNotEquals(1000, $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type)->bug_id);
        $this->assertEquals(SELF::DEFAULT_ACTION_TYPE, $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type)->action_type);
        $this->assertNotEquals('reopen', $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type)->action_type);
    }
}
