<?php

namespace app\fitness\controllers;

use app\fitness\models\Workout;
use app\models\Users;
use app\fitness\models\WorkoutExercise;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class WorkoutController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !empty(Yii::$app->user->identity);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'api-create' => ['POST'],
                    'api-save-as-draft' => ['POST'],
                    'api-publish-draft' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex($studentId)
    {
        $studentFullName = Users::getFullName(Users::findOne($studentId));
        return $this->render('@app/fitness/views/workout/index', [
            'studentId' => $studentId,
            'studentFullName' => $studentFullName,
        ]);
    }

    public function actionApiCreate()
    {
        $this->saveWorkout(false);
    }

    public function actionApiSaveAsDraft()
    {
        $this->saveWorkout(true);
    }

    private function saveWorkout($asDraft)
    {
        $userContext = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $workout = new Workout;
        $workout->author_id = $userContext->id;
        $workout->student_id = $post['studentId'];
        $workout->description = $post['description'];
        $workout->is_draft = $asDraft;

        if ($workout->save()) {
            foreach ($post['workoutExercises'] as $workoutExSet) {
                $workoutExercise = new WorkoutExercise;
                $workoutExercise->load($workoutExSet, '');
                $workoutExercise->exercise_id = $workoutExSet['exercise']['id'];
                $workoutExercise->workout_id = $workout->id;
                $workoutExercise->save();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Workout successfully created') . '!');
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong while creating workout') . '!');
        }
    }

    public function actionApiPublishDraft($workoutId)
    {
        $workout = Workout::findOne($workoutId);
        $workout->is_draft = false;
        $workout->save();
        return json_encode($workout);
    }


    public function actionApiOfStudent($id)
    {
        $query = Workout::find()
            ->where(['student_id' => $id])
            ->joinWith('workoutExercises')
            ->joinWith('evaluation')
            ->joinWith('messageForCoach')
            ->orderBy(['id' => SORT_DESC]);
        $studentWorkouts = $query->all();

        $evaluationExtraAttributes = [];
        foreach ($studentWorkouts as $workoutKey => &$studentWorkout) {
            foreach ($studentWorkout->workoutExercises as $wExerciseKey => $wExercise) {
                if ($wExercise->evaluation) {
                    $evaluationExtraAttributes[$workoutKey][$wExerciseKey] = [
                        'evaluation_text' => $wExercise->evaluation->getEvaluationText(),
                        'one_rep_max_range' => $wExercise->evaluation->getOneRepMaxRange(),
                        'max_reps_range' => $wExercise->evaluation->getMaxRepsRange(),
                        'max_time_seconds_range' => $wExercise->evaluation->getMaxTimeSecondsRange(),
                    ];
                }
            }
            $studentWorkout = $studentWorkout->toArray([], ['workoutExercises.exercise', 'workoutExercises.replacementExercise.exercise', 'workoutExercises.evaluation', 'messageForCoach'], true);
        }

        // TODO: this is very very bad!!! might cause performance issues! think of a better realisation!
        foreach ($evaluationExtraAttributes as $workoutKey => $exerciseKeyToEvaluationOneRepMaxRange) {
            foreach ($exerciseKeyToEvaluationOneRepMaxRange as $wExerciseKey => $evaluationOneRepMaxRange) {
                $evaluation = &$studentWorkouts[$workoutKey]['workoutExercises'][$wExerciseKey]['evaluation'];
                $evaluation['evaluation_text'] = $evaluationOneRepMaxRange['evaluation_text'];
                $evaluation['one_rep_max_range'] = $evaluationOneRepMaxRange['one_rep_max_range'];
                $evaluation['max_reps_range'] = $evaluationOneRepMaxRange['max_reps_range'];
                $evaluation['max_time_seconds_range'] = $evaluationOneRepMaxRange['max_time_seconds_range'];
            }
        }

        return json_encode($studentWorkouts);
    }

    public function actionAbandon($id)
    {
        $workout = Workout::findOne(['id' => $id]);
        $workout->setAsAbandoned();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Workout has been abandoned') . '!');
        return $this->redirect(['fitness-student-exercises/index']);
    }
}
