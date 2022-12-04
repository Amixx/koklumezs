<?php

namespace app\fitness\models;

use app\models\Users;
use DateTime;
use Yii;
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
            [[
                'is_pause',
                'needs_evaluation',
                'is_archived',
                'is_bodyweight',
                'is_ready',
                'has_reps',
                'has_weight',
                'has_time',
                'has_resistance_bands',
                'has_mode',
                'has_incline_percent',
                'has_pace',
                'has_speed',
                'has_pulse',
                'has_height',
            ], 'boolean'],
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
            'is_archived' => \Yii::t('app', 'Archived'),
            'is_bodyweight' => \Yii::t('app', 'Is bodyweight'),
            'is_ready' => \Yii::t('app', 'Ready'),
            'has_reps' => \Yii::t('app', 'Reps'),
            'has_weight' => \Yii::t('app', 'Weight (kg)'),
            'has_time' => \Yii::t('app', 'Time (s)'),
            'has_resistance_bands' => \Yii::t('app', 'Resistance bands'),
            'has_mode' => \Yii::t('app', 'Mode'),
            'has_incline_percent' => \Yii::t('app', 'Incline (%)'),
            'has_pace' => \Yii::t('app', 'Pace (min/km)'),
            'has_speed' => \Yii::t('app', 'Speed (km/h)'),
            'has_pulse' => \Yii::t('app', 'Pulse'),
            'has_height' => \Yii::t('app', 'Height (cm)'),
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

    public function lastWeekEvaluationsOfUser($userId, $workoutIdToExclude = null)
    {
        $query =  WorkoutExerciseEvaluation::find()
            ->joinWith('workoutExercise')
            ->andWhere(['user_id' => $userId])
            ->andWhere([
                'or',
                ['fitness_workoutexercises.exercise_id' => $this->id],
                ['fitness_replacement_exercises.exercise_id' => $this->id],
            ]);
        if($workoutIdToExclude) {
            $query->andWhere(['!=', 'workout_id', $workoutIdToExclude]);
        }

        $evaluations = $query->all();

        return array_filter(
            $evaluations,
            function ($workoutExerciseEvaluation) {
                $evaluationCreationPlusWeek = new \DateTime($workoutExerciseEvaluation->created);
                $evaluationCreationPlusWeek->modify('+1 week');

                $now = new \DateTime();

                return $evaluationCreationPlusWeek > $now;
            });
    }

    public function estimatedAvgAbilityOfUser($userId, $workoutIdToExclude = null)
    {
        $workoutExerciseEvaluations = $this->lastWeekEvaluationsOfUser($userId, $workoutIdToExclude);

        $averageAbilitiesSum = null;
        $res = null;

        // TODO: make this also respect if workout exercise has time or not (new property on exercise?)
        $methodName = $this->is_bodyweight
            ? 'getMaxRepsRange'
            : 'getOneRepMaxRange';
        $type = $this->is_bodyweight ? 'reps' : '1rm';

        // user has not done the exercise in the last week - use last workout evaluations and subtract 5% for every week
        if (empty($workoutExerciseEvaluations)) {
            $lastWorkout = Workout::getLastWorkoutOfUserAndExercise($userId, $this->id);

            if (!$lastWorkout) return null;

            $addedEvaluationsCount = 0;

            $now = new DateTime();
            $workoutCreatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $lastWorkout->created_at);
            $workoutCreatedWeeksAgo = floor($now->diff($workoutCreatedAt)->days / 7);

            $penaltyForEachWeek = 0.05;

            foreach ($lastWorkout->workoutExercises as $workoutExercise) {
                if ($workoutExercise->exercise_id == $this->id && $workoutExercise->evaluation) {
                    $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExercise->evaluation->$methodName());
                    if ($oneRepMaxRangeAverage) {
                        $addedEvaluationsCount++;
                        $averageAbilitiesSum === null
                            ? $averageAbilitiesSum = $oneRepMaxRangeAverage
                            : $averageAbilitiesSum += $oneRepMaxRangeAverage;
                    }
                }
            }

            if ($addedEvaluationsCount > 0) {
                $res = ($averageAbilitiesSum / $addedEvaluationsCount) * (1 - ($penaltyForEachWeek * $workoutCreatedWeeksAgo));
            }
        } else {
            foreach ($workoutExerciseEvaluations as $workoutExerciseEvaluation) {
                $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExerciseEvaluation->$methodName());
                if ($oneRepMaxRangeAverage) {
                    $averageAbilitiesSum === null
                        ? $averageAbilitiesSum = $oneRepMaxRangeAverage
                        : $averageAbilitiesSum += $oneRepMaxRangeAverage;
                }
            }

            $res = $averageAbilitiesSum / count($workoutExerciseEvaluations);
        }

        return $res ? ['ability' => $res, 'type' => $type] : null;
    }

    public function estimatedAvgAbilityInWorkout($workout)
    {
        $averageAbilitiesSum = null;
        $res = null;

        // TODO: make this also respect if workout exercise has time or not (new property on exercise?)
        $methodName = $this->is_bodyweight
            ? 'getMaxRepsRange'
            : 'getOneRepMaxRange';
        $type = $this->is_bodyweight ? 'reps' : '1rm';

        $addedEvaluationsCount = 0;

        foreach ($workout->workoutExercises as $workoutExercise) {
            if ($workoutExercise->exercise_id == $this->id && $workoutExercise->evaluation) {
                $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExercise->evaluation->$methodName());
                if ($oneRepMaxRangeAverage) {
                    $addedEvaluationsCount++;
                    $averageAbilitiesSum === null
                        ? $averageAbilitiesSum = $oneRepMaxRangeAverage
                        : $averageAbilitiesSum += $oneRepMaxRangeAverage;
                }
            }
        }

        if ($addedEvaluationsCount > 0) $res = $averageAbilitiesSum / $addedEvaluationsCount;

        return $res ? ['ability' => $res, 'type' => $type] : null;
    }

    public static function getProgressionChainSelectOptions()
    {
        $exercises = self::find()
            ->where([
                'is_archived' => false,
                'is_bodyweight' => true,
                'fitness_exercises.author_id' => Yii::$app->user->identity->id
            ])
            ->asArray()->all();
        return ArrayHelper::map($exercises, 'id', 'name');
    }

    public static function getWeightExerciseSelectOptions()
    {
        $exercises = self::find()
            ->where([
                'is_archived' => false,
                'has_weight' => true,
                'fitness_exercises.author_id' => Yii::$app->user->identity->id
            ])
            ->asArray()->all();
        return ArrayHelper::map($exercises, 'id', 'name');
    }
}
