<?php

namespace tests\common;

use common\models\BugComment;
use common\components\MyCustomActiveRecord;

class BugCommentTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_BUG_ID = 500;
    const DEFAULT_COMMENT = "hey good job man!";
    const DEFAULT_DELETE_STATUS = MyCustomActiveRecord::DELETE_STATUS_ENABLED;
    const DEFAULT_CREATED_AT = 1604308755; // 2020-11-02T18:00:00+00:00
    const DEFAULT_CREATED_BY = 24;
    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugComment;


    protected function _before()
    {
        $this->bugComment = new BugComment();
        $this->bugComment->bug_id = SELF::DEFAULT_BUG_ID;
        $this->bugComment->comment = SELF::DEFAULT_COMMENT;
        $this->bugComment->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->bugComment->created_at = SELF::DEFAULT_CREATED_AT;
        $this->bugComment->created_by = SELF::DEFAULT_CREATED_BY;
    }

    protected function _after()
    {
    
    }

    public function testBugIdIsRequired()
    {
        $this->assertTrue($this->bugComment->validate(['bug_id']));
    }

    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugComment, 'bug_id', 'integer');
    }
    
    public function testCommentIsRequired()
    {
        $this->assertTrue($this->bugComment->validate(['comment']));
    }

    public function testCommentIsString()
    {
        $this->fieldHasType($this->bugComment, 'comment', 'string');
    }

    public function testDeleteStatusHasPermittedValue()
    {
        $permit = array_keys(MyCustomActiveRecord::deleteStatuses());

        $reject = [
            'some', 'random', 'values', 'thats', 'not', 'permitted'
        ];

        $this->fieldHasPermittedValue($this->bugComment, 'delete_status', $permit, $reject);
    }

    public function testCreatedAtIsInteger()
    {
        $this->fieldHasType($this->bugComment, 'created_at', 'integer');
    }

    public function testCreatedByInteger()
    {
        $this->fieldHasType($this->bugComment, 'created_by', 'integer');
    }

    public function testBugTagIsValid()
    {
        $this->assertTrue($this->bugComment->validate());
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugComment->save();
        $this->bugComment->refresh();

        $this->assertEqualsWithDelta($time, $this->bugComment->created_at, 1);
    }
}
