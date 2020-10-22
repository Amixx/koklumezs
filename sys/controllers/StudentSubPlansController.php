<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
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
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->username);
                        }
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
}
