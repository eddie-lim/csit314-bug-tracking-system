<?php

namespace tests\common;

use common\models\BugDocument;
use common\models\User;
use common\models\Bug;
use Yii;

class BugDocumentTest extends \Codeception\Test\Unit
{
    use traits\UnitTestHelper;

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

    // constant BugDocument model
    const BUGDOCUMENT_DEFAULT_BUG_ID = 1;
    const BUGDOCUMENT_DEFAULT_PATH = "doggo.jpeg";
    const BUGDOCUMENT_DEFAULT_BASE_URL = "uploads/bug_1";
    const BUGDOCUMENT_DEFAULT_DELETE_STATUS = "enabled";
    const BUGDOCUMENT_DEFAULT_CREATED_AT = 1604308755; // 2020-11-02T18:00:00+00:00
    const BUGDOCUMENT_DEFAULT_CREATED_BY = 1;

    /**
     * @var \tests\common\UnitTester
     */
    protected $tester;
    protected $bugDocument;
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

        $this->bugDocument = new BugDocument();
        $this->bugDocument->bug_id = SELF::BUGDOCUMENT_DEFAULT_BUG_ID;
        $this->bugDocument->path = SELF::BUGDOCUMENT_DEFAULT_PATH;
        $this->bugDocument->base_url = SELF::BUGDOCUMENT_DEFAULT_BASE_URL;
        $this->bugDocument->delete_status = SELF::BUGDOCUMENT_DEFAULT_DELETE_STATUS;
        $this->bugDocument->created_at = SELF::BUGDOCUMENT_DEFAULT_CREATED_AT;
        $this->bugDocument->created_by = SELF::BUGDOCUMENT_DEFAULT_CREATED_BY;

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

        // assigning bug document with inserted record
        $this->bugDocument->bug_id = $this->bugId;
        $this->bugDocument->created_by = $this->testUserId;
    }

    protected function _after()
    {
    
    }

    public function testBugDocumentIsValid()
    {
        $this->assertTrue($this->bugDocument->validate());
    }


    public function testBugIdIsInteger()
    {
        $this->fieldHasType($this->bugDocument, 'bug_id', 'integer');
    }

    public function testBugIdReferenceValid()
    {
        $valid = [ $this->bugId ];
        $invalid = [ -1, 0, 1000, 10000 ];

        $this->fieldHasValidReference($this->bugDocument, 'bug_id', $valid, $invalid, 'bugDocument');
    }

    public function testCreatedAtIsInteger()
    {
        $this->fieldHasType($this->bugDocument, 'created_at', 'integer');
    }

    public function testCreatedAtInsertCurrentTimeOnCreate()
    {
        $time = time();
        $this->bugDocument->save();
        $this->bugDocument->refresh();

        $this->assertEqualsWithDelta($time, $this->bugDocument->created_at, 1);
    }

    public function testCreatedByIsInteger()
    {
        $this->fieldHasType($this->bugDocument, 'created_by', 'integer'); 
    }

    public function testCreatedByReferencesExistingUser()
    {
        $valid = [ $this->testUserId ];
        $invalid = [ -1, 0, 1000, 10000 ];

        $this->fieldHasValidReference($this->bugDocument, 'created_by', $valid, $invalid, 'bugDocument');
    }

    public function testAttributeLabelsAreCorrect()
    {
        $labels = $this->bugDocument->attributeLabels();
        $this->assertEquals($labels['id'], 'ID');
        $this->assertEquals($labels['bug_id'], 'Bug ID');
        $this->assertEquals($labels['file_path'], 'File Path');
        $this->assertEquals($labels['delete_status'], 'Delete Status');
        $this->assertEquals($labels['created_at'], 'Created At');
        $this->assertEquals($labels['created_by'], 'Created By');
    }

    public function testFnGetFullPathReturnsCorrectPath()
    {
        $this->assertEquals($this->bugDocument->getFullPath(), SELF::BUGDOCUMENT_DEFAULT_BASE_URL .'/'.SELF::BUGDOCUMENT_DEFAULT_PATH);
        $this->assertNotEquals($this->bugDocument->getFullPath(), 'somerandomtext');
    }

    public function testFnGetTypeReturnsCorrectData()
    {
        $this->assertEquals($this->bugDocument->getType(), 'image');
        $this->assertNotEquals($this->bugDocument->getType(), 'text');
    }

    public function testFnMakeModelReturnsCorrectData()
    {
        $this->assertEquals($this->bugDocument->makeModel( $this->bugDocument->bug_id, $this->bugDocument->path, $this->bugDocument->base_url)->bug_id,
            $this->bugId
        );
        $this->assertEquals($this->bugDocument->makeModel(
            $this->bugDocument->bug_id, $this->bugDocument->path, $this->bugDocument->base_url)->path,
            SELF::BUGDOCUMENT_DEFAULT_PATH
        );
        $this->assertEquals($this->bugDocument->makeModel(
            $this->bugDocument->bug_id, $this->bugDocument->path, $this->bugDocument->base_url)->base_url,
            SELF::BUGDOCUMENT_DEFAULT_BASE_URL
        );
    }
}
