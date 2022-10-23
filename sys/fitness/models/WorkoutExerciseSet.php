<?php

namespace app\fitness\models;

use app\models\Users;

class WorkoutExerciseSet extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workoutexercisesets';
    }

    public function rules()
    {
        return [
            [['workout_id', 'exerciseset_id'], 'required'],
            [['workout_id', 'exerciseset_id'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['workout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Workout::class, 'targetAttribute' => ['workout_id' => 'id']],
            [['exerciseset_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExerciseSet::class, 'targetAttribute' => ['exerciseset_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app',  'Workout ID'),
            'exerciseset_id' => \Yii::t('app',  'Exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getWorkout()
    {
        return $this->hasOne(Workout::class, ['id' => 'workout_id']);
    }

    public function getExerciseSet()
    {
        return $this->hasOne(ExerciseSet::class, ['id' => 'exerciseset_id'])->joinWith('exercise');
    }

    public function getEvaluation()
    {
        return $this->hasOne(WorkoutExerciseSetEvaluation::class, ['workoutexerciseset_id' => 'id']);
    }
}
