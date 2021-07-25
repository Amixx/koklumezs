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
            'start_date' => Yii::t('app', 'The selected date to start playing'),
            'start_time_of_day' => Yii::t('app', 'Time of day to start playing'),
            'chosen_period_started' => Yii::t('app', 'The chosen period of time has started'),
            'commitment_fulfilled' => Yii::t('app', 'Commitment fulfilled'),
            'created_at' => Yii::t('app', 'Date of creation'),
        ];
    }


    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
