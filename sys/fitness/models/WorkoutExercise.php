<?php

namespace app\fitness\models;

use app\models\Users;

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
            [['workout_id', 'exercise_id', 'replaced_by_exercise_id', 'reps', 'time_seconds'], 'integer'],
            [
                ['weight'], 'number',
                'numberPattern' => '/^\d+(.\d{1,2})?$/'
            ],
            [['workout_id'], 'exist', 'skipOnError' => true, 'targetClass' => Workout::class, 'targetAttribute' => ['workout_id' => 'id']],
            [['exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['exercise_id' => 'id']],
            [['replaced_by_exercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exercise::class, 'targetAttribute' => ['replaced_by_exercise_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workout_id' => \Yii::t('app', 'Workout ID'),
            'exercise_id' => \Yii::t('app', 'Exercise ID'),
            'replaced_by_exercise_id' => \Yii::t('app', 'Replaced by exercise ID'),
            'weight' => \Yii::t('app', 'Weight'),
            'reps' => \Yii::t('app', 'Repetitions'),
            'time_seconds' => \Yii::t('app', 'Time (seconds)'),
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
        return $this->hasOne(Exercise::class, ['id' => 'replaced_by_exercise_id'])->joinWith('videos');
    }

    public function getEvaluation()
    {
        return $this->hasOne(WorkoutExerciseEvaluation::class, ['workoutexercise_id' => 'id']);
    }

    public function isReplaced()
    {
        return !!$this->replaced_by_exercise_id;
    }

    public function getExerciseForStudent()
    {
        return $this->isReplaced() ? $this->replacementExercise : $this->exercise;
    }

    private function getWeightRatio()
    {
        if (!$this->isReplaced()) return 1;

        $lastTwoWeeksOneRepMaxAverages = [
            'original' => $this->exercise->lastTwoWeeksAvgOneRepMax(),
            'replacement' => $this->replacementExercise->lastTwoWeeksAvgOneRepMax()
        ];

        if(is_null($lastTwoWeeksOneRepMaxAverages['original']) || is_null($lastTwoWeeksOneRepMaxAverages['replacement'])){
            // TODO: what to do in this situation?
            return 1;
        }

        return $lastTwoWeeksOneRepMaxAverages['replacement'] / $lastTwoWeeksOneRepMaxAverages['original'];
    }

    public function repsWeightTimeFormatted()
    {
        $hasWeight = !!$this->weight;
        $hasReps = !!$this->reps;
        $hasTime = !!$this->time_seconds;

        $weightRatio = $this->getWeightRatio();
        $computedWeight = round($this->weight * $weightRatio, 1);

        $res = '';
        if ($hasReps) {
            $res = wrapInBold($this->reps) . ' reizes';
        } else if ($hasTime) {
            $res = wrapInBold($this->time_seconds) . ' sekundes';
        }
        if ($hasWeight) {
            $res .= ' ar ' . wrapInBold($computedWeight) . ' kg svaru';
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

    public function replaceByExercise($replacementExerciseId)
    {
        $this->replaced_by_exercise_id = $replacementExerciseId;
        $this->update();
        return $this;
    }
}
