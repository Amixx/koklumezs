<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\SentInvoices;
use app\models\SentInvoicesSearch;
use app\models\StudentSubPlans;
use app\models\SchoolSubplanParts;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

class SentInvoicesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $searchModel = new SentInvoicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // public function actionView($id)
    // {
    //     $isGuest = Yii::$app->user->isGuest;
    //     if (!$isGuest) {
    //         $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
    //         if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
    //     }
    //     return $this->render('view', [
    //         'model' => $this->findById($id),
    //     ]);
    // }

    // public function actionCreate()
    // {
    //     $isGuest = Yii::$app->user->isGuest;
    //     if (!$isGuest) {
    //         $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
    //         if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
    //     }
    //     $model = new SentInvoices();

    //     $schoolId = School::getCurrentSchoolId();
    //     if ($model->load(Yii::$app->request->post())) {
    //         $model->school_id = $schoolId;
    //         if ($model->save()) {
    //             return $this->redirect(['view', 'id' => $model->id]);
    //         }
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }

    public function actionUpdate($number)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $model = $this->findAdvanceByNumber($number);
        $realInvoice = SentInvoices::getRealInvoice($model->invoice_number);
        $post = Yii::$app->request->post();

        if(isset($post['SentInvoices']) && isset($post['SentInvoices']['paid_date']) && $post['SentInvoices']['paid_date']){
            $plan = StudentSubPlans::findOne(['user_id' => $model['user_id']]);
            $plan->times_paid += 1;

            $subplan = $plan["plan"];

            $inlineCss = SentInvoices::getInvoiceCss();

            $timestamp = time();
            $teacherId = Yii::$app->user->identity->id;
            $folderUrl = "files/user_$teacherId/invoices/".date("M", $timestamp) . "_" . date("Y", $timestamp)."/real";
            if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);
            $invoiceBasePath = $folderUrl . "/";
            $title = "rekins-$number.pdf";
            $invoicePath = $invoiceBasePath.$title;

            $userFullName = $model['student']['first_name'] . " " . $model['student']['last_name'];

            $content = $this->renderPartial('realInvoiceTemplate', [
                'number' => $number,
                'fullName' => $userFullName,
                'email' => $model['student']['email'],
                'subplan' => $subplan,
                'datePaid' => $post['SentInvoices']['paid_date'],
                'months' => 1,
                'payer' => $model['student']['payer'],
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_FILE,
                'filename' => $invoicePath,
                'content' => $content,
                'cssInline' => $inlineCss,
                'options' => ['title' => $title],
            ]);

            $pdf->render();

            $invoice = new SentInvoices;
            $invoice->user_id = $model['student']['id'];
            $invoice->invoice_number = $number;
            $invoice->is_advance = false;
            $invoice->plan_name = $subplan['name'];
            $invoice->plan_price = SchoolSubplanParts::getPlanTotalCost($subplan['id']);
            $invoice->plan_start_date = $plan['start_date'];
            $invoice->sent_date = $post['SentInvoices']['paid_date'];
            $invoice->save();

            Yii::$app->session->setFlash('success', 'Rēķina apmaksa reģistrēta!');

            $plan->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->render('update', [
            'model' => $model,
            'realInvoice' => $realInvoice,
        ]);
    }

    public function actionRegisterAdvancePayment($userId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $post = Yii::$app->request->post();
        $processRequest = $post && isset($post["SentInvoices"]['paid_months']) && isset($post["SentInvoices"]['paid_date']) && $post["SentInvoices"]['paid_months'] && $post["SentInvoices"]['paid_date'];

        if($processRequest){
            $months = intval($post["SentInvoices"]['paid_months'], 10);
            $date = $post["SentInvoices"]['paid_date'];

            $plan = StudentSubPlans::findOne(['user_id' => $userId]);
            $plan->times_paid += $months;

            $subplan = $plan["plan"];

            $user = Users::find()->where(['users.id' => $userId])->joinWith('payer')->one();

            $inlineCss = SentInvoices::getInvoiceCss();

            $timestamp = time();
            $teacherId = Yii::$app->user->identity->id;
            $folderUrl = "files/user_$teacherId/invoices/".date("M", $timestamp) . "_" . date("Y", $timestamp)."/real";
            if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);
            $invoiceBasePath = $folderUrl . "/";
            $number = mt_rand(10000000, 99999999);
            $title = "rekins-$number.pdf";
            $invoicePath = $invoiceBasePath.$title;

            $userFullName = $user['first_name'] . " " . $user['last_name'];

            $content = $this->renderPartial('realInvoiceTemplate', [
                'number' => $number,
                'fullName' => $userFullName,
                'email' => $user['email'],
                'subplan' => $subplan,
                'datePaid' => $date,
                'months' => $months, 
                'payer' => $user['payer'],
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_FILE,
                'filename' => $invoicePath,
                'content' => $content,
                'cssInline' => $inlineCss,
                'options' => ['title' => $title],
            ]);

            $pdf->render();

            $invoice = new SentInvoices;
            $invoice->user_id = $user['id'];
            $invoice->invoice_number = $number;
            $invoice->is_advance = false;
            $invoice->plan_name = $subplan['name'];
            $invoice->plan_price = SchoolSubplanParts::getPlanTotalCost($subplan['id']);
            $invoice->plan_start_date = $plan['start_date'];
            $invoice->sent_date = $date;
            $invoice->save();

            Yii::$app->session->setFlash('success', 'Maksājums tika reģistrēts!');
            
            $plan->save();

            return $this->redirect(["user/index"]);
        }

        $model = new SentInvoices();

        return $this->render("advance-payment", [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $deleted = $this->findById($id)->delete();

         if($deleted) {
            Yii::$app->session->setFlash('success', 'Rēķins izdzēsts!');
        } else {
            Yii::$app->session->setFlash('error', 'Notikusi kļūda! Rēķins netika izdzēsts!');
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

    protected function findAdvanceByNumber($number)
    {
        if (($model = SentInvoices::find()->where(['invoice_number' => $number])->joinWith("student")->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
