<?php

namespace app\controllers;

use app\models\LectureAssignment;
use app\models\School;
use app\models\Sentlectures;
use app\models\Studentgoals;
use app\models\UserLectures;
use app\models\Lectures;
use app\models\Users;
use app\models\StudentSubPlans;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\helpers\InvoiceManager;
use app\helpers\EmailSender;
use app\models\SentInvoices;
use app\models\Trials;

class CronController extends Controller
{

    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->queryParams;
        if (!isset($get['send'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $users = Users::getActiveStudents();
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

    public function actionUserlectures()
    {
        $get = Yii::$app->request->queryParams;
        if (!isset($get['id'])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $users = Users::find()->where(['id' => $get['id'], 'is_deleted' => false])->asArray()->all();
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

        $log = ob_get_clean();
        return $this->renderPartial('index', [
            'log' => $log,
        ]);
    }

    public function actionForEachStudent()
    {
        $students = Users::getAllStudents();

        foreach ($students as $student) {
            Yii::$app->language = $student['language'];

            $studentSubplans = StudentSubPlans::getActivePlansForStudent($student["id"]);

            foreach ($studentSubplans as $studentSubplan) {
                if (StudentSubPlans::shouldSendAdvanceInvoice($studentSubplan)) {
                    InvoiceManager::sendAdvanceInvoice($student, $studentSubplan);
                } else if (StudentSubPlans::hasPaidInAdvance($studentSubplan)) {
                    StudentSubPlans::increaseSentInvoicesCount($studentSubplan);
                }
            }

            if (Trials::shouldSendTrialEndedEmail($student["id"])) {
                $sent = EmailSender::sendTrialEndMessage($student);

                if (
                    $student
                    && $student['status']
                    && $student['status'] == Users::STATUS_ACTIVE
                ) {
                    $stud = Users::findOne($student['id']);
                    $stud->status = Users::STATUS_PASSIVE;
                    $stud->update();
                }

                if ($sent) {
                    Trials::markEndMessageSent($student["id"]);
                }
            }

            $firstRentPlan = StudentSubPlans::findFirstRentSubPlan($student['id']);
            if ($firstRentPlan && $firstRentPlan['sent_invoices_count'] === 1 && $firstRentPlan['times_paid'] === 0) {
                $invoice = SentInvoices::findOne(['studentsubplan_id' => $firstRentPlan['id']]);
                $today = date('d.m.Y');
                $match_date = date('d.m.Y', strtotime($invoice["sent_date"] . " + 11 days"));

                if ($today === $match_date) {
                    EmailSender::sendFirstRentPaymentFailedEmail($student);
                    Users::softDelete($student['id']);
                }
            }
        }
    }

    public function actionRemindToPay($studentSubplanId)
    {
        $studentSubplan = StudentSubPlans::find()->where(['studentsubplans.id' => $studentSubplanId])->joinWith('plan')->joinWith('user')->asArray()->one();
        $school = School::getByStudent($studentSubplan['user_id']);

        $lateMonthsCount = $studentSubplan['sent_invoices_count'] - $studentSubplan['times_paid'];

        $sent = false;
        if ($studentSubplan['user']) {
            $sent = EmailSender::sendReminderToPay(
                $school['email'],
                $studentSubplan['user']['email'],
                $lateMonthsCount,
                $studentSubplan['plan']['name']
            );
        }

        if ($sent) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Reminder sent') . '!');
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Could not send reminder') . '!');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
