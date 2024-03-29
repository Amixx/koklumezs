<?php

namespace app\fitness\models;

use app\models\Users;
use DateTime;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class Exercise extends Yii\db\ActiveRecord
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
            [[
                'name',
                'description',
                'video',
                'equipment_video',
                'equipment_video_thumbnail',
                'technique_video',
                'popularity_type',
            ], 'string'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => Yii::t('app', 'Author ID'),
            'name' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Apraksts'),
            'video' => Yii::t('app', 'Video'),
            'equipment_video' => Yii::t('app', 'Equipment video'),
            'equipment_video_thumbnail' => Yii::t('app', 'Equipment video thumbnail'),
            'technique_video' => Yii::t('app', 'Technique video'),
            'is_pause' => Yii::t('app', 'Is pause'),
            'needs_evaluation' => Yii::t('app', 'Needs evaluation'),
            'popularity_type' => Yii::t('app', 'Popularity type'),
            'is_archived' => Yii::t('app', 'Archived'),
            'is_bodyweight' => Yii::t('app', 'Is bodyweight exercise'),
            'is_ready' => Yii::t('app', 'Ready'),
            'has_reps' => Yii::t('app', 'Reps'),
            'has_weight' => Yii::t('app', 'Weight (kg)'),
            'has_time' => Yii::t('app', 'Time (s)'),
            'has_resistance_bands' => Yii::t('app', 'Resistance bands'),
            'has_mode' => Yii::t('app', 'Mode'),
            'has_incline_percent' => Yii::t('app', 'Incline (%)'),
            'has_pace' => Yii::t('app', 'Pace (min/km)'),
            'has_speed' => Yii::t('app', 'Speed (km/h)'),
            'has_pulse' => Yii::t('app', 'Pulse'),
            'has_height' => Yii::t('app', 'Height (cm)'),
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

    public function getProgressionChainMainExercises()
    {
        return $this->hasMany(ProgressionChainMainExercise::class, ['weight_exercise_id' => 'id']);
    }

    public function getWeightExerciseAbilityRatios($joinWithExercises = false)
    {
        if (!$this->has_weight) return null;
        $query = WeightExerciseAbilityRatio::find()->where(['or', ['exercise_id_1' => $this->id], ['exercise_id_2' => $this->id]]);
        if ($joinWithExercises) {
            $query->joinWith('exercise1');
            $query->joinWith('exercise2');
        }
        return $query->all();
    }

    public function getWeightExerciseAbilityRatiosMapped()
    {
        if (!$this->has_weight) return null;
        $weightExerciseAbilityRatios = $this->getWeightExerciseAbilityRatios();
        $res = [];
        foreach ($weightExerciseAbilityRatios as $wear) {
            if ($wear->exercise_id_1 === $this->id) {
                $res[$wear->exercise_id_2] = $wear->ratio_percent;
            } else {
                $res[$wear->exercise_id_1] = $wear->ratio_percent;
            }
        }
        return $res;
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

    public function getVideoThumb()
    {
        if ($this->equipment_video_thumbnail) return $this->equipment_video_thumbnail;

        $user = Yii::$app->user->identity;
        $school = $user->getSchool();
        return $school->video_thumbnail;
    }

    public function getInterchangeableOtherExercisesObj()
    {
        $exerciseIds = $this->getInterchangeableExerciseIds();
        return self::find()->where(['in', 'id', $exerciseIds])->all();
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

    public static function getAllExerciseSelect2Options()
    {
        return array_map(function ($exercise) {
            return [
                'id' => $exercise['id'],
                'text' => $exercise['name'],
            ];
        }, self::find()->where(['author_id' => Yii::$app->user->identity->id])->asArray()->all());
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
        $isOriginalExerciseAndNotReplacedCondition = "fitness_workoutexercises.exercise_id = {$this->id} AND fitness_replacement_exercises.id IS NULL";
        $isReplacementExerciseCondition = ['fitness_replacement_exercises.exercise_id' => $this->id];

        $query = WorkoutExerciseEvaluation::find()
            ->joinWith('workoutExercise')
            ->andWhere(['user_id' => $userId])
            ->andWhere([
                'or',
                $isOriginalExerciseAndNotReplacedCondition,
                $isReplacementExerciseCondition,
            ]);

        if ($workoutIdToExclude) {
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

    public function averageAbilityInWorkout($workout, $methodName)
    {
        $addedEvaluationsCount = 0;
        $averageAbilitiesSum = null;

        foreach ($workout->workoutExercises as $workoutExercise) {
            if ($workoutExercise->evaluation && (
                    $workoutExercise->exercise_id == $this->id && !$workoutExercise->isReplaced()
                    || $workoutExercise->isReplaced() && $workoutExercise->replacementExercise->exercise_id == $this->id
                )) {

                $oneRepMaxRangeAverage = OneRepMaxCalculator::oneRepMaxRangeToAverage($workoutExercise->evaluation->$methodName());
                if ($oneRepMaxRangeAverage) {
                    $addedEvaluationsCount++;
                    $averageAbilitiesSum === null
                        ? $averageAbilitiesSum = $oneRepMaxRangeAverage
                        : $averageAbilitiesSum += $oneRepMaxRangeAverage;
                }
            }
        }

        return $addedEvaluationsCount > 0 ? $averageAbilitiesSum / $addedEvaluationsCount : null;
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

            $now = new DateTime();
            $workoutCreatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $lastWorkout->created_at);
            $workoutCreatedWeeksAgo = floor($now->diff($workoutCreatedAt)->days / 7);

            $penaltyForEachWeek = 0.05;

            $averageAbility = $this->averageAbilityInWorkout($lastWorkout, $methodName);

            if (!is_null($averageAbility)) {
                $res = $averageAbility * (1 - ($penaltyForEachWeek * $workoutCreatedWeeksAgo));
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

        $averageAbility = $this->averageAbilityInWorkout($workout, $methodName);
        if (!is_null($averageAbility)) $res = $averageAbility;

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

    public static function initForForm()
    {
        $model = new Exercise();
        $model->author_id = Yii::$app->user->identity->id;
        $model->needs_evaluation = true;
        $model->popularity_type = 'AVERAGE';
        $model->is_bodyweight = null;
        $model->has_reps = true;
        $model->has_weight = true;
        return $model;
    }

    public static function initForProgressionChainForm()
    {
        $model = self::initForForm();
        $model->is_bodyweight = true;
        return $model;
    }

    private function recursivelyFindBodyweightChainMainExercise($exerciseIdToIgnore, &$percentages, &$alreadyCheckedExerciseIdPairs)
    {
        $wears = $this->getWeightExerciseAbilityRatios(true);

        foreach ($wears as $wear) {
            if (($wear->exercise_id_1 == $this->id ? $wear->exercise_id_2 : $wear->exercise_id_1) == $exerciseIdToIgnore) {
                continue;
            };

            if ($wear->exercise_id_1 == $this->id) {
                $otherExercise = $wear->exercise2;
                $percentages[] = $wear->ratioPercentAsFraction();
            } else {
                $otherExercise = $wear->exercise1;
                $percentages[] = 1 / $wear->ratioPercentAsFraction();
            }

            if ($otherExercise->progressionChainMainExercises) {
                $percentages[] = $otherExercise->progressionChainMainExercises[0]->rep_bw_ratio_percent;
                return [
                    'chain_main_exercise' => $otherExercise->progressionChainMainExercises[0],
                    'percentages' => $percentages,
                ];
            }

            $thisExercisePairHasAlreadyBeenChecked = !empty(array_filter(
                $alreadyCheckedExerciseIdPairs,
                function ($a) use ($otherExercise) {
                    return $this->id === $a[0] && $otherExercise->id === $a[1]
                        || $this->id === $a[1] && $otherExercise->id === $a[0];
                }));
            if ($thisExercisePairHasAlreadyBeenChecked) {
                continue;
            } else {
                $alreadyCheckedExerciseIdPairs[] = [$this->id, $otherExercise->id];
                $iterRes = $otherExercise->recursivelyFindBodyweightChainMainExercise($exerciseIdToIgnore, $percentages, $alreadyCheckedExerciseIdPairs);
                if ($iterRes) return $iterRes;
            }

            array_pop($percentages);
        }

        return null;
    }

    private function findBodyweightExerciseChainMainExercise()
    {
        if (!$this->has_weight) return null;

        $percentages = [];
        if (!empty($this->progressionChainMainExercises)) {
            $percentages[] = $this->progressionChainMainExercises[0]->rep_bw_ratio_percent;
            return [
                'chain_main_exercise' => $this->progressionChainMainExercises[0],
                'percentages' => $percentages,
            ];
        }

        $wears = $this->getWeightExerciseAbilityRatios(true);

        foreach ($wears as $wear) {
            if ($wear->exercise_id_1 == $this->id) {
                $otherExercise = $wear->exercise2;
                $percentages[] = $wear->ratioPercentAsFraction();
            } else {
                $otherExercise = $wear->exercise1;
                $percentages[] = 1 / $wear->ratioPercentAsFraction();
            }
            if ($otherExercise->progressionChainMainExercises) {
                $percentages[] = $otherExercise->progressionChainMainExercises[0]->rep_bw_ratio_percent;
                return [
                    'chain_main_exercise' => $otherExercise->progressionChainMainExercises[0],
                    'percentages' => $percentages,
                ];
            }

            $alreadyCheckedExerciseIdPairs = [];
            $iterRes = $otherExercise->recursivelyFindBodyweightChainMainExercise($this->id, $percentages, $alreadyCheckedExerciseIdPairs);

            if ($iterRes) return $iterRes;

            array_pop($percentages);
        }

        return null;
    }

    private function getAveragePercentage($array)
    {
        return array_sum($array) / count($array);
    }

    public function getBodyweightChainExercise()
    {
        $mainExerciseArr = self::findBodyweightExerciseChainMainExercise();
        return $mainExerciseArr ? [
            'chain_exercise' => $mainExerciseArr['chain_main_exercise']->progressionChainExercise,
            'percentage' => $this->getAveragePercentage($mainExerciseArr['percentages']),
        ] : null;
    }

    public function getBodyweightChain()
    {
        $bodyweightChainExercise = self::getBodyweightChainExercise();
        return $bodyweightChainExercise ? $bodyweightChainExercise['chain_exercise']->progressionChain : null;
    }

    public function getBodyweightChainExerciseToAssign()
    {
        $bodyweightChain = $this->getBodyweightChain();
        if (!$bodyweightChain) return null;
        //TODO: calculate which is the most appropriate exercise to assign from the chain
        $bodyweightChainExercise = self::getBodyweightChainExercise();
        return [
            'exercise' => $bodyweightChainExercise['chain_exercise']->exercise,
            'percentage' => $bodyweightChainExercise['percentage'],
        ];
    }
}
