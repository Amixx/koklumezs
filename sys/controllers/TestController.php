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
            $currentUser = Users::getByUsername(Yii::$app->user->identity->username);
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

        // $user = Users::findByUsername(Yii::$app->user->identity->username);
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

        // $user = Users::findByUsername(Yii::$app->user->identity->username);
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
    }
}
