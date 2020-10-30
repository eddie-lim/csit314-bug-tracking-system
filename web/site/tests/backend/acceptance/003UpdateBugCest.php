<?php

use tests\backend\AcceptanceTester as AT;

/* @var $scenario Codeception\Scenario */
class updateBugCest{
    public $I;
    protected $testId;
    protected $dev;

    public function _before(AT $I) {
    }

    public function _after() {
    }

    protected function updateTestId($testId)
    {
        $this->testId = $testId;
    }

    protected function updateDev($dev)
    {
        $this->dev = $dev;
    }

    // Alvin Bruce -> reviewer
    // Jason Howard -> developer
    // Gene Williams -> triager
     
    public function assignBugAsTraiger(AT $I)
    {
        $I->logout();
        $I->login('Gene Williams', 'password');
        $I->pressKey('//input[@name="BugSearch[bug_status]"]', 'new', \Facebook\WebDriver\WebDriverKeys::ENTER);
        $entries = $I->grabMultiple('//tbody/tr', 'data-key');
        $this->updateTestId($entries[rand(0, count($entries)-1)]);
        $I->wait(1);
        $I->accessBug($this->testId);
        $I->checkCommentFunction("huge bug, please fix");
        $I->checkDownloadFunction();

        $this->updateDev($I->updateBugPropertiesTriager());
    }

    public function acceptBugAsDeveloper(AT $I)
    {
        $I->logout();
        $I->login($this->dev, 
            'password');
        // navigate to assigned form
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId);
        $I->checkCommentFunction("ok, accepted");
        $I->checkDownloadFunction();
        $I->acceptAssignedBug();
    }

    public function pendReviewBugAsDeveloper(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId);
        $I->pendingReview();
    }

    public function reopenBugAsReviewer(AT $I)
    {
        $I->logout();
        $I->login('Alvin Bruce', 'password');
        $I->click('//a[@href="/bug/tasks"]');
        $I->accessBug($this->testId);
        $I->reopenBug();
        $this->acceptBugAsDeveloper($I);
        $this->pendReviewBugAsDeveloper($I);
    }

    public function completeBugAsReviewer(AT $I)
    {
        $I->logout();
        $I->login('Alvin Bruce', 'password');
        $I->click('//a[@href="/bug/tasks"]');
        $I->accessBug($this->testId);
        $I->completeBug();
    }
}

