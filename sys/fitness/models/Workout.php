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

    public function getWorkoutExerciseSets()
    {
        return $this->hasMany(WorkoutExerciseSet::class, ['workout_id' => 'id'])
            ->joinWith('exerciseSet')
            ->joinWith('evaluations')
            ->orderBy(['fitness_workoutexercisesets.id' => SORT_ASC]);
    }

    public function getNextWorkoutExercise($workoutExercise)
    {
        $takeNext = false;
        foreach ($this->workoutExerciseSets as $wExerciseSet) {
            if ($takeNext) return $wExerciseSet;
            if ($wExerciseSet->id == $workoutExercise->id) $takeNext = true;
        }
        return null;
    }

    public static function getUnopenedForCurrentUser()
    {
        return self::getForCurrentUser(false);
    }

    public static function getOpenedForCurrentUser()
    {
        return self::getForCurrentUser(true);
    }

    private static function getForCurrentUser($opened) {
        $userContext = Yii::$app->user->identity;
        $openedAtCond = $opened ? ['IS NOT', 'opened_at', null] : ['opened_at' => null];
        $query = self::find()
            ->where(['student_id' => $userContext->id])
            ->andWhere($openedAtCond)
            ->orderBy(['id' => SORT_DESC])
            ->joinWith('workoutExerciseSets');
        return $query->asArray()->all();
    }
}
