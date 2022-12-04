<?php

namespace app\fitness\models;

use Yii;
use app\models\Users;

class WorkoutExerciseEvaluation extends \yii\db\ActiveRecord
{
    public $oneRepMaxRange;

    public static function tableName()
    {
        return 'fitness_workoutexerciseevaluations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['workoutexercise_id', 'user_id', 'evaluation'], 'required'],
            [['workoutexercise_id', 'user_id', 'evaluation'], 'integer'],
            [['created', 'oneRepMaxRange'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['workoutexercise_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkoutExercise::class, 'targetAttribute' => ['workoutexercise_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workoutexercise_id' => \Yii::t('app', 'Workout exercise'),
            'user_id' => \Yii::t('app', 'Client'),
            'evaluation' => \Yii::t('app', 'Evaluation'),
            'created' => \Yii::t('app', 'Created'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkoutExercise()
    {
        return $this->hasOne(WorkoutExercise::class, ['id' => 'workoutexercise_id'])->joinWith('replacementExercise');
    }

    public function getEvaluationText(){
        $wExercise = $this->getEvaluatedExercise();
        $reps = $this->getEvaluatedExerciseReps();
        $difficultyEvaluationModel = $reps
            ? DifficultyEvaluation::createForReps($reps)
            : DifficultyEvaluation::createForTime($wExercise->time_seconds);

        return $difficultyEvaluationModel->createEvaluationText($this->evaluation);
    }

    private function getEvaluatedExercise(){
        return $this->workoutExercise->replacementExercise
            ?: $this->workoutExercise;
    }

    private function getEvaluatedExerciseWeight(){
        return $this->workoutExercise->replacementExercise
            ? $this->workoutExercise->replacementExercise->weight
            : ($this->workoutExercise->actual_weight ?: $this->workoutExercise->weight);
    }

    private function getEvaluatedExerciseReps(){
        return $this->workoutExercise->replacementExercise
            ? $this->workoutExercise->replacementExercise->reps
            :  ($this->workoutExercise->actual_reps ?: $this->workoutExercise->reps);
    }

    public function getOneRepMaxRange()
    {
        $wExercise = $this->getEvaluatedExercise();
        $weight = $this->getEvaluatedExerciseWeight();
        $reps = $this->getEvaluatedExerciseReps();
        if (
            $wExercise->exercise->is_bodyweight ||
            !$weight
            || !$wExercise->exercise->renderEvaluation()
            || (!$reps && !$wExercise->time_seconds)) {
            return null;
        }

        $difficultyEvaluationModel = $reps
            ? DifficultyEvaluation::createForReps($reps)
            : DifficultyEvaluation::createForTime($wExercise->time_seconds);

        $minMaxTotalRepsOrTime = $difficultyEvaluationModel->createMinMaxTotalRepsOrTimeSeconds($this->evaluation);

        return OneRepMaxCalculator::oneRepMaxRange(
            $weight,
            $minMaxTotalRepsOrTime['min'],
            $minMaxTotalRepsOrTime['max']);
    }

    public function getMaxRepsRange(){
        $wExercise = $this->getEvaluatedExercise();
        $reps = $this->getEvaluatedExerciseReps();
        if (
            !$wExercise->exercise->is_bodyweight
            || !$wExercise->exercise->renderEvaluation()
            || !$reps) {
            return null;
        }

        return DifficultyEvaluation::createForReps($reps)->createMinMaxTotalRepsOrTimeSeconds($this->evaluation);
    }

    public function getMaxTimeSecondsRange(){
        $wExercise = $this->getEvaluatedExercise();
        if (
            !$wExercise->exercise->is_bodyweight
            || !$wExercise->exercise->renderEvaluation()
            || !$wExercise->time_seconds) {
            return null;
        }

        return DifficultyEvaluation::createForReps($wExercise->time_seconds)->createMinMaxTotalRepsOrTimeSeconds($this->evaluation);
    }
}
