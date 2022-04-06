<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\SentInvoices;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class StudentInvoicesController extends Controller
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
                            return Users::isStudent(Yii::$app->user->identity->email);
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
        $userContext = Yii::$app->user->identity;
        $invoices = SentInvoices::getUnpaidForStudent($userContext->id);

        $get = Yii::$app->request->get();

        if (isset($get['state']) && $get['state'] === 'success') {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Payment was successful! Thank you!'));
        }

        return $this->render('index', [
            'invoices' => $invoices,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
}
