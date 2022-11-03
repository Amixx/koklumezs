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
            foreach ($post['workoutExercises'] as $workoutExSet) {
                $workoutExercise = new WorkoutExercise;
                $workoutExercise->workout_id = $workout->id;
                $workoutExercise->exercise_id = $workoutExSet['exercise']['id'];
                $workoutExercise->weight = $workoutExSet['weight'];
                $workoutExercise->reps = $workoutExSet['reps'];
                $workoutExercise->time_seconds = $workoutExSet['time_seconds'];
                $workoutExercise->save();
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
            ->joinWith('workoutExercises')
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
