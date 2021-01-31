<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignUpForm extends Model
{
    public $password;
    public $first_name;
    public $last_name;
    public $email;
    public $language;
    public $rememberMe = true;
    public $ownsInstrument = null;
    public $hasExperience = false;
    public $agree = false;

    public $schoolId;

    public function rules()
    {
        return [ 
            [['email', 'password', 'first_name', 'last_name', 'agree', 'ownsInstrument'], 'required'],
            [['rememberMe', 'agree', 'ownsInstrument', 'hasExperience'], 'boolean'],
            ['password', 'validatePassword'],
            ['agree', 'validateAgree'],
            ['email', 'email'],
            ['email', 'checkIfUserExists'],
        ];
    }

    public function attributeLabels(){
        return [
            'password' => Yii::t('app', 'Password'),
            'first_name' => Yii::t('app', 'First name'),
            'last_name' => Yii::t('app', 'Last name'),
            'email' => Yii::t('app', 'E-mail'),
            'rememberMe' => Yii::t('app', 'Remember me'),
            'ownsInstrument' => Yii::t('app', 'Do you have your own instrument'),
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

    public function validateAgree($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if(!$this->agree){
                $this->addError($attribute, Yii::t('app', 'Please confirm.'));
            }
        }       
    }

    public function checkIfUserExists($attribute, $params){
        if (!$this->hasErrors()) {
            if(Users::doesUserExist($this->first_name, $this->last_name, $this->email)){
                $this->addError($attribute, Yii::t('app', 'A profile has already been registered using this e-mail! Have you forgotten your password?'));
            }
        }
    }

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
            $user->email = $this->email;
            $user->language = $this->language;

            $user->status = Users::STATUS_ACTIVE;

            $saved = $user->save();

            if($saved){
                return $user->id;
            }
        }
        return false;
    }
}
