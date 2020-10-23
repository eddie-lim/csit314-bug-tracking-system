<?php

namespace tests\backend\_pages;

use tests\backend\AcceptanceTester;
use yii\helpers\Url;

/**
 * Represents login page
 */
class LoginPage
{
    
    public function login($actor, $mobile_calling_code = '65', $mobile_number)
    {
        // $this->actor->fillField('input[name="OtpForm[region_id]"]', $region_id);
        $actor->fillField('#otpform-mobile_number',$mobile_calling_code);
        $actor->fillField('input[name="OtpForm[mobile_number]"]', $mobile_number);
        $actor->click('login-button');
    }
}
