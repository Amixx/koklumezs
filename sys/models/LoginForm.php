<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property Users|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            $logged = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            if ($logged) {
                $model = Users::findOne($this->_user->id);
                if ($model) {
                    $schoolStudent = SchoolStudent::findOne(['user_id' => $model['id']]);
                    $startLaterCommitment = StartLaterCommitments::findOne(['user_id' => $model['id']]);

                    if ($schoolStudent && !$schoolStudent['show_real_lessons'] && !$startLaterCommitment) {
                        Yii::$app->session->set("renderPostRegistrationModal", true);
                    }

                    $model->last_login = date('Y-m-d H:i:s', time());
                    $model->update();
                }
            }
            return $logged;
        }
        return false;
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Users::findByEmail($this->email);
        }

        return $this->_user;
    }
}
