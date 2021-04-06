<?php

namespace app\helpers;

use yii\helpers\Url;
use yii;

class GuestLayoutHelper extends LayoutHelper
{
    public function __construct($school)
    {
        $this->school = $school;
    }

    public function getSignupUrl()
    {
        if (!$this->school) {
            return null;
        }

        return Url::to(['site/sign-up', 's' => $this->school->id, 'l' => Yii::$app->language]);
    }

    public function getLoginUrl()
    {
        return $this->school
            ? Url::to(['site/login', 's' => $this->school->id, 'l' => Yii::$app->language])
            : Url::to(['site/login']);
    }
}
