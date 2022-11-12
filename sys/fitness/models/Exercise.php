<?php

namespace app\fitness\models;

use app\models\Users;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class Exercise extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'fitness_exercises';
    }

    public function rules()
    {
        return [
            [['author_id', 'name', 'popularity_type'], 'required'],
            [['author_id'], 'integer'],
            [['is_pause', 'needs_evaluation', 'is_archived', 'is_bodyweight'], 'boolean'],
            [['name', 'description', 'video', 'technique_video', 'popularity_type'], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => \Yii::t('app', 'Author ID'),
            'name' => \Yii::t('app', 'Title'),
            'description' => \Yii::t('app', 'Apraksts'),
            'video' => \Yii::t('app', 'Video'),
            'technique_video' => \Yii::t('app', 'Technique video'),
            'is_pause' => \Yii::t('app', 'Is pause'),
            'needs_evaluation' => \Yii::t('app', 'Needs evaluation'),
            'popularity_type' => \Yii::t('app', 'Popularity type'),
            'is_archived' => \Yii::t('app', 'Is archived'),
            'is_bodyweight' => \Yii::t('app', 'Is bodyweight'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ]
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    public function getSets()
    {
        return $this->hasMany(ExerciseVideo::class, ['exercise_id' => 'id']);
    }

    public function getExerciseTags()
    {
        return $this->hasMany(ExerciseTag::class, ['exercise_id' => 'id'])->joinWith('tag');
    }

    public function getVideos()
    {
        return $this->hasMany(ExerciseVideo::class, ['exercise_id' => 'id']);
    }

    public function getInterchangeableExercises($joinWithExercises = false)
    {
        $query = InterchangeableExercise::find()->where(['or', ['exercise_id_1' => $this->id], ['exercise_id_2' => $this->id]]);
        if ($joinWithExercises) {
            $query->joinWith('exercise1');
            $query->joinWith('exercise2');
        }
        return $query->asArray()->all();
    }

    public function getInterchangeableExerciseIds()
    {
        $interchangeableExercises = $this->getInterchangeableExercises();
        $ids = [];
        foreach ($interchangeableExercises as $ie) {
            if ($ie['exercise_id_1'] != $this->id) $ids[] = $ie['exercise_id_1'];
            else if ($ie['exercise_id_2'] != $this->id) $ids[] = $ie['exercise_id_2'];
        }
        return $ids;
    }

    public function getInterchangeableOtherExercises()
    {
        $interchangeableExercises = $this->getInterchangeableExercises(true);
        return array_map(function ($interchangeableExercise) {
            return $interchangeableExercise['exercise_id_1'] == $this->id
                ? $interchangeableExercise['exercise2']
                : $interchangeableExercise['exercise1'];
        }, $interchangeableExercises);
    }

    public function getInterchangeableExercisesSelect2Options()
    {
        $otherExercises = $this->getInterchangeableOtherExercises();
        return array_map(function ($otherExercise) {
            return [
                'id' => $otherExercise['id'],
                'text' => $otherExercise['name'],
            ];
        }, $otherExercises);
    }

    public function renderEvaluation()
    {
        return !$this->is_pause && $this->needs_evaluation;
    }

    public function lastWeekEvaluationsOfUser($userId)
    {
        $evaluations = WorkoutExerciseEvaluation::find()
            ->joinWith('workoutExercise')
            ->andWhere(['user_id' => $userId])
            ->andWhere([
                'or',
                ['exercise_id' => $this->id],
                ['replaced_by_exercise_id' => $this->id],
            ])->all();

        return array_filter(
            $evaluations,
            function ($workoutExerciseEvaluation) {
                $evaluationCreationPlusWeek = new \DateTime($workoutExerciseEvaluation->created);
                $evaluationCreationPlusWeek->modify('+1 week');

                $now = new \DateTime();

                return $evaluationCreationPlusWeek > $now;
            });
    }

    public function estimatedAvgOneRepMaxOfUser($userId){
        $workoutExerciseEvaluations = $this->lastWeekEvaluationsOfUser($userId);

        $averageOneRepMaxSum = null;
        $res = null;

        // user has not done the exercise in the last week - use last workout evaluations and subtract 5% for every week
        if(empty($workoutExerciseEvaluations)) {
            $lastWorkout = Workout::getLastWorkoutOfUserAndExercise($userId, $this->id);
            $addedEvaluationsCount = 0;

            $now = new DateTime();
            $workoutCreatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $lastWorkout->created_at);
            $workoutCreatedWeeksAgo = floor($now->diff($workoutCreatedAt)->days/7);

            $penaltyForEachWeek = 0.05;

            foreach($lastWorkout->workoutExercises as $workoutExercise) {
                if($workoutExercise->exercise_id == $this->id) {
                    $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExercise->evaluation->getOneRepMaxRange());
                    if ($oneRepMaxRangeAverage) {
                        $addedEvaluationsCount++;
                        $averageOneRepMaxSum === null
                            ? $averageOneRepMaxSum = $oneRepMaxRangeAverage
                            : $averageOneRepMaxSum += $oneRepMaxRangeAverage;
                    }
                }
            }

            if($addedEvaluationsCount > 0) {
                $res = ($averageOneRepMaxSum / $addedEvaluationsCount) * (1 - ($penaltyForEachWeek * $workoutCreatedWeeksAgo));
            }
        } else {
            foreach ($workoutExerciseEvaluations as $workoutExerciseEvaluation) {
                $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExerciseEvaluation->getOneRepMaxRange());
                if ($oneRepMaxRangeAverage) {
                    $averageOneRepMaxSum === null
                        ? $averageOneRepMaxSum = $oneRepMaxRangeAverage
                        : $averageOneRepMaxSum += $oneRepMaxRangeAverage;
                }
            }

            $res = $averageOneRepMaxSum / count($workoutExerciseEvaluations);
        }

        return $res;
    }
    
    public static function getProgressionChainSelectOptions(){
        $exercises = self::find()->where(['is_archived' => false, 'is_bodyweight' => true])->asArray()->all();
        return ArrayHelper::map($exercises, 'id', 'name');
    }
}
