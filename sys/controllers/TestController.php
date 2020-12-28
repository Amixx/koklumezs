<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\db\Connection;
use app\models\Difficulties;
use app\models\LecturesDifficulties;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LectureAssignment;
use app\models\SectionsVisible;
use app\models\SentInvoices;
use app\models\Lectures;
use app\models\SchoolTeacher;
use app\models\SchoolLecture;
use app\models\Users;
use app\models\StudentSubPlans;
use app\models\SchoolSubPlans;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use app\models\CommentresponsesSearch;
use yii\base\Event;
use yii\web\View;
use app\models\School;
use app\models\LectureViews;
use app\models\SchoolStudent;
use app\models\CommentResponses;
use app\models\UserLectures;
use app\models\Userlectureevaluations;
use kartik\mpdf\Pdf;
use app\widgets\ChatRoom;

class TestController extends Controller
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }

        // $lectures = Lectures::getLecturesObjectsForUser([]);
        // foreach ($lectures as $lecture) {
        //     $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($lecture['id']);
        //     $lecture = Lectures::findOne(['id', $lecture['id']]);
        //     $difficultySum = 0;
        //     foreach ($lectureDifficulties as $difficulty) {
        //         if (is_numeric($difficulty)) {
        //             $difficultySum += (10 * pow(2, ($difficulty / 3)));
        //         }
        //     }
        //     $difficultiesCount = count($lectureDifficulties);
        //     $newComplexity = 0;
        //     if ($difficultiesCount > 0) {
        //         $newComplexity = (int) round($difficultySum / count($lectureDifficulties));
        //     }
        //     $lecture['complexity'] = $newComplexity;
        // //     echo $lecture->update();
        //     echo "<br>";
        // }

        // $users = Users::find()->asArray()->all();
        // foreach ($users as $user) {
        //     if ($user['username'] == null) {
        //         $newUser = Users::findOne(['id', $user['id']]);
        //         $newUser['username'] = $user['email'];
        // //         echo $newUser->update();
        //     }
        // }

        // $user = Users::findByEmail(Yii::$app->user->identity->username);
        // $userLectures = UserLectures::find()->where(['user_id' => 220])->asArray()->all();
        // $opentimes = array_map(function ($ulecture) {
        //     return $ulecture['opentime'];
        // }, $userLectures);
        // $firstOpenTime = null;
        // foreach ($opentimes as $time) {
        //     if ($firstOpenTime == null || ($time !== null && $time < $firstOpenTime)) {
        //         $firstOpenTime = $time;
        //     }
        // }
        // return $firstOpenTime;

        // var_dump(LectureViews::getDayResult(Yii::$app->user->identity->id));

        // $user = Users::findByEmail(Yii::$app->user->identity->username);
        // echo Yii::$app
        //     ->mailer
        //     ->compose(
        //         ['html' => 'lecture-assigned-html', 'text' => 'lecture-assigned-text'],
        //         ['user' => $user/*, 'lecture' => $lecture*/]
        //     )
        //     ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
        //     ->setTo('liepinsimantsimis2001@gmail.com')
        //     ->setSubject('Jaunas nodarbības - ' . Yii::$app->name)
        //     ->send();

        // return $this->render("index", [
        //     'recipientId' => 478
        // ]);

        date_default_timezone_set('EET');
        $time = time();
        echo date("y-m-d H:i:s", $time);
    }

    public function actionTest()
    {
        $users = Users::getAllStudents();
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

        $timestamp = time();
        $folderUrl = 'invoices/'.date("M", $timestamp) . "_" . date("Y", $timestamp);
        if (!is_dir($folderUrl)) mkdir($folderUrl, 0777, true);
        $invoiceBasePath = $folderUrl . "/";

        foreach ($users as $user) {
            $studentSubplan = StudentSubPlans::getForStudent($user["id"]);
            if($studentSubplan !== null){
                if($user['id'] == 478){
                    echo "te";
                    $studentSubplan['sent_invoices_count'] += 1;
                    $studentSubplan->save();
                }
                
                echo "<br>";
                echo $user["id"];
            }
            
            echo "<hr>";
            // if ($studentSubplan !== null) {
            //     $today = date('d.m.Y');
            //     $match_date = date('d.m.Y', strtotime($studentSubplan["start_date"]));

            //     $today_split = explode(".", $today);
            //     $match_date_split = explode(".", $match_date);

            //     if ($today_split[0] === $match_date_split[0]) {
            //         $userFullName = $user['first_name'] . " " . $user['last_name'];

            //         $subplan = SchoolSubPlans::findOne($studentSubplan['plan_id']);

            //         $id = mt_rand(10000000, 99999999);
            //         $title = "rekins-$id.pdf";
            //         $invoicePath = $invoiceBasePath.$title;

            //         $content = $this->renderPartial('invoiceTemplate', [
            //             'id' => $id,
            //             'fullName' => $userFullName,
            //             'email' => $user['email'],
            //             'subplan' => $subplan
            //         ]);

            //         $pdf = new Pdf([
            //             'mode' => Pdf::MODE_UTF8,
            //             'format' => Pdf::FORMAT_A4,
            //             'orientation' => Pdf::ORIENT_PORTRAIT,
            //             'destination' => Pdf::DEST_FILE,
            //             'filename' => $invoicePath,
            //             'content' => $content,
            //             'cssInline' => $inlineCss,
            //             'options' => ['title' => $title],
            //         ]);

            //         $pdf->render();

            //         $planModel = StudentSubPlans::findOne($studentSubplan['id']);
            //         $planUnlimited = $subplan['months'] === 0;
            //         $planEnded = $planModel['sent_invoices_count'] == $subplan['months'];
            //         $hasPaidInAdvance = $planModel['times_paid'] > $planModel['sent_invoices_count'];

            //         if(!$planEnded || $planUnlimited){
            //             if(!$hasPaidInAdvance){
            //                 $message = "Nosūtam rēķinu par tekošā mēneša nodarbībām. Lai jauka diena!";
            //                 if(isset($subplan['message']) && $subplan['message']) $message = $subplan['message'];
                            
            //                 $sent = Yii::$app
            //                     ->mailer
            //                     ->compose(
            //                         ['html' => 'rekins-html', 'text' => 'rekins-text'],
            //                         ['message' => $message])
            //                     ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            //                     ->setTo($user['email'])
            //                     ->setSubject("Rēķins $id - " . Yii::$app->name)
            //                     ->attach($invoicePath)
            //                     ->send();

            //                 if ($sent) {
            //                     $planModel['sent_invoices_count'] += 1;
            //                     $planModel->update();

            //                     $invoice = new SentInvoices;
            //                     $invoice->user_id = $user['id'];
            //                     $invoice->invoice_number = $id;
            //                     $invoice->plan_name = $subplan['name'];
            //                     $invoice->plan_price = $subplan['monthly_cost'];
            //                     $invoice->plan_start_date = $studentSubplan['start_date'];
            //                     $invoice->save();
            //                 }else{
            //                     Yii::$app
            //                         ->mailer
            //                         ->compose([
            //                             'html' => 'invoice-not-sent-html', 
            //                             'text' => 'invoice-not-sent-text'
            //                         ], [
            //                             'email' => $user['email'],
            //                         ])
            //                         ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            //                         ->setTo(Yii::$app->params['senderEmail'])
            //                         ->setSubject("Skolēnam nenosūtījās rēķins!")
            //                         ->send();
            //                 }
            //             }else{
            //                 $planModel['sent_invoices_count'] += 1;
            //                 $planModel->update();
            //             }
            //         }
            //     }
            // }
        }
    }
}
