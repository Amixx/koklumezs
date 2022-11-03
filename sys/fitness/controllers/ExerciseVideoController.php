<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\ExerciseVideo;
use app\models\Users;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExerciseVideoController extends Controller
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate($exercise_id)
    {
        $post = Yii::$app->request->post();
        $model = new ExerciseVideo;
        $model->author_id = Yii::$app->user->identity->id;
        $model->exercise_id = $exercise_id;

        if ($model->load($post)) {
            if($model->save()) {
                return $this->redirect(Url::to(['fitness-exercises/view', 'id' => $exercise_id]));
            } else {
//                var_dump($model);
            }
        }

        return $this->render('@app/fitness/views/exercise-video/create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::to(['fitness-exercises/view', 'id' => $model['exercise_id']]));
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/exercise-video/update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id, $exercise_id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Exercise video deleted!'));

        return $this->redirect(Url::to(['fitness-exercises/view', 'id' => $exercise_id]));
    }

    public function actionApiList()
    {
        $exerciseVideos = ExerciseVideo::find()->asArray()->all();
        return json_encode($exerciseVideos);
    }

    protected function findModel($id)
    {
        if (($model = ExerciseVideo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
