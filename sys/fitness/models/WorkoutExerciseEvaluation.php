<?php

namespace app\fitness\models;

use Yii;
use app\models\Users;

class WorkoutExerciseEvaluation extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workoutexerciseevaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['workoutexercise_id', 'user_id', 'evaluation'], 'required'],
            [['workoutexercise_id', 'user_id', 'evaluation'], 'integer'],
            [['created'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['workoutexercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkoutExercise::class, 'targetAttribute' => ['workoutexercise_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workoutexercise_id' => \Yii::t('app',  'Workout exercise'),
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
    public function getWorkoutExercise()
    {
        return $this->hasOne(WorkoutExercise::class, ['id' => 'workoutexercise_id']);
    }
}
