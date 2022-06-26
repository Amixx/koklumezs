<?php

namespace app\fitness\controllers;

use Yii;
use app\fitness\models\Exercise;
use app\models\Users;
use app\fitness\models\ExerciseSearch;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExerciseController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new ExerciseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('@app/fitness/views/exercise/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('@app/fitness/views/exercise/view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new Exercise();
        $model->author_id = Yii::$app->user->identity->id;

        if ($model->load($post) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('@app/fitness/views/exercise/create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);

        if ($model->load($post) && $model->save()) {
            return $this->redirect(Url::previous());
        }

        Url::remember(Yii::$app->request->referrer);

        return $this->render('@app/fitness/views/exercise/update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Exercise::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
