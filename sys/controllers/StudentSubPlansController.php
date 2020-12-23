<?php

namespace app\controllers;

use Yii;
use app\models\StudentSubPlans;
use app\models\Users;
use app\models\PlanFiles;
use app\models\SentInvoices;
use app\models\StudentSubplanPauses;
use app\models\SchoolSubPlans;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

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

    public function actionView($id){
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        $subplan = StudentSubPlans::getForStudent($id);
        $planFiles = PlanFiles::getFilesForPlan($subplan["plan_id"])->asArray()->all();
        $planPauses = null;
        if(StudentSubplanPauses::studentHasAnyPauses($id)){
            $planPauses = new ActiveDataProvider([
                'query' => StudentSubplanPauses::getForStudent($id),
            ]);
        }
        $newPause = new StudentSubplanPauses;    
        $remainingPauseWeeks = StudentSubPlans::getRemainingPauseWeeks($id);
        $planCurrentlyPaused = StudentSubPlans::isPlanCurrentlyPaused($id);

        return $this->render('view', [
            'subplan' => $subplan,
            'planFiles' => $planFiles,
            'planPauses' => $planPauses,
            'newPause' => $newPause,
            'remainingPauseWeeks' => $remainingPauseWeeks,
            'planCurrentlyPaused' => $planCurrentlyPaused,
        ]);
    }

    public function actionDelete($userId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        StudentSubPlans::findOne(['user_id' => $userId])->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionIncreaseTimesPaid($userId, $invoiceId)
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        
        $plan = StudentSubPlans::findOne(['user_id' => $userId]);
        $plan->times_paid += 1;

        $subplan = SchoolSubPlans::findOne($plan['plan_id']);

        $user = Users::findOne(['id' => $userId]);

        $inlineCss = '
            body {
                font-family: Arial, serif;
                color: rgb(0, 0, 0);
                font-weight: normal;
                font-style: normal;
                text-decoration: none
            }

            .bordered-table {
                width: 100%; border: 1px solid black;
                border-collapse:collapse;
            }

            .bordered-table td, th {
                border: 1px solid black;
                text-align:center;
            }

            .bordered-table th {
                font-weight:normal;
                padding:8px 4px;
            }

            .bordered-table td {
                padding: 32px 4px;
            }

            .font-l {
                font-size: 18px;
            }

            .font-m {
                font-size: 15px;
            }

            .font-s {
                font-size: 14px;
            }

            .font-xs {
                font-size: 13px;
            }

            .align-center {
                text-align:center;
            }

            .align-right {
                text-align:right;
            }

            .lh-2 {
                line-height:2;
            }

            .leftcol {
                width:140px;
            }

            .info {
                line-height:unset;
                margin-top:16px;
            }
        ';

        $message = "Nosūtam apmaksāto rēķinu par nodarbībām. Lai jauka diena!";
        if(isset($subplan['message']) && $subplan['message']) $message = $subplan['message'];

        $timestamp = time();
        $folderUrl = 'invoices/'.date("M", $timestamp) . "_" . date("Y", $timestamp);
        if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);
        $invoiceBasePath = $folderUrl . "/";
        $id = $invoiceId;
        $title = "rekins-$id.pdf";
        $invoicePath = $invoiceBasePath.$title;

        $userFullName = $user['first_name'] . " " . $user['last_name'];

        $content = $this->renderPartial('invoiceTemplate', [
            'id' => $id,
            'fullName' => $userFullName,
            'email' => $user['email'],
            'subplan' => $subplan,
            'isAdvanceInvoice' => false,
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
        
        $sent = Yii::$app
            ->mailer
            ->compose(
                ['html' => 'rekins-html', 'text' => 'rekins-text'],
                ['message' => $message])
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            ->setTo($user['email'])
            ->setSubject("Rēķins $id - " . Yii::$app->name)
            ->attach($invoicePath)
            ->send();

        if ($sent) {
            $invoice = new SentInvoices;
            $invoice->user_id = $user['id'];
            $invoice->invoice_number = $id;
            $invoice->is_advance = false;
            $invoice->plan_name = $subplan['name'];
            $invoice->plan_price = $subplan['monthly_cost'];
            $invoice->plan_start_date = $plan['start_date'];
            $invoice->save();
        }else{
            Yii::$app
                ->mailer
                ->compose([
                    'html' => 'invoice-not-sent-html', 
                    'text' => 'invoice-not-sent-text'
                ], [
                    'email' => $user['email'],
                ])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
                ->setTo(Yii::$app->params['senderEmail'])
                ->setSubject("Skolēnam nenosūtījās rēķins!")
                ->send();
        }
        
        $plan->save();

        return $this->redirect(Yii::$app->request->referrer);
    }
}
