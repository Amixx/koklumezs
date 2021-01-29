<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\validators\EmailValidator;

class SignUpForm extends Model
{
    public $password;
    public $first_name;
    public $last_name;
    public $phone_number;
    public $email;
    public $language;
    public $rememberMe = true;
    public $hasKokle;
    public $agree;

    public $schoolId;

    public function rules()
    {
        return [ 
            [['email', 'password', 'first_name', 'last_name', 'language','agree'], 'required'],
            [['rememberMe', 'agree'], 'boolean'],
            ['password', 'validatePassword'],
            ['phone_number', 'validatePhoneNumber'],
            ['agree', 'validateAgree'],
            ['email', 'email'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(strlen($this->password) < 4){
                $this->addError($attribute, Yii::t('app', 'Password too short.'));
            }
        }
    }

    public function validatePhoneNumber($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if($this->phone_number[0] !== '+' && strlen($this->phone_number) !== 8){
                $this->addError($attribute, Yii::t('app', 'Invalid phone number.'));
            }
        }
    }

    public function validateAgree($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if($this->agree  != true){
                $this->addError($attribute, Yii::t('app', 'Please confirm.'));
            }
        }
    }

    // public function login()
    // {
    //     if ($this->validate()) {
    //         $logged = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    //         if ($logged) {
    //             $model = Users::findOne($this->_user->id);
    //             if ($model) {
    //                 $model->last_login = date('Y-m-d H:i:s', time());
    //                 $model->update();
    //             }
    //         }
    //         return $logged;
    //     }
    //     return false;
    // }

    public function signUp()
    {
        if ($this->validate()) {
            // $logged = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            // if ($logged) {
            //     $model = Users::findOne($this->_user->id);
            //     if ($model) {
            //         $model->last_login = date('Y-m-d H:i:s', time());
            //         $model->update();
            //     }
            // }
            // return $logged;
            $user = new Users();
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->phone_number = $this->phone_number;
            $user->email = $this->email;
            $user->language = $this->language;
            $user->agree = $this->agree;

            $user->status = Users::STATUS_ACTIVE;

            $saved = $user->save();

            if($saved){
                return $user->id;
            }
        }
        return false;
    }
}
