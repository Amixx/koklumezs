<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\SentInvoices;
use app\models\SentInvoicesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\helpers\InvoiceManager;

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

    public function actionUpdate($invoiceNumber)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $model = $this->findAdvanceByNumber($invoiceNumber);
        $post = Yii::$app->request->post();
        $processRequest = isset($post['SentInvoices']) && isset($post['SentInvoices']['paid_date']) && $post['SentInvoices']['paid_date'];

        if($processRequest){
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
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $post = Yii::$app->request->post();
        $processRequest = $post && isset($post["SentInvoices"]['paid_months']) && isset($post["SentInvoices"]['paid_date']) && $post["SentInvoices"]['paid_months'] && $post["SentInvoices"]['paid_date'];

        if($processRequest){
            InvoiceManager::createRealInvoiceForMultipleMonths($userId, $post["SentInvoices"]['paid_months'],  $post["SentInvoices"]['paid_date']);

            Yii::$app->session->setFlash('success', 'Maksājums tika reģistrēts!');            
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

    protected function findAdvanceByNumber($invoiceNumber)
    {
        if (($model = SentInvoices::find()->where(['invoice_number' => $invoiceNumber])->joinWith("student")->joinWith("studentSubplan")->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
