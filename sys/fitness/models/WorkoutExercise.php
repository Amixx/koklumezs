<?php

namespace app\fitness\models;

use app\models\Users;
use Yii;

function wrapInBold($str)
{
    return "<strong>$str</strong>";
}

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
            [[
                'workout_id',
                'exercise_id',
                'reps',
                'actual_reps',
                'time_seconds',
                'mode',
                'incline_percent',
                'pace',
                'speed',
                'pulse',
                'height',
            ], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [
                ['actual_weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['resistance_bands'], 'string'],
            [['workout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Workout::class, 'targetAttribute' => ['workout_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app', 'Workout ID'),
            'exercise_id' => \Yii::t('app', 'Exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
            'reps' => \Yii::t('app', 'Repetitions'),
            'actual_weight' => \Yii::t('app', 'Actual weight'),
            'actual_reps' => \Yii::t('app', 'Actual repetitions'),
            'time_seconds' => \Yii::t('app', 'Time (seconds)'),
            'resistance_bands' => \Yii::t('app', 'Resistance bands'),
            'mode' => \Yii::t('app', 'Mode'),
            'incline_percent' => \Yii::t('app', 'Incline (%)'),
            'pace' => \Yii::t('app', 'Pace (min/km)'),
            'speed' => \Yii::t('app', 'Speed (km/h)'),
            'pulse' => \Yii::t('app', 'Pulse'),
            'height' => \Yii::t('app', 'Height (cm)'),
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

    public function getExercise()
    {
        return $this->hasOne(Exercise::class, ['id' => 'exercise_id'])->joinWith('videos');
    }

    public function getReplacementExercise()
    {
        return $this->hasOne(ReplacementExercise::class, ['workoutexercise_id' => 'id'])->joinWith('exercise');
    }

    public function getEvaluation()
    {
        return $this->hasOne(WorkoutExerciseEvaluation::class, ['workoutexercise_id' => 'id']);
    }

    public function isReplaced()
    {
        return !!$this->replacementExercise;
    }

    public function getExerciseForStudent()
    {
        return $this->isReplaced() ? $this->replacementExercise->exercise : $this->exercise;
    }

    private function getAbilityRatio()
    {
        if (!$this->isReplaced()) return 1;

        $workoutUserId = $this->workout->student->id;

        $lastTwoWeeksOneRepMaxAverages = [
            'original' => $this->exercise->estimatedAvgAbilityOfUser($workoutUserId),
            'replacement' => $this->replacementExercise->exercise->estimatedAvgAbilityOfUser($workoutUserId)
        ];

        if (is_null($lastTwoWeeksOneRepMaxAverages['original']) || is_null($lastTwoWeeksOneRepMaxAverages['replacement'])) {
            // TODO: what to do in this situation?
            return 1;
        }

        return $lastTwoWeeksOneRepMaxAverages['replacement']['ability'] / $lastTwoWeeksOneRepMaxAverages['original']['ability'];
    }

    private function getAvgAbilityThisWorkoutAndBeforeRatio()
    {
        $avgAbilityBeforeThisWorkout = $this->exercise->estimatedAvgAbilityOfUser($this->workout->student_id, $this->workout_id);
        if(!$avgAbilityBeforeThisWorkout) return null;
        $avgAbilityThisWorkout = $this->exercise->estimatedAvgAbilityInWorkout($this->workout);
        $ratio = isset($avgAbilityThisWorkout['ability']) && isset($avgAbilityBeforeThisWorkout['type'])
            ? $avgAbilityThisWorkout['ability'] / $avgAbilityBeforeThisWorkout['ability']
            : null;
        return ['ratio' => $ratio, 'type' => $avgAbilityThisWorkout['type']];
    }

    public function setActualWeightAndReps(){
        $this->actual_weight = $this->weight;
        $this->actual_reps = $this->reps;
        $ratioArr = $this->getAvgAbilityThisWorkoutAndBeforeRatio();
        if(!$ratioArr) {
            $this->save();
            return;
        }

        if($ratioArr['ratio']) {
            if($ratioArr['type'] === '1rm' && $this->weight || !$this->actual_weight) {
                $this->actual_weight = round($this->weight * $ratioArr['ratio'], 1);
            }
            if($ratioArr['type'] === 'reps' && $this->reps || !$this->actual_reps) {
                $this->actual_reps = round($this->reps * $ratioArr['ratio']);
            }
        }

        $this->save();
    }

    public function repsWeightTimeFormatted()
    {
        if (!$this->isReplaced()) {
            $weight = $this->actual_weight;
            $reps = $this->actual_reps;
            $time = $this->time_seconds;
        } else {
            $weight = $this->replacementExercise->weight;
            $reps = $this->replacementExercise->reps;
            $time = $this->replacementExercise->time_seconds;
        }

        $hasWeight = !!$weight;
        $hasReps = !!$reps;
        $hasTime = !!$time;


        $res = '';
        if ($hasReps) {
            $res = wrapInBold($this->actual_reps) . ' reizes';
        } else if ($hasTime) {
            $res = wrapInBold($time) . ' sekundes';
        }
        if ($hasWeight) {
            $res .= ' ar ' . wrapInBold($this->actual_weight) . ' kg svaru';
        }

        return $res;
    }

    // if there is a video for the exact reps amount or time, show that video
    // else fall back to exercise main video (exercise->video)
    public function videoToDisplay()
    {
        foreach ($this->exercise->videos as $video) {
            $repsMatch = $video->reps && $this->reps && $this->reps == $video->reps;
            $timeMatches = $video->time_seconds && $this->time_seconds && $this->time_seconds == $video->time_seconds;

            $forReps = $video->reps && !$video->time_seconds && $repsMatch;
            $forTime = $video->time_seconds && !$video->reps && $timeMatches;
            $forBoth = $video->reps && $video->time_seconds && $repsMatch && $timeMatches;

            if ($forReps || $forTime || $forBoth) return $video->value;
        }

        return $this->exercise->video;
    }

    public function timeFormatted()
    {
        if (!$this->time_seconds) return '0:00';
        $seconds = $this->time_seconds % 60;
        $min = floor($this->time_seconds / 60);
        if ($min == 0) return "0:{$seconds}";
        else return "{$min}:{$seconds}";
    }

    public static function getVideoToDisplay($workoutExerciseId)
    {
        $workoutExercise = self::findOne(['id' => $workoutExerciseId]);
        return $workoutExercise->videoToDisplay();
    }

    private function weightReplacedByWeight($exerciseToReplaceWith)
    {
        return !$this->exercise->is_bodyweight && !$exerciseToReplaceWith->is_bodyweight;
    }

    private function weightReplacedByBodyweight($exerciseToReplaceWith)
    {
        return !$this->exercise->is_bodyweight && $exerciseToReplaceWith->is_bodyweight;
    }

    private function bodyweightReplacedByWeight($exerciseToReplaceWith)
    {
        return $this->exercise->is_bodyweight && !$exerciseToReplaceWith->is_bodyweight;
    }

    private function getReplacementExerciseWeight($exerciseToReplaceWith)
    {
        if (is_null($this->actual_weight)) return null;

        $abilityRatio = $this->getAbilityRatio();

        if ($this->weightReplacedByWeight($exerciseToReplaceWith)) {
            return round($this->actual_weight * $abilityRatio, 1);
        }
        if ($this->weightReplacedByBodyweight($exerciseToReplaceWith)) return null;
        if ($this->bodyweightReplacedByWeight($exerciseToReplaceWith)) {
            //TODO: what to do here? calculate needed weight from bodyweight reps
            return null;
        }

        return null;
    }

    private function getReplacementExerciseReps($exerciseToReplaceWith)
    {
        if ($this->weightReplacedByWeight($exerciseToReplaceWith)) {
            return $this->actual_reps;
        }
        if ($this->weightReplacedByBodyweight($exerciseToReplaceWith)) {
            if (!$this->actual_reps) return null;

            $originalAbility = $this->exercise->estimatedAvgAbilityOfUser($this->workout->student->id);
            $replacementAbility = $exerciseToReplaceWith->estimatedAvgAbilityOfUser($this->workout->student->id);
            // TODO: improve this!
            $originalRpe = RpeCalculator::calculateRpe(
                $this->actual_reps,
                $this->actual_weight / ($originalAbility ? $originalAbility['ability'] : 1));

            return round(($replacementAbility ? $replacementAbility['ability'] : 10) * ($originalRpe / 10), 0);
        }
        if ($this->bodyweightReplacedByWeight($exerciseToReplaceWith)) {
            //TODO: what to do here? calculate needed reps from bodyweight reps
            return null;
        }

        return null;
    }

    private function getReplacementExerciseTimeSeconds($exerciseToReplaceWith)
    {
        if ($this->weightReplacedByWeight($exerciseToReplaceWith)) return $this->time_seconds;
        if ($this->weightReplacedByBodyweight($exerciseToReplaceWith)) {
            if (!$this->time_seconds) return null;

            $originalAbility = $this->exercise->estimatedAvgAbilityOfUser($this->workout->student->id);
            $replacementAbility = $exerciseToReplaceWith->estimatedAvgAbilityOfUser($this->workout->student->id);
            // TODO: improve this!
            $originalRpe = RpeCalculator::calculateRpe(
                $this->time_seconds,
                $this->actual_weight / ($originalAbility ? $originalAbility['ability'] : 1));

            return round(($replacementAbility ? $replacementAbility['ability'] : 30) * ($originalRpe / 10), 0);
        }
        if ($this->bodyweightReplacedByWeight($exerciseToReplaceWith)) return null;

        return null;
    }


    public function replaceByExercise($replacementExerciseId)
    {
        if ($this->isReplaced()) return null;

        $exerciseToReplaceWith = Exercise::find()->where(['id' => $replacementExerciseId])->one();

        $replacementExercise = new ReplacementExercise;
        $replacementExercise->workoutexercise_id = $this->id;
        $replacementExercise->exercise_id = $replacementExerciseId;
        $replacementExercise->weight = $this->getReplacementExerciseWeight($exerciseToReplaceWith);
        $replacementExercise->reps = $this->getReplacementExerciseReps($exerciseToReplaceWith);
        $replacementExercise->time_seconds = $this->getReplacementExerciseTimeSeconds($exerciseToReplaceWith);

        return $replacementExercise->save();
    }
}
