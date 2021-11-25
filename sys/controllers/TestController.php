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
use app\models\RegistrationLesson;
use app\models\SectionsVisible;
use app\models\Chat;
use app\models\SentInvoices;
use app\models\StudentSubplanPauses;
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
use app\models\BankAccounts;
use app\models\SchoolStudent;
use app\models\CommentResponses;
use app\models\NeedHelpMessages;
use app\models\RegistrationMessage;
use app\models\Trials;
use app\models\User;
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
                'class' => \yii\filters\AccessControl::class,
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

    public function actionIndex()
    {
        // $q = 'SELECT * FROM studentsubplans where
        //     start_date like "%-31" or
        //     start_date like "%-01" OR
        //     start_date like "%-02" OR
        //     start_date like "%-03" OR
        //     start_date like "%-04"';

        // $data = Yii::$app->db->createCommand($q)->queryAll();

        // $ids = [];

        // foreach($data as $d){
        //     $ids[] = $d['user_id'];
        // }

        // var_dump($ids);


        ////lekciju sarežģītības pārrēķināšanai
        // $lectures = Lectures::find()->asArray()->all();

        // foreach($lectures as $lecture){
        //     $lectureDifficulties = LecturesDifficulties::getLectureDifficulties($lecture['id']);
        //     $sum = 0;
        //     if(empty($lectureDifficulties)){
        //         $res = 0;
        //     } else{
        //         foreach ($lectureDifficulties as $pid => $value) {
        //             $sum += (10 * pow(2, ($value / 3)));
        //         }
        //         $res = round($sum / count($lectureDifficulties));
        //     }

        //     if($lecture['complexity'] != $res){
        //         $l = Lectures::find()->where(['id' => $lecture['id']])->one();
        //         $l->complexity = $res;
        //         $l->save();
        //     }
        // }

        //var_dump(RegistrationMessage::getBody(1, false, true));

        // $evals = Userlectureevaluations::find()->joinWith('lecture')->joinWith('user')
        //     ->where(['evaluation_id' => 1])
        //     ->andWhere(['>', 'complexity', 1])
        //     //->andWhere(['>', 'userlectureevaluations.created', '2021-04-01 00:00:00'])
        //     ->orderBy('userlectureevaluations.created asc')
        //     ->asArray()->all();

        // $transformed = [];

        // foreach ($evals as $e) {
        //     if (!array_key_exists($e['user_id'], $transformed)) {
        //         $trial = Trials::find()->where(['user_id' => $e['user_id']])->one();
        //         if (!$trial) $transformed[$e['user_id']] = $e['created'];
        //     }
        // }


        // foreach ($transformed as $id => $t) {
        //     $firstEval = Userlectureevaluations::find()->joinWith('lecture')
        //         ->where(['user_id' => $id, 'evaluation_id' => 1])
        //         ->andWhere(['>', 'complexity', 1])
        //         ->orderBy('created asc')
        //         ->limit(1)
        //         ->asArray()->all()[0];

        //     //var_dump($firstEval);
        //     if ($firstEval['created'] > '2021-05-30 00:00:00') {
        //         echo $firstEval['created'] . "<br>";
        //         echo $firstEval['created'] > '2021-05-30 00:00:00';

        //         $trial = new Trials;
        //         $trial->user_id = $id;
        //         $trial->start_date = $firstEval['created'];
        //         $trial->end_email_sent = 0;
        //         $trial->save();
        //     }


        //     echo "<hr>";
        // }

        // die();

        //var_dump($transformed);

        // $schools = School::find()->asArray()->all();
        // foreach ($schools as $school) {
        //     var_dump($school['id']);
        //     $bankAccount  = new BankAccounts;
        //     $bankAccount->school_id = $school['id'];
        //     echo $bankAccount->save() ? "saved" : "not saved";
        //     echo "<hr>";
        // }

        // $x = StudentSubPlans::findFirstRentSubPlan(1280);
        // if ($x['sent_invoices_count'] === 1 && $x['times_paid'] === 0) {
        //     $invoice = SentInvoices::findOne(['studentsubplan_id' => $x['id']]);
        //     $today = date('d.m.Y');
        //     $match_date = date('d.m.Y', strtotime($invoice["sent_date"] . " + 8 days"));
        // }


        // $userContext = Yii::$app->user->identity;
        // $schoolId = $userContext->getSchool()->id;
        // var_dump($schoolId);

        // $user = Users::findOne(['id' => 813]);
        // $user->password = \Yii::$app->security->generatePasswordHash("1234");
        // $user->update();


        $messages = NeedHelpMessages::find()->all();
        foreach ($messages as $msg) {

            $user = User::findOne(['id' => $msg->author->id]);
            $schoolTeacherId = SchoolTeacher::getBySchoolId($user->getSchool()->id)->user->id;

            Chat::addNewMessage($msg['message'], $msg['author_id'], $schoolTeacherId, 3, $msg['lesson_id'], $msg['created_at']);

            echo "<hr>";
        }
        die();
    }
}
