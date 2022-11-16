<?php

namespace app\fitness\models;

use app\models\Users;
use Symfony\Component\Console\Exception\InvalidOptionException;
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
            [['abandoned'], 'boolean'],
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
            'author_id' => \Yii::t('app', 'Author ID'),
            'student_id' => \Yii::t('app', 'Student ID'),
            'description' => \Yii::t('app', 'Notes'),
            'abandoned' => \Yii::t('app', 'Has been abandoned'),
            'created_at' => \Yii::t('app', 'Created at'),
            'opened_at' => \Yii::t('app', 'Opened at'),
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
        return $this->hasMany(WorkoutExercise::class, ['workout_id' => 'id'])
            ->joinWith('exercise')
            ->joinWith('replacementExercise')
            ->joinWith('evaluation')
            ->orderBy(['fitness_workoutexercises.id' => SORT_ASC]);
    }

    public function getMessageForCoach()
    {
        return $this->hasOne(PostWorkoutMessage::class, ['workout_id' => 'id']);
    }

    public function getEvaluation()
    {
        return $this->hasOne(WorkoutEvaluation::class, ['workout_id' => 'id']);
    }

    public function hasEvaluation()
    {
        return !!$this->evaluation;
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

    public static function getUnfinishedForCurrentUser()
    {
        $query = self::getForCurrentUserQuery(false);
        return $query->asArray()->all();
    }

    public static function getFinishedForCurrentUser()
    {
        $query = self::getForCurrentUserQuery(true);
        return $query->asArray()->all();
    }

    public static function getForCurrentUserQuery($finished)
    {
        $userContext = Yii::$app->user->identity;
        $query = self::find()
            ->where(['student_id' => $userContext->id])
            ->orderBy(['id' => SORT_DESC])
            ->joinWith('workoutExercises')
            ->joinWith('evaluation');

        if ($finished) {
            $query->andWhere([
                'OR',
                ['IS NOT', 'fitness_workout_evaluations.id', null],
                ['abandoned' => true],
            ]);
        } else {
            $query->andWhere(['OR',
                    ['opened_at' => null],
                    [
                        'AND',
                        ['IS', 'fitness_workout_evaluations.id', null],
                        ['abandoned' => false],
                    ]
                ]
            );
        }

        return $query;
    }

    public function setAsOpened()
    {
        if (!$this->opened_at) {
            $this->opened_at = date('Y-m-d H:i:s', time());
            $this->update();
        }
    }

    public function setAsAbandoned()
    {
        if (!$this->opened_at) {
            throw new InvalidOptionException('Cannot abandon a workout that has not been started (opened)');
        }
        $this->abandoned = true;
        $this->update();
    }

    public static function getFirstUnopenedExercise($workout)
    {
        return WorkoutExercise::find()
            ->where(['workout_id' => $workout['id']])
            ->joinWith('exercise')
            ->andWhere(['fitness_exercises.is_pause' => false])
            ->joinWith('evaluation')
            ->andWhere(['fitness_workoutexerciseevaluations.id' => null])
            ->orderBy(['fitness_workoutexercises.id' => SORT_ASC])
            ->one();
    }

    public static function getLastWorkoutOfUserAndExercise($userId, $exerciseId) {
        return self::find()
            ->joinWith('workoutExercises')
            ->where(['user_id' => $userId])
            ->andWhere(['fitness_workoutexercises.exercise_id' => $exerciseId])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();
    }
}
