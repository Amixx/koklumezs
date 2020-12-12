<?php

namespace app\controllers;

use app\models\LectureAssignment;
use app\models\SentInvoices;
use app\models\Sentlectures;
use app\models\Studentgoals;
use app\models\UserLectures;
use app\models\Lectures;
use app\models\Users;
use app\models\StudentSubPlans;
use app\models\School;
use app\models\SchoolTeacher;
use app\models\SchoolSubPlans;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;

/**
 * CronController sends spam a lot.
 */
class CronController extends Controller
{

    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $get = Yii::$app->request->queryParams;
        if (!isset($get['send'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $onlyThoseWithoutDontBother = true;
        $users = Users::getActiveStudents($onlyThoseWithoutDontBother);
        $queue = [];
        $spam = true;
        $dbg = Yii::$app->request->get('dbg');
        ob_start();
        foreach ($users as $user_id => $userVals) {
            //if( $dbg ){
            echo '<strong>' . $userVals['email'] . ' automātiskā sūtīšana</strong><br />';
            //}
            $queue = UserLectures::getUnsentLectures($user_id);
            $last = UserLectures::getLastEvaluatedLecture($user_id);
            if (!isset($get['withmore'])) {
                // more, more, MORE!!! NO MORE..
                $u = Users::findOne($user_id);
                $u->more_lecture_requests = 0;
                $u->save(false);
            }
            //user has some assigned lectures that has not sent
            if ($queue) {
                foreach ($queue as $q) {
                    $lecture = Lectures::findOne($q->lecture_id);
                    if ($lecture) {
                        $model = new Sentlectures();
                        $model->user_id = $user_id;
                        $model->lecture_id = $lecture->id;
                        $model->created = date('Y-m-d H:i:s', time());
                        //$sent = UserLectures::sendEmail($user_id, $model->lecture_id);
                        $sent =  true;
                        if (!$sent) {
                            echo '<strong>Nosūtīts e-pasts administratoram</strong><br />';
                            UserLectures::sendAdminEmail($user_id, $model->lecture_id, 0);
                        } else {
                            echo '<strong>' . $userVals['email'] . ' nosūtīta nodarbība ' . $model->lecture_id . '</strong><br />';
                        }
                        //if fails email, still make visible to student
                        $sent = 1;
                        $model->sent = (int) $sent;
                        $model->save(false);
                        $q->sent = (int) $sent;
                        $q->save(false);
                    }
                }
                if (!isset($get['withmore'])) {
                    //Laikam būs loģiskāk ja e-pastus sūtītu tikai 2x nedēļā, pa vienam epastam, ka ir ienākuši jauni uzdevumi. Savādāk cilvēkus gāž riņķī daudzie e-pasti. 
                    //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                    //$sent = UserLectures::sendEmail($user_id);
                }
                echo '<hr />';
            } else {
                echo '<strong>' . $userVals['email'] . ' nebija sakrājušās nodarbības ko nosūtīt</strong><br />';
            }
            if ($last) {
                $count = $last->sent_times;
                if ($count == 1) {
                    $x = 4;
                } elseif ($count == 2) {
                    //Respektīvi ievietojam nākamo uzdevumu ar vērtējumu "8 (diezgan sarežģīti un nepieciešams ko vieglāku nākamajā reizē)".
                    //8 (itkā saprotu, ebt pirksti neklausa) Max-x-2/3
                    $x = 8;
                } else {
                    $x = Studentgoals::getUserDifficultyCoef($user_id);
                }
                LectureAssignment::giveNewAssignment($user_id, $x, $last->lecture_id, $spam, $dbg);
                $last->sent_times = (int) $last->sent_times + 1;
                $last->update();
                //Uzdevumus izvēlas un piešķir VIENĀ UN TAJĀ PAŠĀ BRĪDĪ. trešdienā un sestdienā 
                $queue = UserLectures::getUnsentLectures($user_id);
                if ($queue) {
                    foreach ($queue as $q) {
                        $lecture = Lectures::findOne($q->lecture_id);
                        if ($lecture) {
                            $model = new Sentlectures();
                            $model->user_id = $user_id;
                            $model->lecture_id = $lecture->id;
                            $model->created = date('Y-m-d H:i:s', time());
                            //$sent = UserLectures::sendEmail($user_id, $model->lecture_id);
                            $sent =  true;
                            if (!$sent) {
                                echo '<strong>Nosūtīts e-pasts administratoram</strong><br />';
                                UserLectures::sendAdminEmail($user_id, $model->lecture_id, 0);
                            } else {
                                echo '<strong>' . $userVals['email'] . ' nosūtīta nodarbība ' . $model->lecture_id . '</strong><br />';
                            }
                            //if fails email, still make visible to student
                            $sent = 1;
                            $model->sent = (int) $sent;
                            $model->update(false);
                            $q->sent = (int) $sent;
                            $q->update(false);
                        }
                    }
                    if (!isset($get['withmore'])) {
                        //Laikam būs loģiskāk ja e-pastus sūtītu tikai 2x nedēļā, pa vienam epastam, ka ir ienākuši jauni uzdevumi. Savādāk cilvēkus gāž riņķī daudzie e-pasti. 
                        //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                        //$sent = UserLectures::sendEmail($user_id);
                    }
                    echo '<hr />';
                } else {
                    echo '<strong>' . $userVals['email'] . ' nebija nodarbības ko nosūtīt</strong><br />';
                }
            } else {
                $x = Studentgoals::getUserDifficultyCoef($user_id);
                LectureAssignment::giveNewAssignment($user_id, $x, null, $spam, $dbg);
                echo '<strong>' . $userVals['email'] . ' tiek piešķirtas jaunās nodarbības</strong><br />';
                //Uzdevumus izvēlas un piešķir VIENĀ UN TAJĀ PAŠĀ BRĪDĪ. trešdienā un sestdienā 
                $queue = UserLectures::getUnsentLectures($user_id);
                if ($queue) {
                    foreach ($queue as $q) {
                        $lecture = Lectures::findOne($q->lecture_id);
                        if ($lecture) {
                            $model = new Sentlectures();
                            $model->user_id = $user_id;
                            $model->lecture_id = $lecture->id;
                            $model->created = date('Y-m-d H:i:s', time());
                            //$sent = UserLectures::sendEmail($user_id, $model->lecture_id);
                            $sent =  true;
                            if (!$sent) {
                                echo '<strong>Nosūtīts e-pasts administratoram</strong><br />';
                                UserLectures::sendAdminEmail($user_id, $model->lecture_id, 0);
                            } else {
                                echo '<strong>' . $userVals['email'] . ' nosūtīta nodarbība ' . $model->lecture_id . '</strong><br />';
                            }
                            //if fails email, still make visible to student
                            $sent = 1;
                            $model->sent = (int) $sent;
                            $model->update(false);
                            $q->sent = (int) $sent;
                            $q->update(false);
                        }
                    }
                    if (!isset($get['withmore'])) {
                        //Laikam būs loģiskāk ja e-pastus sūtītu tikai 2x nedēļā, pa vienam epastam, ka ir ienākuši jauni uzdevumi. Savādāk cilvēkus gāž riņķī daudzie e-pasti. 
                        //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                        //$sent = UserLectures::sendEmail($user_id);
                    }
                    echo '<hr />';
                } else {
                    echo '<strong>' . $userVals['email'] . ' nebija nodarbības ko nosūtīt</strong><br />';
                }
            }
            if ($dbg) {
                echo '<hr />';
            }
        }

        //$model->update();
        $log = ob_get_clean();
        return $this->renderPartial('index', [
            'log' => $log,
        ]);
    }

    /**
     *
     * @return mixed
     */
    public function actionUserlectures()
    {
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }
        $get = Yii::$app->request->queryParams;
        if (!isset($get['id'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $users = Users::find()->where(['id' => $get['id']])->asArray()->all();
        $tmp = [];
        foreach ($users as $u) {
            $tmp[$u['id']] = $u;
        }
        $users = $tmp;
        $queue = [];
        $spam = true;
        $dbg = Yii::$app->request->get('dbg');
        ob_start();
        foreach ($users as $user_id => $userVals) {
            //if( $dbg ){
            echo '<strong>' . $userVals['email'] . '</strong><br />';
            //}
            $queue = UserLectures::getUnsentLectures($user_id);

            $last = UserLectures::getLastEvaluatedLecture($user_id);
            if (!isset($get['withmore'])) {
                // more, more, MORE!!! NO MORE..
                $u = Users::findOne($user_id);
                $u->more_lecture_requests = 0;
                $u->save(false);
            }
            //user has some assigned lectures that has not sent
            if ($queue) {
                foreach ($queue as $q) {
                    $lecture = Lectures::findOne($q->lecture_id);
                    if ($lecture) {
                        $model = new Sentlectures();
                        $model->user_id = $user_id;
                        $model->lecture_id = $lecture->id;
                        $model->created = date('Y-m-d H:i:s', time());
                        //noņemt e-pastu automātisko sūtīšanu. Cilvēkiem uzrādās tie kā "Nedroši"
                        //$sent = UserLectures::sendEmail($user_id);//, $model->lecture_id                        
                        //if (!$sent) {
                        //    UserLectures::sendAdminEmail($user_id, $model->lecture_id, 0);
                        //}
                        //if fails email, still make visible to student
                        $sent = 1;
                        $model->sent = (int) $sent;
                        $model->save(false);
                        $q->sent = (int) $sent;
                        $q->save(false);
                    }
                }
            }
            if ($last) {
                $count = $last->sent_times;
                if ($count == 1) {
                    $x = 4;
                } elseif ($count == 2) {
                    //Respektīvi ievietojam nākamo uzdevumu ar vērtējumu "8 (diezgan sarežģīti un nepieciešams ko vieglāku nākamajā reizē)".
                    //8 (itkā saprotu, ebt pirksti neklausa) Max-x-2/3
                    $x = 8;
                } else {
                    $x = Studentgoals::getUserDifficultyCoef($user_id);
                }
                LectureAssignment::giveNewAssignment($user_id, $x, $last->lecture_id, $spam, $dbg);
                $last->sent_times = (int) $last->sent_times + 1;
                $last->update();
            } else {
                $x = Studentgoals::getUserDifficultyCoef($user_id);
                if ($dbg) {
                    var_dump($x);
                }
                LectureAssignment::giveNewAssignment($user_id, $x, null, $spam, $dbg);
            }
            if ($dbg) {
                echo '<hr />';
            }
        }

        //$model->update();
        $log = ob_get_clean();
        return $this->renderPartial('index', [
            'log' => $log,
        ]);
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
            if ($studentSubplan !== null) {
                $today = date('d.m.Y');
                $match_date = date('d.m.Y', strtotime($studentSubplan["start_date"]));

                $today_split = explode(".", $today);
                $match_date_split = explode(".", $match_date);

                if ($today_split[0] === $match_date_split[0]) {
                    $userFullName = $user['first_name'] . " " . $user['last_name'];

                    $subplan = SchoolSubPlans::findOne($studentSubplan['plan_id']);

                    $id = mt_rand(10000000, 99999999);
                    $title = "rekins-$id.pdf";
                    $invoicePath = $invoiceBasePath.$title;

                    $content = $this->renderPartial('invoiceTemplate', [
                        'id' => $id,
                        'fullName' => $userFullName,
                        'email' => $user['email'],
                        'subplan' => $subplan
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

                    $planModel = StudentSubPlans::findOne($studentSubplan['id']);
                    $planUnlimited = $subplan['months'] === 0;
                    $planEnded = $planModel['sent_invoices_count'] == $subplan['months'];
                    $hasPaidInAdvance = $planModel['times_paid'] > $planModel['sent_invoices_count'];

                    if(!$planEnded || $planUnlimited){
                        if(!$hasPaidInAdvance){
                            $message = "Nosūtam rēķinu par tekošā mēneša nodarbībām. Lai jauka diena!";
                            if(isset($subplan['message']) && $subplan['message']) $message = $subplan['message'];
                            
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
                                $planModel['sent_invoices_count'] += 1;
                                $planModel->update();

                                $invoice = new SentInvoices;
                                $invoice->user_id = $user['id'];
                                $invoice->invoice_number = $id;
                                $invoice->plan_name = $subplan['name'];
                                $invoice->plan_price = $subplan['monthly_cost'];
                                $invoice->plan_start_date = $studentSubplan['start_date'];
                                $invoice->save();
                            }else{
                                Yii::$app
                                    ->mailer
                                    ->compose([
                                        'html' => 'invoice-not-sent-html', 
                                        'text' => 'invoice-not-sent-text'
                                    ], [
                                        'username' => $user['username'],
                                        'email' => $user['email'],
                                    ])
                                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
                                    ->setTo(Yii::$app->params['senderEmail'])
                                    ->setSubject("Skolēnam nenosūtījās rēķins!")
                                    ->send();
                            }
                        }else{
                            $planModel['sent_invoices_count'] += 1;
                            $planModel->update();
                        }
                    }
                }
            }
        }
    }

    public function actionRemindToPay($userId){    
        $user = Users::findOne($userId);   
        if($user){
            $sent = Yii::$app
                ->mailer
                ->compose(['html' => 'reminder-to-pay-html', 'text' => 'reminder-to-pay-text'])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
                ->setTo($user['email'])
                ->setSubject("Atgādinājums par rēķina apmaksu")
                ->send();
        };

        if($sent) {
            Yii::$app->session->setFlash('success', 'Atgādinājums nosūtīts!');
        } else {
            Yii::$app->session->setFlash('error', 'Atgādinājums netika nosūtīts!');
        }
        
        return $this->redirect(Yii::$app->request->referrer);
    }
}
