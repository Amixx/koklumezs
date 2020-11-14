<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\PlanFiles;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class StudentSubPlansController extends Controller
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
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    public function actionView($id){
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $subplan = StudentSubPlans::getForStudent($id);
        $planFiles = PlanFiles::getFilesForPlan($subplan["plan_id"])->asArray()->all();

        return $this->render('view', [
            'subplan' => $subplan,
            'planFiles' => $planFiles,
        ]);
    }

    public function actionDelete($userId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        StudentSubPlans::findOne(['user_id' => $userId])->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionIncreaseTimesPaid($userId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $plan = StudentSubPlans::findOne(['user_id' => $userId]);
        $plan->times_paid += 1;
        $plan->save();

        return $this->redirect(Yii::$app->request->referrer);
    }
}
