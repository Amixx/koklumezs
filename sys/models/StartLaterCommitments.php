<?php

namespace app\models;

use Yii;

class StartLaterCommitments extends \yii\db\ActiveRecord
{
    const TIMES_OF_DAY = [
        'morning' => [
            'start' => '8:00',
            'end' => '13:00',
        ],
        'afternoon' => [
            'start' => '13:00',
            'end' => '17:00',
        ],
        'evening' => [
            'start' => '17:00',
            'end' => '23:00',
        ],
    ];

    public static function tableName()
    {
        return 'start_later_commitments';
    }

    public function rules()
    {
        return [
            [['user_id', 'start_date'], 'required'],
            [['user_id'], 'integer'],
            [['start_date', 'start_time_of_day'], 'string'],
            [['chosen_period_started', 'commitment_fulfilled'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User ID'),
            'start_date' => Yii::t('app', 'Start date'),
            'start_time_of_day' => Yii::t('app', 'Start time of day'),
            'chosen_period_started' => Yii::t('app', 'The chosen period of time has started'),
            'commitment_fulfilled' => Yii::t('app', 'Commitment fulfilled'),
            'created_at' => Yii::t('app', 'Date of creation'),
        ];
    }


    // public function validatePassword($attribute, $params)
    // {
    //     if (!$this->hasErrors() && strlen($this->password) < 4) {
    //         $this->addError($attribute, Yii::t('app', 'Password too short.'));
    //     }
    // }

    // public function validatePasswordRepeat($attribute, $params)
    // {
    //     if (!$this->hasErrors() && $this->password != $this->passwordRepeat) {
    //         $this->addError($attribute, Yii::t('app', 'Passwords don\'t match') . '.');
    //     }
    // }

    // public function validateAgree($attribute, $params)
    // {
    //     if (!$this->hasErrors() && !$this->agree) {
    //         $this->addError($attribute, Yii::t('app', 'Please confirm.'));
    //     }
    // }

    // public function checkIfUserExists($attribute, $params)
    // {
    //     if (!$this->hasErrors() && Users::doesUserExist($this->first_name, $this->last_name, $this->email, $this->schoolId)) {
    //         $this->addError($attribute, Yii::t('app', 'A profile has already been registered using this e-mail! Have you forgotten your password?'));
    //     }
    // }

    // public function signUp()
    // {
    //     if ($this->validate()) {
    //         $user = new Users();
    //         $user->password = Yii::$app->security->generatePasswordHash($this->password);
    //         $user->first_name = $this->first_name;
    //         $user->last_name = $this->last_name;
    //         $user->email = $this->email;
    //         $user->language = $this->language;

    //         $user->status = Users::STATUS_PASSIVE;

    //         $saved = $user->save();

    //         if ($saved) {
    //             return $user;
    //         }
    //     }
    //     return false;
    // }

    // public static function fromSession()
    // {
    //     $model = new SignUpForm;
    //     if (Yii::$app->session['signupModel'] !== null) {
    //         $signupModel = Yii::$app->session['signupModel'];
    //         $model->first_name = $signupModel['first_name'];
    //         $model->last_name = $signupModel['last_name'];
    //         $model->email = $signupModel['email'];
    //     }

    //     return $model;
    // }
}
