<?php

namespace app\fitness\models;

use Yii;
use app\models\Users;

class WorkoutExerciseSetEvaluation extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workoutexercisesetevaluation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['workoutexerciseset_id', 'user_id', 'evaluation'], 'required'],
            [['workoutexerciseset_id', 'user_id', 'evaluation'], 'integer'],
            [['created'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['workoutexerciseset_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkoutExerciseSet::class, 'targetAttribute' => ['workoutexerciseset_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workoutexerciseset_id' => \Yii::t('app',  'Workout exercise set'),
            'user_id' => \Yii::t('app',  'Client'),
            'evaluation' => \Yii::t('app',  'Evaluation'),
            'created' => \Yii::t('app',  'Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkoutExerciseSet()
    {
        return $this->hasOne(WorkoutExerciseSet::class, ['id' => 'workoutexerciseset_id']);
    }
}
