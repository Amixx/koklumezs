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

        $student = Users::findOne($userContext->id);
        $student->status = Users::STATUS_ACTIVE;
        $student->update();

        return $this->redirect(["lekcijas/index", 'payment_success' => 1]);
    }

    public function actionGeneratePaymentIntent()
    {
        $post = Yii::$app->request->post();
        $subPlan = SchoolSubPlans::findOne($post['plan_id']);
        $priceType = $post['price_type'];

        $planPrices = SchoolSubPlans::getSubPlanStripePrices($subPlan);

        $paymentIntent = $priceType === 'single'
            ? $this->generatePaymentIntent((int)$planPrices['single'] * 100)
            : $this->prepareSubscription($subPlan['stripe_recurring_price_id']);

        if ($paymentIntent['status'] == 'succeeded') {
            return $this->redirect([
                'success',
                'planId' => $post['plan_id'],
                'allAtOnce' => $priceType === 'single',
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

    private function prepareSubscription($recurringPriceId)
    {
        $userContext = Yii::$app->user->identity;
        $secretKey = Yii::$app->params['stripe']['sk'];
        $stripe = new \Stripe\StripeClient($secretKey);

        $subscriptionConfig = [
            'items' => [['price' => $recurringPriceId]],
            'expand' => ['latest_invoice.payment_intent'],
        ];

        if ($userContext->stripe_id) {
            $customer = $stripe->customers->retrieve(
                $userContext->stripe_id,
                []
            );

            $subscriptionConfig['customer'] = $customer['id'];

            $paymentMethods = $stripe->customers->allPaymentMethods(
                $userContext->stripe_id,
                ['type' => 'card']
            );

            if (!$customer['invoice_settings']['default_payment_method']) {
                if (!empty($paymentMethods['data'])) {
                    $stripe->customers->update(
                        $userContext->stripe_id,
                        [
                            'invoice_settings' => ['default_payment_method' => $paymentMethods['data'][0]['id']]
                        ]
                    );
                } else {
                    $subscriptionConfig['payment_behavior'] = 'default_incomplete';
                }
            }
        } else {
            $customer = $this->createCustomer();
            $subscriptionConfig['customer'] = $customer['id'];
            $subscriptionConfig['payment_behavior'] = 'default_incomplete';
        }

        $subscription = $stripe->subscriptions->create($subscriptionConfig);

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

        return $stripe->paymentIntents->create([
            'amount' => $price,
            'currency' => 'eur',
            'customer' => $customerId,
            'setup_future_usage' => 'off_session'
        ]);
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
