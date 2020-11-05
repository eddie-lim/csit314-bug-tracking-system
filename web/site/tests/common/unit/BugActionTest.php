<?php

namespace tests\common;

use Yii;
use common\models\Bug;
use common\models\User;
use common\models\BugAction;

class BugActionTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

    const MAX_NOTES_LEN = 1028;

    // constant BugAction model
    const BUGACTION_DEFAULT_BUG_ID = 1;
    const BUGACTION_DEFAULT_ACTION_TYPE = "assigned";
    const BUGACTION_DEFAULT_NOTES = "test note";
    const BUGACTION_DEFAULT_DELETE_STATUS = "enabled";
    const BUGACTION_DEFAULT_CREATED_AT = 1604462730; //2020-11-04T04:05:30+00:00 
    const BUGACTION_DEFAULT_CREATED_BY = 1;

    // constant Bug model
    const BUG_DEFAULT_TITLE = 'bug_dingdong';
    const BUG_DEFAULT_DESCRIPTION = 'description_wingwong';
    const BUG_DEFAULT_STATUS = 'new';
    const BUG_DEFAULT_PRIORITY_LEVEL = 2;
    const BUG_DEFAULT_DEVELOPER_USER_ID = 1; 
    const BUG_DEFAULT_NOTES = NULL;
    const BUG_DEFAULT_DELETE_STATUS = 'enabled';
    const BUG_DEFAULT_CREATED_AT = 1604562730;
    const BUG_DEFAULT_CREATED_BY = 1;
    const BUG_DEFAULT_UPDATED_AT = 1604562730;
    const BUG_DEFAULT_UPDATED_BY = 1; 
    
    // constant User model
    const USER_DEFAULT_USERNAME = 'test_420';
    const USER_DEFAULT_PASSWORD_HASH = '$2y$13$mF6n1Y6zZS2sxIj.O00Wme8a4BeLgQlAw89exGmrlzX0vYxurmyHS';
    const USER_DEFAULT_AUTH_KEY = 'test_auth';
    const USER_DEFAULT_ACCESS_TOKEN = '1234567890123456789012345678901234567890'; // length 40 token
    const USER_DEFAULT_OAUTH_CLIENT = NULL;
    const USER_DEFAULT_OAUTH_CLIENT_USER_ID = NULL;
    const USER_DEFAULT_EMAIL = 'blazing_fast_420@nomail.com';
    const USER_DEFAULT_ACCOUNT_STATUS = NULL;
    const USER_DEFAULT_STATUS = 2;
    const USER_DEFAULT_LOGGED_AT = 1604462730;
    const USER_DEFAULT_CREATED_AT = 1604462730;
    const USER_DEFAULT_UPDATED_AT = 1604462730;

    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugAction;
    protected $bug;
    protected $bugId;
    protected $testUser;
    protected $testUserId;

    protected function _before()
    {
        $this->testUser = new User();
        $this->testUser->username = SELF::USER_DEFAULT_USERNAME;
        $this->testUser->password_hash = SELF::USER_DEFAULT_PASSWORD_HASH;
        $this->testUser->auth_key = SELF::USER_DEFAULT_AUTH_KEY;
        $this->testUser->access_token = SELF::USER_DEFAULT_ACCESS_TOKEN;
        $this->testUser->oauth_client = SELF::USER_DEFAULT_OAUTH_CLIENT;
        $this->testUser->oauth_client_user_id = SELF::USER_DEFAULT_OAUTH_CLIENT;
        $this->testUser->email = SELF::USER_DEFAULT_EMAIL;
        $this->testUser->account_status = SELF::USER_DEFAULT_ACCOUNT_STATUS;
        $this->testUser->status = SELF::USER_DEFAULT_STATUS;
        $this->testUser->logged_at = SELF::USER_DEFAULT_LOGGED_AT;
        $this->testUser->created_at = SELF::USER_DEFAULT_CREATED_AT;
        $this->testUser->updated_at = SELF::USER_DEFAULT_UPDATED_AT;

        $this->bug = new Bug();
        $this->bug->title = SELF::BUG_DEFAULT_TITLE;
        $this->bug->description = SELF::BUG_DEFAULT_DESCRIPTION;
        $this->bug->bug_status  = SELF::BUG_DEFAULT_STATUS;
        $this->bug->priority_level = SELF::BUG_DEFAULT_PRIORITY_LEVEL;
        $this->bug->developer_user_id = SELF::BUG_DEFAULT_DEVELOPER_USER_ID;
        $this->bug->notes = SELF::BUG_DEFAULT_NOTES;
        $this->bug->delete_status = SELF::BUG_DEFAULT_DELETE_STATUS;
        $this->bug->created_at = SELF::BUG_DEFAULT_CREATED_AT;
        $this->bug->created_by = SELF::BUG_DEFAULT_CREATED_BY;
        $this->bug->updated_at = SELF::BUG_DEFAULT_UPDATED_AT;
        $this->bug->updated_by = SELF::BUG_DEFAULT_UPDATED_BY;

        $this->bugAction = new BugAction();
        $this->bugAction->bug_id = SELF::BUGACTION_DEFAULT_BUG_ID;
        $this->bugAction->action_type = SELF::BUGACTION_DEFAULT_ACTION_TYPE;
        $this->bugAction->notes = SELF::BUGACTION_DEFAULT_NOTES;
        $this->bugAction->delete_status = SELF::BUGACTION_DEFAULT_DELETE_STATUS;
        $this->bugAction->created_at = SELF::BUGACTION_DEFAULT_CREATED_AT;
        $this->bugAction->created_by = SELF::BUGACTION_DEFAULT_CREATED_BY;

        // inserting user and bug before validating bugaction
        $this->testUserId = $this->tester->haveRecord('common\models\User', array(
            'username' => $this->testUser->username,
            'password_hash' => $this->testUser->password_hash,
            'auth_key' => $this->testUser->auth_key,
            'access_token' => $this->testUser->access_token,
            'oauth_client' => $this->testUser->oauth_client,
            'oauth_client_user_id' => $this->testUser->oauth_client_user_id,
            'email' => $this->testUser->email,
            'account_status' => $this->testUser->account_status,
            'status' => $this->testUser->status,
            'logged_at' => $this->testUser->logged_at,
            'created_at' => $this->testUser->created_at,
            'updated_at' => $this->testUser->updated_at,
        ));

        // assigning rbac_auth_assignment to user
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole("developer"), 1);

        $this->bugId = $this->tester->haveRecord('common\models\Bug', array(
            'title' => $this->bug->title,
            'description' => $this->bug->description,
            'bug_status' => $this->bug->bug_status,
            'priority_level' => $this->bug->priority_level,
            'developer_user_id' => $this->bug->developer_user_id,
            'notes' => $this->bug->notes,
            'delete_status' => $this->bug->delete_status,
            'created_at' => $this->bug->created_at,
            'created_by' => $this->bug->created_by,
            'updated_at' => $this->bug->updated_at,
            'updated_by' => $this->bug->updated_by,
        ));

        // assigning bug action with inserted record
        $this->bugAction->bug_id = $this->bugId;
        $this->bugAction->created_by = $this->testUserId;

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
        $valid = [ $this->bugAction->bug_id ];
        $invalid = [ -1, 0, 10000, 100000 ];

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

    public function testCreatedAtIsInteger()
    {
        $this->fieldHasType($this->bugAction, 'created_at', 'integer');
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugAction->save();
        $this->bugAction->refresh();

        $this->assertEqualsWithDelta($time, $this->bugAction->created_at, 1);
    }

    public function testCreatedByIsInteger()
    {
        $this->fieldHasType($this->bugAction, 'created_by', 'integer');
    }

    public function testCreatedByReferencesExistingUser()
    {
        $valid = [ $this->testUser->id ];
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
        $model = $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type);
        $this->assertEquals($this->bugId, $model->bug_id);
        $this->assertNotEquals(-1000, $model->bug_id);
        $this->assertEquals('assigned', $model->action_type);
        $this->assertNotEquals('invalid', $this->bugAction->makeModel($this->bugAction->bug_id, $this->bugAction->action_type)->action_type);
    }
}
