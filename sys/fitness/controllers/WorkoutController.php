<?php

namespace app\fitness\controllers;

use app\fitness\models\Workout;
use app\models\Users;
use app\fitness\models\WorkoutExerciseSet;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UnprocessableEntityHttpException;

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
        $userContext = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $workout = new Workout;
        $workout->author_id = $userContext->id;
        $workout->student_id = $post['studentId'];
        $workout->description = $post['description'];

        if ($workout->save()) {
            foreach ($post['workoutExerciseSets'] as $workoutExSet) {
                $workoutExerciseSet = new WorkoutExerciseSet;
                $workoutExerciseSet->workout_id = $workout->id;
                $workoutExerciseSet->exerciseset_id = $workoutExSet['exerciseSet']['id'];
                $workoutExerciseSet->weight = $workoutExSet['weight'];
                $workoutExerciseSet->save();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Workout successfully created') . '!');
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Something went wrong while creating workout') . '!');
        }
    }

    public function actionApiOfStudent($id)
    {
        $query = Workout::find()
            ->where(['student_id' => $id])
            ->joinWith('workoutExerciseSets')
            ->joinWith('evaluation')
            ->joinWith('messageForCoach')
            ->orderBy(['id' => SORT_DESC]);
        $studentWorkouts = $query->asArray()->all();
        return json_encode($studentWorkouts);
    }

    public function actionAbandon($id) {
        $workout = Workout::findOne(['id' => $id]);
        $workout->setAsAbandoned();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Workout has been abandoned') . '!');
        return $this->redirect(['fitness-student-exercises/index']);
    }
}
