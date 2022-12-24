<?php

namespace app\fitness\models;

use app\models\Users;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use Yii;

class WorkoutEvaluation extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workout_evaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['workout_id', 'evaluation'], 'required'],
            [['workout_id', 'evaluation'], 'integer'],
            [['created'], 'safe'],
            [['workout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Workout::class, 'targetAttribute' => ['workout_id' => 'id']],
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app',  'Workout'),
            'evaluation' => \Yii::t('app',  'Evaluation'),
            'created_at' => \Yii::t('app',  'Created at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkout()
    {
        return $this->hasOne(Workout::class, ['id' => 'workout_id']);
    }
}
