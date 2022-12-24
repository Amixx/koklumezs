<?php

namespace app\fitness\models;

use Yii;

class ReplacementExercise extends Yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_replacement_exercises';
    }

    public function rules()
    {
        return [
            [['workoutexercise_id', 'exercise_id'], 'required'],
            [['workoutexercise_id', 'exercise_id', 'reps', 'executed_reps', 'time_seconds'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['workoutexercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkoutExercise::class, 'targetAttribute' => ['workoutexercise_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workoutexercise_id' => Yii::t('app', 'Workout exercise ID'),
            'exercise_id' => Yii::t('app', 'Exercise ID'),
            'weight' => Yii::t('app', 'Weight'),
            'reps' => Yii::t('app', 'Repetitions'),
            'executed_reps' => Yii::t('app', 'Executed repetitions'),
            'time_seconds' => Yii::t('app', 'Time (seconds)'),
        ];
    }


    public function getWorkoutExercise()
    {
        return $this->hasOne(Workout::class, ['id' => 'workoutexercise_id']);
    }

    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id'])->joinWith('videos');
    }
}
