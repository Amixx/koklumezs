<?php

namespace app\fitness\models;

use app\models\Users;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use Yii;

class PostWorkoutMessage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_post_workout_messages';
    }

    public function rules()
    {
        return [
            [['workout_id'], 'required'],
            [['workout_id'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app',  'Workout ID'),
            'text' => \Yii::t('app', 'Do you want to say something to the coach?'),
            'created_at' => \Yii::t('app',  'Created at'),
        ];
    }


    public function getWorkout()
    {
        return $this->hasOne(Workout::class, ['id' => 'workout_id']);
    }
}
