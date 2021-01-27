<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\validators\EmailValidator;

class RentOrBuyForm extends Model
{
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    public $payment_type;
    public $delivery_type;
    public $color = null;

    public function rules()
    {
        return [
            [['fullname', 'email', 'phone_number', 'address', 'payment_type', 'delivery_type'], 'required'],
            [['fullname', 'address', 'payment_type', 'delivery_type'], 'string'],
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
            'payment_type' => \Yii::t('app',  'Payment type'),
            'delivery_type' => \Yii::t('app',  'Delivery type'),
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
