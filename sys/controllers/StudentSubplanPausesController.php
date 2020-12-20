<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\PlanFiles;
use app\models\StudentSubplanPauses;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

class StudentSubplanPausesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

   public function actionCreate()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = new StudentSubplanPauses();

        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks(Yii::$app->user->identity->id);

        if ($model->load(Yii::$app->request->post())) {
           if($model["weeks"] > $remainingPauseWeeks){
               Yii::$app->session->setFlash('error', 'Neizdevās nosūtīt e-pasta adresi, lai atjaunotu paroli.');
           }else{
                $model->save();
           }           
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
