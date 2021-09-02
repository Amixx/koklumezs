<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignUpForm extends Model
{
    public $password;
    public $passwordRepeat;
    public $first_name;
    public $last_name;
    public $email;
    public $language;
    public $rememberMe = true;
    public $ownsInstrument = false;
    public $hasExperience = false;
    public $agree = false;

    public $schoolId;

    public function rules()
    {
        return [
            [['email', 'password', 'passwordRepeat', 'first_name', 'last_name', 'agree', 'ownsInstrument'], 'required'],
            [['rememberMe', 'agree', 'ownsInstrument', 'hasExperience'], 'boolean'],
            ['password', 'validatePassword'],
            ['passwordRepeat', 'validatePasswordRepeat'],
            ['agree', 'validateAgree'],
            ['email', 'email'],
            ['email', 'checkIfUserExists'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'first_name' => Yii::t('app', 'First name'),
            'last_name' => Yii::t('app', 'Last name'),
            'email' => Yii::t('app', 'E-mail'),
            'rememberMe' => Yii::t('app', 'Remember me'),
            'ownsInstrument' => Yii::t('app', 'Do you have your own instrument'),
            'passwordRepeat' => Yii::t('app', 'Repeat password'),
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors() && strlen($this->password) < 4) {
            $this->addError($attribute, Yii::t('app', 'Password too short.'));
        }
    }

    public function validatePasswordRepeat($attribute, $params)
    {
        if (!$this->hasErrors() && $this->password != $this->passwordRepeat) {
            $this->addError($attribute, Yii::t('app', 'Passwords don\'t match') . '.');
        }
    }

    public function validateAgree($attribute, $params)
    {
        if (!$this->hasErrors() && !$this->agree) {
            $this->addError($attribute, Yii::t('app', 'Please confirm.'));
        }
    }

    public function checkIfUserExists($attribute, $params)
    {
        if (!$this->hasErrors() && Users::doesUserExist($this->first_name, $this->last_name, $this->email, $this->schoolId)) {
            $this->addError($attribute, Yii::t('app', 'A profile has already been registered using this e-mail! Have you forgotten your password?'));
        }
    }

    public function signUp()
    {
        if ($this->validate()) {
            $qna = Yii::$app->session['questionsAndAnswers'];
            $aboutUser = "";
            foreach ($qna as $qnaItem) {
                $aboutUser .= $qnaItem['answer'];
                $aboutUser .= "\n";
            }

            $user = new Users();
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->email = $this->email;
            $user->language = $this->language;
            $user->about = $aboutUser;

            $user->status = Users::STATUS_PASSIVE;

            $saved = $user->save();

            if ($saved) {
                return $user;
            }
        }
        return false;
    }

    public static function fromSession()
    {
        $model = new SignUpForm;
        if (Yii::$app->session['signupModel'] !== null) {
            $signupModel = Yii::$app->session['signupModel'];
            $model->first_name = $signupModel['first_name'];
            $model->ownsInstrument = $signupModel['ownsInstrument'];
            $model->hasExperience = $signupModel['hasExperience'];
        }

        return $model;
    }
}
