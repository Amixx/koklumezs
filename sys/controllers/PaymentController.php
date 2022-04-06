<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\Difficulties;
use app\models\School;
use app\models\SchoolSubPlans;
use app\models\SentInvoices;
use app\models\StudentSubPlans;
use app\models\User;
use InvalidArgumentException;
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

        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);

        $paymentIntent = $stripe->paymentIntents->retrieve(
            $get['payment_intent'],
            []
        );
        $stripe->customers->update(
            $paymentIntent['customer'],
            [
                'invoice_settings' => ['default_payment_method' => $paymentIntent['payment_method']]
            ]
        );

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

        $paymentIntent = $singlePayment ? $this->generatePaymentIntent($totalPrice) : $this->prepareSubscription();

        if($paymentIntent['status'] == 'succeeded') {
            return $this->redirect([
                'success', 
                'planId' => $post['plan_id'],
                'allAtOnce' => $singlePayment,
                'payment_intent' => $paymentIntent['id'],
            ]);
        }

        return json_encode($paymentIntent);
    }

    public function actionPrepareInvoicePayment()
    {
        $post = Yii::$app->request->post();
        $invoice = SentInvoices::findOne($post['invoice_id']);
        $priceInCents = $invoice['plan_price'] * 100;

        return json_encode($this->generatePaymentIntent($priceInCents));
    }

    private function prepareSubscription()
    {
        $post = Yii::$app->request->post();
        $userContext = Yii::$app->user->identity;
        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);

        if(!isset($post['plan_price_id'])){
            throw new InvalidArgumentException("Nav norādīta plāna cena (parametrs plan_price_id). Plāna cenas nosaukums jānorāda obligāti!");
        }

        if ($userContext->stripe_id) {
            $subscription = $stripe->subscriptions->create([
                'customer' => $userContext->stripe_id,
                'items' => [
                    ['price' => $post['plan_price_id']],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
        } else {
            $customer = $this->createCustomer();
            $subscription = $stripe->subscriptions->create([
                'customer' => $customer['id'],
                'items' => [
                    ['price' => $post['plan_price_id']],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
            ]);
        }

        return $subscription['latest_invoice']['payment_intent'];
    }

    private function generatePaymentIntent($price)
    {
        $userContext = Yii::$app->user->identity;
        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);

        if ($userContext->stripe_id) {
            $customerId = $userContext->stripe_id;
        } else {
            $customer = $this->createCustomer();
            $customerId = $customer['id'];
        }

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $price,
            'currency' => 'eur',
            'customer' => $customerId,
            'setup_future_usage' => 'off_session'
            // 'payment_method_types' => ['card'],
            // 'receipt_email' => ...
        ]);
        
        return $paymentIntent;
    }

    private function createCustomer()
    {
        $userContext = Yii::$app->user->identity;
        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);
        $customer = $stripe->customers->create([
            'name' => $userContext->first_name . " " . $userContext->last_name,
            'email' => $userContext->email,
            'description' => $userContext->about ? $userContext->about : "",
        ]);

        $user = User::findOne($userContext->id);
        $user->stripe_id = $customer['id'];
        $user->update();

        return $customer;
    }
}
