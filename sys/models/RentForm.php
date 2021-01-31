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

    public function attributeLabels() {
        return [
            'fullname' => \Yii::t('app',  'Name'),
            'email' => \Yii::t('app',  'E-mail'),
            'phone_number' => \Yii::t('app',  'Phone number'),
            'address' => \Yii::t('app',  'Address'),
        ];
    }

    public function validatePhoneNumber($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if($this->phone_number[0] !== '+' && strlen($this->phone_number) !== 8){
                $this->addError($attribute, Yii::t('app', 'Invalid phone number.'));
            }
        }
    }
}
