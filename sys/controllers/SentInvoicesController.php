<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\SentInvoices;
use app\models\SentInvoicesSearch;
use app\models\StudentSubPlans;
use app\models\RegisterPaymentForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\helpers\InvoiceManager;
use yii\helpers\Url;

class SentInvoicesController extends Controller
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email) || Users::isStudent(Yii::$app->user->identity->email);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'get-for-payment' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $userContext = Yii::$app->user->identity;
        $isTeacher = $userContext->isTeacher();

        $searchModel = new SentInvoicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'isTeacher' => $isTeacher,
        ]);
    }

    public function actionUpdate($invoiceNumber)
    {
        $model = $this->findAdvanceByNumber($invoiceNumber);
        $post = Yii::$app->request->post();
        $processRequest = isset($post['SentInvoices']) && isset($post['SentInvoices']['paid_date']) && $post['SentInvoices']['paid_date'];

        if ($processRequest) {
            InvoiceManager::createRealInvoice($model, $invoiceNumber, $model['user_id'], $post['SentInvoices']['paid_date'], $model);

            return $this->redirect(Yii::$app->request->referrer);
        }

        $realInvoice = SentInvoices::getRealInvoice($model->invoice_number);

        return $this->render('update', [
            'model' => $model,
            'realInvoice' => $realInvoice,
        ]);
    }

    public function actionRegisterAdvancePayment($userId)
    {
        $post = Yii::$app->request->post();
        $model = new RegisterPaymentForm();

        if ($post && $model->load($post) && $model->validate()) {
            InvoiceManager::createRealInvoiceForMultipleMonths($model);
            Yii::$app->session->setFlash('success', \Yii::t('app', 'Paymant registered') . '!');
            return $this->redirect(["user/index"]);
        }

        $studentSubPlans = StudentSubPlans::getForStudentMapped($userId);

        return $this->render("advance-payment", [
            'model' => $model,
            'studentSubPlans' => $studentSubPlans,
        ]);
    }

    public function actionGetForPayment()
    {
        $invoice = $this->findById(Yii::$app->request->post()['id']);
        if (!$invoice) return null;

        return json_encode([
            'user_id' => $invoice['user_id'],
            'studentsubplan_id' => $invoice['studentsubplan_id'],
            'invoice_number' => $invoice['invoice_number'],
            'plan_name' => $invoice['plan_name'],
            'sent_date' => $invoice['sent_date'],
            'plan_price' => $invoice['plan_price'],
            'plan_start_date' => $invoice['plan_start_date'],
        ]);
    }

    public function actionHandlePaymentSuccess()
    {
        $get = Yii::$app->request->get();
        $invoiceId = $get['invoice_id'];

        $advanceInvoice = SentInvoices::findOne($invoiceId);

        $model = new SentInvoices;
        $model->user_id = $advanceInvoice['user_id'];
        $model->studentsubplan_id = $advanceInvoice['studentsubplan_id'];
        $model->invoice_number = $advanceInvoice['invoice_number'];
        $model->is_advance = false;
        $model->plan_name = $advanceInvoice['plan_name'];
        $model->plan_price = $advanceInvoice['plan_price'];
        $model->plan_start_date = $advanceInvoice['plan_start_date'];
        $model->save();

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

        return $this->redirect(Url::to(['student-invoices/index', 'state' => 'success']));
    }

    public function actionDelete($id)
    {
        $deleted = $this->findById($id)->delete();

        if ($deleted) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Invoice deleted') . '!');
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Problem encountered! Couldn\'t delete an invoice') . '!');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findById($id)
    {
        if (($model = SentInvoices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findAdvanceByNumber($invoiceNumber)
    {
        if (($model = SentInvoices::find()->where(['invoice_number' => $invoiceNumber])->joinWith("student")->joinWith("studentSubplan")->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
