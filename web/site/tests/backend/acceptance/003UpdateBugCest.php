<?php

use tests\backend\AcceptanceTester as AT;

/* @var $scenario Codeception\Scenario */
class updateBugCest{
    public $I;
    protected $testId;
    protected $targetTitle;
    protected $dev;

    public function _before(AT $I) {
    }

    public function _after() {
    }

    protected function updateTestId($testId)
    {
        $this->testId = $testId;
    }

    protected function updateTargetTitle($title)
    {
        $this->targetTitle = $title;
    }

    protected function updateDev($dev)
    {
        $this->dev = $dev;
    }

    public function assignBugAsTriager(AT $I)
    {
        $I->login('Gene Williams', 'password');
        $I->click('//a[@href="/bug/tasks"]');
        $entries = $I->grabMultiple('//tbody/tr', 'data-key');

        $bugId = $entries[rand(0, count($entries)-1)];
        $this->updateTestId($bugId);
        $bugTitle = $I->grabTextFrom('//a[@href="/bug/view?id=' . $bugId . '"]');
        $this->updateTargetTitle($bugTitle);

        $I->wait(1);
        $I->accessBug($this->testId, $this->targetTitle);
        $I->checkCommentFunction("huge bug, please fix");
        $I->checkDownloadFunction();

        $this->updateDev($I->updateBugPropertiesTriager());
        $I->logout();
    }

    public function acceptBugAsDeveloper(AT $I)
    {
        $I->login($this->dev, 'password');
        // navigate to assigned form
        $I->reloadPage();
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId, $this->targetTitle);
        $I->checkCommentFunction("ok, accepted");
        $I->checkDownloadFunction();
        $I->acceptAssignedBug();
    }

    public function pendReviewBugAsDeveloper(AT $I)
    {
        $I->amOnPage('bug/index');
        $I->reloadPage();
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId, $this->targetTitle);
        $I->pendingReview();
        $I->logout();
    }

    public function reopenBugAsReviewer(AT $I)
    {
        $I->login('Alvin Bruce', 'password');
        $I->reloadPage();
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId, $this->targetTitle);
        $I->reopenBug();
        $I->logout();
        $this->acceptBugAsDeveloper($I);
        $this->pendReviewBugAsDeveloper($I);
    }

    public function completeBugAsReviewer(AT $I)
    {
        $I->login('Alvin Bruce', 'password');
        $I->reloadPage();
        $I->click('//a[@href="/bug/tasks"]');
        $I->wait(1);
        $I->accessBug($this->testId, $this->targetTitle);
        $I->completeBug();
        $I->logout();
    }
}
