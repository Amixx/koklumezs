<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\SchoolSubPlans;
use app\models\SentInvoices;
use app\models\StudentSubPlans;
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
                    'prepareInvoicePayment' => ['POST']
                ],
            ],
        ];
    }

    public function actionSuccess()
    {
        $userContext = Yii::$app->user->identity;
        $get = Yii::$app->request->get();
        $planId = (int)$get['planId'];
        $allAtOnce = filter_var($get['allAtOnce'], FILTER_VALIDATE_BOOLEAN);
        $subPlan = SchoolSubPlans::findOne($planId);

        $studentSubplan = new StudentSubPlans;
        $studentSubplan->user_id = $userContext->id;
        $studentSubplan->plan_id = $planId;
        $studentSubplan->is_active = true;
        $studentSubplan->start_date = date('Y-m-d H:i:s', time());

        if ($allAtOnce) {
            $studentSubplan->sent_invoices_count = $subPlan['months'];
            $studentSubplan->times_paid = $subPlan['months'];
        } else {
            $studentSubplan->sent_invoices_count = 1;
            $studentSubplan->times_paid = 1;
        }

        $studentSubplan->save();

        return $this->redirect(["lekcijas/index", 'payment_success' => 1]);
    }

    public function actionGeneratePaymentIntent()
    {

        $post = Yii::$app->request->post();
        $subPlan = SchoolSubPlans::findOne($post['plan_id']);
        $monthlyPriceInCents = $subPlan->price() * 100;
        $allAtOnceDiscount = 0.1;
        $singlePayment = filter_var($post['single_payment'], FILTER_VALIDATE_BOOLEAN);

        $totalPrice = $singlePayment && $subPlan['months'] > 0
            ? round($monthlyPriceInCents * $subPlan['months'] * (1 - $allAtOnceDiscount))
            : $monthlyPriceInCents;

        return json_encode($this->generatePaymentIntent($totalPrice));
    }

    public function actionPrepareInvoicePayment()
    {
        $post = Yii::$app->request->post();
        $invoice = SentInvoices::findOne($post['invoice_id']);
        $priceInCents = $invoice['plan_price'] * 100;

        return json_encode($this->generatePaymentIntent($priceInCents));
    }

    private function generatePaymentIntent($price)
    {
        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $price,
            'currency' => 'eur',
            // 'payment_method_types' => ['card'],
        ]);

        return $paymentIntent;
    }
}
