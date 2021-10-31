<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\School;
use app\models\JoinSchoolForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class JoinSchoolController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $userContext = Yii::$app->user->identity;
                            return $userContext->isStudent();
                        }
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
        $model = new JoinSchoolForm;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->addUserToNewSchool();
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Profile successfully linked to a new school') . '!');
            return $this->redirect(['lekcijas/index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
