<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RentForm extends Model
{
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    public $color = null;

    public function rules()
    {
        return [
            [['fullname', 'email', 'phone_number', 'address'], 'required'],
            [['fullname', 'address'], 'string'],
            ['phone_number', 'validatePhoneNumber'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fullname' => \Yii::t('app',  'Name'),
            'email' => \Yii::t('app',  'E-mail'),
            'phone_number' => \Yii::t('app',  'Phone number'),
            'address' => \Yii::t('app',  'Address'),
        ];
    }

    public function validatePhoneNumber($attribute, $params)
    {
        if (!$this->hasErrors() && $this->phone_number[0] !== '+' && strlen($this->phone_number) !== 8) {
            $this->addError($attribute, Yii::t('app', 'Invalid phone number.'));
        }
    }

    public static function registerUser($signupModel, $phoneNumber)
    {
        $user = new Users;
        $user->password = Yii::$app->security->generatePasswordHash($signupModel['password']);
        $user->first_name = $signupModel['first_name'];
        $user->last_name = $signupModel['last_name'];
        $user->email = $signupModel['email'];
        $user->language = $signupModel['language'];
        $user->status = Users::STATUS_ACTIVE;
        $user->status = 11;
        $user->phone_number = $phoneNumber;
        $saved = $user->save();
        Yii::$app->session['signupModel'] = null;

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

    public static function createFromSession($signupModel)
    {
        $model = new RentForm;
        $model->fullname = $signupModel['first_name'] . " " . $signupModel['last_name'];
        $model->email = $signupModel['email'];

        return $model;
    }
}
