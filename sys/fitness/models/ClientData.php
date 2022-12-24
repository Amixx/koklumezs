<?php

namespace app\fitness\models;

use app\models\Users;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ClientData extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'client_data';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'bodyweight'], 'integer'],
            [['goal', 'experience', 'injuries', 'problems', 'operations', 'blood_analysis', 'emotional_state', 'notes'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'User ID'),
            'bodyweight' => Yii::t('app', 'Bodyweight (kg)'),
            'goal' => Yii::t('app', 'Goal'),
            'experience' => Yii::t('app', 'Experience'),
            'injuries' => Yii::t('app', 'Injuries'),
            'problems' => Yii::t('app', 'Problems'),
            'operations' => Yii::t('app', 'Operations'),
            'blood_analysis' => Yii::t('app', 'Blood analysis'),
            'emotional_state' => Yii::t('app', 'Emotional state'),
            'notes' => Yii::t('app', 'Notes'),
        ];
    }


    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
