<?php

namespace app\models;

use Yii;
use app\models\Users;
use yii\base\Model;
use app\helpers\EmailSender;

class ResendVerificationEmailForm extends Model
{
    /**
     * @var string
     */
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
                'targetClass' => '\common\models\Users',
                'filter' => ['status' => Users::STATUS_INACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }
    /**
     * Sends confirmation email to user
     *
     * @return bool whether the email was sent
     */
    public function sendEmail()
    {
        $user = Users::findOne([
            'email' => $this->email,
            'status' => Users::STATUS_INACTIVE
        ]);
        
        if ($user === null) return false;

        return EmailSender::sendEmailVerification($user, $this->email);
    }
}
