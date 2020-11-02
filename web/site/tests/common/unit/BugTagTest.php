<?php

namespace tests\common;

use common\models\BugTag;
use common\components\MyCustomActiveRecord;

class BugTagTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_BUG_ID = 500;
    const DEFAULT_NAME = "frontend";
    const DEFAULT_DELETE_STATUS = MyCustomActiveRecord::DELETE_STATUS_ENABLED;
    const DEFAULT_CREATED_AT = 1604308755; // 2020-11-02T18:00:00+00:00
    const DEFAULT_CREATED_BY = 24;
    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugTag;


    protected function _before()
    {
        $this->bugTag = new BugTag();
        $this->bugTag->bug_id = SELF::DEFAULT_BUG_ID;
        $this->bugTag->name = SELF::DEFAULT_NAME;
        $this->bugTag->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->bugTag->created_at = SELF::DEFAULT_CREATED_AT;
        $this->bugTag->created_by = SELF::DEFAULT_CREATED_BY;
    }

    protected function _after()
    {
    
    }

    public function testbugTagIsValid()
    {
        $this->assertTrue($this->bugTag->validate());
    }

    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugTag, 'bug_id', 'integer');
    }

    public function testNameIsString()
    {
        $this->fieldHasType($this->bugTag, 'name', 'string');
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugTag->save();
        $this->bugTag->refresh();

        $this->assertEqualsWithDelta($time, $this->bugTag->created_at, 1);
    }

}
