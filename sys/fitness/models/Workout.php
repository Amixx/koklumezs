<?php

namespace app\fitness\models;

use app\models\Users;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use Yii;

class Workout extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_workouts';
    }

    public function rules()
    {
        return [
            [['author_id', 'student_id'], 'required'],
            [['author_id', 'student_id'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'opened_at'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['student_id' => 'id']],
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
            'author_id' => \Yii::t('app',  'Author ID'),
            'student_id' => \Yii::t('app',  'Student ID'),
            'description' => \Yii::t('app', 'Notes'),
            'created_at' => \Yii::t('app',  'Created at'),
            'opened_at' => \Yii::t('app',  'Opened at'),
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

    public function getWorkoutExercises()
    {
        return $this->hasMany(WorkoutExercise::class, ['workout_id' => 'id'])->joinWith('exercise');
    }

    public function getNextWorkoutExercise($workoutExercise)
    {
        $takeNext = false;
        foreach ($this->workoutExercises as $wExercise) {
            if ($takeNext) return $wExercise;
            if ($wExercise->id == $workoutExercise->id) $takeNext = true;
        }
        return null;
    }

    public static function getForCurrentUser()
    {
        $userContext = Yii::$app->user->identity;
        return self::find()
            ->where(['student_id' => $userContext->id])
            ->orderBy('id', SORT_DESC)
            ->joinWith('workoutExercises')->asArray()->all();
    }
}
