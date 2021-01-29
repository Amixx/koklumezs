<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\PlanParts;
use app\models\School;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class PlanPartsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $schoolId = School::getCurrentSchoolId();
        $dataProvider = new ActiveDataProvider([
            'query' => PlanParts::find()->where(['school_id' => $schoolId]),
        ]);
        $model = new PlanParts;

        $post = Yii::$app->request->post();
        if($post){
            $model->load($post);
            $model->school_id = $schoolId;

            if($model->validate() && $model->save()){
                Yii::$app->session->setFlash('success', 'Plāna daļa izveidota!');
                $model = new PlanParts;
            }
        }
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);        
    }

    public function actionDelete($id)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = PlanParts::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
