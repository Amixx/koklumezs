<?php

namespace app\controllers;

use Yii;
use app\models\School;
use app\models\SchoolFaqs;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class SchoolFaqsController extends Controller
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
        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        $dataProvider = new ActiveDataProvider([
            'query' => SchoolFaqs::find()->where(['school_id' => $schoolId]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new SchoolFaqs();

        $userContext = Yii::$app->user->identity;
        $schoolId = $userContext->getSchool()->id;
        if ($model->load(Yii::$app->request->post())) {
            $model->school_id = $schoolId;
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionForStudents()
    {
        $faqs = SchoolFaqs::getForCurrentSchool();

        return $this->render('for-students', [
            'faqs' => $faqs,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = SchoolFaqs::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
