<?php

namespace tests\common;

use Faker\Factory;
use common\models\Bug;
use common\models\BugDocument;
use common\models\BugTag;
use common\models\User;
use common\components\MyCustomActiveRecord;

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

    protected $faker;
    protected $bug;

    protected function _before()
    {
        $this->faker = Factory::create();

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

    // Test associations

    public function testAssocDocumentsGetsCorrectBugDocuments()
    {
        $bug1 = $this->bug;
        $bug1->save();

        for ($i = 1; $i <= 3; $i++) {
            $doc = BugDocument::makeModel($bug1->id, "bug1_doc_0$i", 'base_url');
            $doc->save();
        }

        $bug2 = new Bug();
        $bug2->title = 'Control Bug';
        $bug2->description = 'Bug for test control';
        $bug2->priority_level = Bug::PRIORITY_LOW;
        $bug2->bug_status = Bug::BUG_STATUS_NEW;
        $bug2->save();

        for ($i = 1; $i <= 2; $i++) {
            $doc = BugDocument::makeModel($bug2->id, "bug2_doc_0$i", 'base_url');
            $doc->save();
        }

        $this->assertEquals(3, count($bug1->documents));
        foreach ($bug1->documents as $doc) {
            $this->assertEquals($bug1->id, $doc->bug_id);
        }

        $this->assertEquals(2, count($bug2->documents));
        foreach ($bug2->documents as $doc) {
            $this->assertEquals($bug2->id, $doc->bug_id);
        }
    }

    public function testAssocTagsGetsCorrectBugTags()
    {
        $bug1 = $this->bug;
        $bug1->save();

        for ($i = 1; $i <= 3; $i++) {
            $tag = BugTag::makeModel($bug1->id, "bug1_tag_0$i");
            $tag->save();
        }
        $tag = BugTag::makeModel($bug1->id, "bug1_tag_04");
        $tag->delete_status = MyCustomActiveRecord::DELETE_STATUS_DISABLED;
        $tag->save();

        $bug2 = new Bug();
        $bug2->title = 'Control Bug';
        $bug2->description = 'Bug for test control';
        $bug2->priority_level = Bug::PRIORITY_LOW;
        $bug2->bug_status = Bug::BUG_STATUS_NEW;
        $bug2->save();

        for ($i = 1; $i <= 2; $i++) {
            $tag = BugTag::makeModel($bug2->id, "bug2_tag_0$i", 'base_url');
            $tag->save();
        }
        $tag = BugTag::makeModel($bug2->id, "bug2_tag_03");
        $tag->delete_status = MyCustomActiveRecord::DELETE_STATUS_DISABLED;
        $tag->save();

        $this->assertEquals(3, count($bug1->tags));
        foreach ($bug1->tags as $tag) {
            $this->assertEquals($bug1->id, $tag->bug_id);
        }

        $this->assertEquals(2, count($bug2->tags));
        foreach ($bug2->tags as $tag) {
            $this->assertEquals($bug2->id, $tag->bug_id);
        }
    }

    public function testAssocDeveloperuserGetsCorrectUser()
    {
        $this->assertNull($this->bug->developerUser);

        $this->bug->developer_user_id = 10;
        $this->assertEquals(
            'Richard Burke', $this->bug->developerUser->getAttribute('username')
        );

        $this->bug->developer_user_id = 11;
        $this->assertEquals(
            'Charles Mata', $this->bug->developerUser->getAttribute('username')
        );
    }

    // Test instance methods

    public function testFnAttributelabelsAreCorrect()
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

    public function testFnGetprioritylevelbadgecolorReturnsCorrectClass()
    {
        $this->bug->priority_level = Bug::PRIORITY_LOW;
        $this->assertEquals('badge-info', $this->bug->getPriorityLevelBadgeColor());

        $this->bug->priority_level = Bug::PRIORITY_MED;
        $this->assertEquals('badge-warning', $this->bug->getPriorityLevelBadgeColor());

        $this->bug->priority_level = Bug::PRIORITY_HIGH;
        $this->assertEquals('badge-danger', $this->bug->getPriorityLevelBadgeColor());
    }

    public function testFnToobjectCopiesFixedFieldsCorrectly()
    {
        $bugObj = $this->bug->toObject();
        $this->assertEquals('object', gettype($bugObj));

        $this->assertEquals($this->bug->id, $bugObj->id);
        $this->assertEquals($this->bug->title, $bugObj->title);
        $this->assertEquals($this->bug->description, $bugObj->description);
        $this->assertEquals($this->bug->bug_status, $bugObj->bug_status);
        $this->assertEquals($this->bug->priority_level, $bugObj->priority_level);
        $this->assertEquals($this->bug->notes, $bugObj->notes);
        $this->assertEquals($this->bug->delete_status, $bugObj->delete_status);
    }

    public function testFnToobjectSetsDerivedFieldsCorrectly()
    {
        $this->bug->developer_user_id = 10;
        $this->bug->updated_by = $this->bug->created_by;
        $this->bug->updated_at = $this->bug->created_at;
        $bugObj = $this->bug->toObject();

        $this->assertEquals('object', gettype($bugObj));
        $this->assertEquals('Richard Burke', $bugObj->developer);

        $dt = \DateTime::createFromFormat('d M Y h:i:s A', $bugObj->created_at);
        $this->assertTrue($dt !== false && !array_sum($dt::getLastErrors()));
        $this->assertEquals('John Doe', $bugObj->created_by);

        $dt = \DateTime::createFromFormat('d M Y h:i:s A', $bugObj->updated_at);
        $this->assertTrue($dt !== false && !array_sum($dt::getLastErrors()));
        $this->assertEquals('John Doe', $bugObj->updated_by);

        $this->assertEquals('badge-light', $bugObj->bug_status_badge);;
        $this->assertEquals('badge-info', $bugObj->priority_level_badge);;
    }

    public function testFnToobjectSetsEmptyFieldsCorrectly()
    {
        unset($this->bug->created_by);
        unset($this->bug->created_at);
        $bugObj = $this->bug->toObject();

        $this->assertEquals('object', gettype($bugObj));
        $this->assertEquals('', $bugObj->developer);
        $this->assertEquals('<span class="not-set">(not set)</span>', $bugObj->created_at);
        $this->assertNull($bugObj->created_by);
        $this->assertEquals('<span class="not-set">(not set)</span>', $bugObj->updated_at);
        $this->assertNull($bugObj->updated_by);
    }

    public function testFnGetDocumentPreviews()
    {
        $this->bug->save();
        $doc1 = BugDocument::makeModel($this->bug->id, "file1.txt", 'dir');
        $doc1->save();
        $doc2 = BugDocument::makeModel($this->bug->id, "file2.jpg", 'dir');
        $doc2->save();

        $expect = [
            'data' => [
                \Yii::getAlias('@web') . DIRECTORY_SEPARATOR . $doc1->base_url .
                DIRECTORY_SEPARATOR . $doc1->path,
                \Yii::getAlias('@web') . DIRECTORY_SEPARATOR . $doc2->base_url .
                DIRECTORY_SEPARATOR . $doc2->path,
            ],
            'config' => [
                [ 'type' => 'text', 'caption' => $doc1->path, 'key' => $doc1->id ],
                [ 'type' => 'image', 'caption' => $doc2->path, 'key' => $doc2->id ],
            ]
        ];

        $this->assertEquals($expect, $this->bug->getDocumentPreviews());
    }

    // Test static functions

    public function testFnMakemodelMakesCorrectBug()
    {
        $title = 'test make model';
        $desc = 'test make model returns correct values';
        $this->bug = Bug::makeModel($title, $desc);

        $this->assertEquals($title, $this->bug->title);
        $this->assertEquals($desc, $this->bug->description);
        $this->assertEquals(Bug::BUG_STATUS_NEW, $this->bug->bug_status);
        $this->assertEquals(Bug::PRIORITY_LOW, $this->bug->priority_level);
    }

    public function testFnGetactivebugsdataGetsCorrectBugs()
    {
        $total = 100;
        $min = 10;
        $max = 15;

        $targets = [
            Bug::BUG_STATUS_ASSIGNED => $this->faker->numberBetween($min, $max),
            Bug::BUG_STATUS_FIXING => $this->faker->numberBetween($min, $max),
            Bug::BUG_STATUS_PENDING_REVIEW => $this->faker->numberBetween($min, $max),
            Bug::BUG_STATUS_COMPLETED => $this->faker->numberBetween($min, $max),
            Bug::BUG_STATUS_REJECTED => $this->faker->numberBetween($min, $max),
            Bug::BUG_STATUS_REOPEN => $this->faker->numberBetween($min, $max),
        ];

        foreach ($targets as $status => $target) {
            for ($i = 0; $i < $target; $i++) {
                $bug = new Bug();
                $bug->title = $this->faker->words($nb = 3, $unique = true);
                $bug->description = $this->faker->sentence(10);
                $bug->priority_level = Bug::PRIORITY_LOW;
                $bug->bug_status = $status;
                $bug->save();
            }
        }

        $numNew = $total - array_sum($targets);
        for ($i = 0; $i < $numNew; $i++) {
            $bug = new Bug();
            $bug->title = $this->faker->words($nb = 3, $unique = true);
            $bug->description = $this->faker->sentence(10);
            $bug->priority_level = Bug::PRIORITY_LOW;
            $bug->bug_status = Bug::BUG_STATUS_NEW;
            $bug->save();
        }

        $expect = $targets[Bug::BUG_STATUS_ASSIGNED] +
                  $targets[Bug::BUG_STATUS_FIXING] +
                  $targets[Bug::BUG_STATUS_REOPEN] + $numNew;
        $this->assertEquals($expect, count(Bug::getActiveBugsData()));
    }

    public function testFnGetresolvedbugsdataGetsCorrectBugs()
    {
        $total = 100;
        $min = 50;
        $target = $this->faker->numberBetween($min, $total);

        $options = [
          Bug::BUG_STATUS_NEW, Bug::BUG_STATUS_ASSIGNED, Bug::BUG_STATUS_FIXING,
          Bug::BUG_STATUS_PENDING_REVIEW, Bug::BUG_STATUS_REJECTED, Bug::BUG_STATUS_REOPEN,
        ];

        $completed = 0;
        for ($i = 0; $i < $total; $i++) {
            $bug = new Bug();
            $bug->title = $this->faker->words($nb = 3, $unique = true);
            $bug->description = $this->faker->sentence(10);
            $bug->priority_level = Bug::PRIORITY_LOW;

            if ($completed < $target) {
                $bug->bug_status = Bug::BUG_STATUS_COMPLETED;
                ++$completed;
            } else {
                $bug->bug_status = $options[array_rand($options)];
            }
            $bug->save();
        }

        $this->assertEquals($target, count(Bug::getResolvedBugsData()));
    }

    public function testFnGetpendingbugsdataGetsCorrectBugs()
    {
        $total = 100;
        $min = 50;
        $target = $this->faker->numberBetween($min, $total);

        $options = [
          Bug::BUG_STATUS_NEW, Bug::BUG_STATUS_ASSIGNED, Bug::BUG_STATUS_FIXING,
          Bug::BUG_STATUS_COMPLETED, Bug::BUG_STATUS_REJECTED, Bug::BUG_STATUS_REOPEN,
        ];

        $completed = 0;
        for ($i = 0; $i < $total; $i++) {
            $bug = new Bug();
            $bug->title = $this->faker->words($nb = 3, $unique = true);
            $bug->description = $this->faker->sentence(10);
            $bug->priority_level = Bug::PRIORITY_LOW;

            if ($completed < $target) {
                $bug->bug_status = Bug::BUG_STATUS_PENDING_REVIEW;
                ++$completed;
            } else {
                $bug->bug_status = $options[array_rand($options)];
            }
            $bug->save();
        }

        $this->assertEquals($target, count(Bug::getPendingBugsData()));
    }

    public function testFnGettopdeveloperataGetsCorrectDevelopers()
    {
        for ($i = 0; $i< 100; $i++) {
            $bug = new Bug();
            $bug->title = $this->faker->words($nb = 3, $unique = true);
            $bug->description = $this->faker->sentence(10);
            $bug->priority_level = Bug::PRIORITY_LOW;
            $bug->bug_status = Bug::BUG_STATUS_COMPLETED;
            $bug->developer_user_id = $this->faker->numberBetween(4, 18);
            $bug->save();
        }
        $bugs = Bug::find()->all();

        $rank = [];
        foreach ($bugs as $bug) {
            if (!isset($rank[strval($bug->developer_user_id)])) {
                $rank[strval($bug->developer_user_id)] = 1;
            } else {
                $rank[strval($bug->developer_user_id)] += 1;
            }
        }
        arsort($rank);
        $expect = array_slice($rank, 0, 3, true);

        $actual = [];
        $data = Bug::getTopDeveloperData();
        foreach ($data as $d) {
            $actual[$d['developer_user_id']] = intval($d['counter']);
        }

        foreach ($actual as $devId => $numFixed) {
            $this->assertEquals($numFixed, $actual[$devId]);
        }
    }

}
