<?php

use tests\backend\_pages\LoginPage;
use tests\backend\_pages\OtpPage;
use tests\backend\AcceptanceTester;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure login page works');

$loginPage = LoginPage::openBy($I);
$otpPage = OtpPage::openBy($I);

sleep(5); // let's wait for the browser to fire-up
// $I->amGoingTo('submit login form with no data');
// $loginPage->login('65', '');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->expectTo('see validations errors');

//=========================================================================
// $I->amGoingTo('try to login with not existing user');
// $loginPage->login('65', '93732062');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('No such user.');
//=========================================================================
// $I->amGoingTo('try to login with invalid mobile number');
// $loginPage->login('65', 'webmaster1234');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('Mobile Number is invalid.');

//=========================================================================
$I->amGoingTo('try to login with valid mobile number');
$loginPage->login('65', '93732061');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
$I->see('One-Time Pin');

// ============================ LOGIN WITH OTP ==============================================

// $I->amGoingTo('try to login with empty otp');
// $otpPage->login('');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('token cannot be blank.');


// $I->amGoingTo('try to login with invalid otp');
// $otpPage->login('111112');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('invalid token.');


// $I->amGoingTo('try to login with string value');
// $otpPage->login('aaaaaa');
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('invalid token.');


$I->amGoingTo('try to login with valid otp');
$otpPage->login('111111');
if (method_exists($I, 'wait')) {
    $I->wait(3); // only for selenium
}
// $I->see('DASHBOARD.');


// $I->amGoingTo('try to load dashboard');
// $dashboardPage->plan();
// if (method_exists($I, 'wait')) {
//     $I->wait(3); // only for selenium
// }
// $I->see('Plan Offerings');














/** Uncomment if using WebDriver
 * $I->click('Logout (erau)');
 * $I->dontSeeLink('Logout (erau)');
 * $I->seeLink('Login');
 */
