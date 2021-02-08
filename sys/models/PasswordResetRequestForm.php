<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;
use app\helpers\EmailSender;

class PasswordResetRequestForm extends Model
{
    public $email;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [
                'email', 'exist',
                'targetClass' => '\app\models\Users',
                'filter' => ['status' => Users::STATUS_ACTIVE],
                'message' => \Yii::t('app',  'There is no user with this e-mail address')
            ],
        ];
    }
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        $user = Users::findOne([
            'status' => Users::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        if (!$user) {
            return false;
        }

        if (!Users::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return EmailSender::sendPasswordReset($user, $this->email);
    }
}
