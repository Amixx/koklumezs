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
    }
}
