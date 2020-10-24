<?php

use tests\backend\_pages\LoginPage;
use tests\backend\AcceptanceTester as AT;

/* @var $scenario Codeception\Scenario */
class ALoginCest {

	public $loginPage;

	public function _before(AT $I) {
		sleep(5); 
		$this->loginPage = new LoginPage();
    }

    public function _after(AT $I) {

    }

 	public function login(AT $I) {	
        $I->amOnPage('/sign-in/login');
        $I->wantTo('try to login with invalid account');
		$this->loginPage->login($I, 'wronguser', 'wrongpassword');
		if (method_exists($I, 'wait')) {
		    $I->wait(3); // only for selenium
		}
		$I->see('Incorrect username or password.');
    }

    public function login2(AT $I) {	
        $I->amOnPage('/sign-in/login');
        $I->wantTo('login');
		$this->loginPage->login($I, 'webmaster', 'webmaster');
		if (method_exists($I, 'wait')) {
		    $I->wait(3); // only for selenium
		}
        $I->see('Bug Tracking Sys');
    }

}
