<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RentForm extends Model
{
    public $agreeToTerms = false;

    public function rules()
    {
        return [
            [['agreeToTerms'], 'required'],
            [['agreeToTerms'], 'boolean'],
            ['agreeToTerms', 'validateAgree'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'agreeToTerms' => \Yii::t('app',  'Aggree to terms'),
        ];
    }

    public function validateAgree($attribute, $params)
    {
        if (!$this->hasErrors() && !$this->agreeToTerms) {
            $this->addError($attribute, Yii::t('app', 'Please confirm.'));
        }
    }

    public static function registerUser($signupModel)
    {
        $user = new Users;
        $user->password = Yii::$app->security->generatePasswordHash($signupModel['password']);
        $user->first_name = $signupModel['first_name'];
        $user->last_name = $signupModel['last_name'];
        $user->email = $signupModel['email'];
        $user->language = $signupModel['language'];
        $user->status = Users::STATUS_PASSIVE;
        $saved = $user->save();

        return $saved ? $user : null;
    }

    public static function registerPlanForUser($userId, $sspId)
    {
        $studentSubplan = new StudentSubPlans;
        $studentSubplan->user_id = $userId;
        $studentSubplan->plan_id = $sspId;
        $studentSubplan->is_active = false;
        $studentSubplan->start_date = date('Y-m-d H:i:s', time());
        $studentSubplan->sent_invoices_count = 0;
        $studentSubplan->times_paid = 0;
        $saved = $studentSubplan->save();

        return $saved ? $studentSubplan : null;
    }
}
