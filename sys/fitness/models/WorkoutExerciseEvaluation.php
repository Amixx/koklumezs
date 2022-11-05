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
        return $this->hasOne(WorkoutExercise::class, ['id' => 'workoutexercise_id']);
    }

    public function getEvaluationText(){
        $difficultyEvaluationModel = $this->workoutExercise->reps
            ? DifficultyEvaluation::createForReps($this->workoutExercise->reps)
            : DifficultyEvaluation::createForTime($this->workoutExercise->time_seconds);

        return $difficultyEvaluationModel->createEvaluationText($this->evaluation);
    }

    public function getOneRepMaxRange()
    {
        if (
            !$this->workoutExercise->weight
            || !$this->workoutExercise->exercise->renderEvaluation()
            || (!$this->workoutExercise->reps && !$this->workoutExercise->reps)) {
            return null;
        }

        $difficultyEvaluationModel = $this->workoutExercise->reps
            ? DifficultyEvaluation::createForReps($this->workoutExercise->reps)
            : DifficultyEvaluation::createForTime($this->workoutExercise->time_seconds);

        $minMaxTotalRepsOrTime = $difficultyEvaluationModel->createMinMaxTotalRepsOrTimeSeconds($this->evaluation);

        return OneRepMaxCalculator::oneRepMaxRange(
            $this->workoutExercise->weight,
            $minMaxTotalRepsOrTime['min'],
            $minMaxTotalRepsOrTime['max']);
    }
}
