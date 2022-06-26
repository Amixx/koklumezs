<?php

namespace app\fitness\models;

use app\models\Users;

class WorkoutExercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workoutexercises';
    }

    public function rules()
    {
        return [
            [['workout_id', 'exercise_id'], 'required'],
            [['workout_id', 'exercise_id', 'reps'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['workout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Workouts::class, 'targetAttribute' => ['workout_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercises::class, 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app',  'Workout ID'),
            'exercise_id' => \Yii::t('app',  'Exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
            'reps' => \Yii::t('app', 'Repetitions'),
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Users::class, ['id' => 'student_id']);
    }
}
