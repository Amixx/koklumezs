<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\SchoolSubPlans;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class PaymentController extends Controller
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
                    'generatePaymentIntent' => ['POST'],
                ],
            ],
        ];
    }

    public function actionSuccess()
    {
        return $this->render('index', []);
    }

    public function actionGeneratePaymentIntent()
    {
        $secretKey = Yii::$app->params['stripe']['sk'];

        $post = Yii::$app->request->post();
        $subPlan = SchoolSubPlans::findOne($post['plan_id']);
        $priceInCents = $subPlan->price() * 100;

        $stripe = new \Stripe\StripeClient($secretKey);
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $priceInCents,
            'currency' => 'eur',
            // 'payment_method_types' => ['card'],
        ]);

        return json_encode($paymentIntent);
    }
}
