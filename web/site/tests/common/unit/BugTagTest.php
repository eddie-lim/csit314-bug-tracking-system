<?php

namespace tests\common;

use Faker\Factory;
use common\models\Bug;
use common\models\BugTag;
use common\components\MyCustomActiveRecord;

class BugTagTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const DEFAULT_NAME = "frontend";
    const DEFAULT_DELETE_STATUS = "enabled";
    const DEFAULT_CREATOR = 1;
    const MAX_NAME_LEN = 128;

    /**
     * @var \tests\common\UnitTester
     */
    protected $bug;
    protected $bugTag;

    protected function _before()
    {
        $this->bug = Bug::makeModel('test bug', 'for reference id');
        $this->bug->save();

        $this->bugTag = new BugTag();
        $this->bugTag->bug_id = $this->bug->id;
        $this->bugTag->name = SELF::DEFAULT_NAME;
        $this->bugTag->delete_status = SELF::DEFAULT_DELETE_STATUS;
        $this->bugTag->created_at = time();
        $this->bugTag->created_by = SELF::DEFAULT_CREATOR;
    }

    protected function _after()
    {

    }

    public function testBugIdIsRequired()
    {
        $this->assertTrue($this->bugTag->validate(['bug_id']));
    }

    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugTag, 'bug_id', 'integer');
    }

    public function testNameIsRequired()
    {
        $this->assertTrue($this->bugTag->validate(['name']));
    }

    public function testNameIsString()
    {
        $this->fieldHasType($this->bugTag, 'name', 'string');
    }

    public function testNameMax128Chars()
    {
        $this->fieldHasMaxLength($this->bugTag, 'name', SELF::MAX_NAME_LEN);
    }

    public function testDeleteStatusHasPermittedValue()
    {
        $permit = array_keys(MyCustomActiveRecord::deleteStatuses());

        $reject = [
            'some', 'random', 'values', 'thats', 'not', 'permitted'
        ];

        $this->fieldHasPermittedValue($this->bugTag, 'delete_status', $permit, $reject);
    }

    public function testCreatedAtIsInteger()
    {
        $this->fieldHasType($this->bugTag, 'created_at', 'integer');
    }

    public function testCreatedByInteger()
    {
        $this->fieldHasType($this->bugTag, 'created_by', 'integer');
    }

    public function testBugTagIsValid()
    {
        $this->assertTrue($this->bugTag->validate());
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugTag->save();
        $this->bugTag->refresh();

        $this->assertEqualsWithDelta($time, $this->bugTag->created_at, 1);
    }

}
