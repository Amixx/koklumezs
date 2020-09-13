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
use app\models\Lectures;
use app\models\SchoolTeacher;
use app\models\SchoolLecture;
use app\models\Users;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use app\models\CommentresponsesSearch;
use yii\base\Event;
use yii\web\View;
use app\models\School;
use app\models\SchoolStudent;
use app\models\CommentResponses;
use app\models\UserLectures;
use app\models\Userlectureevaluations;

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
                        'matchCallback' => function ($rule, $action) {
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->username);
                        },
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
        $isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
        $isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

        $school = null;
        if ($isTeacher) {
            $school = School::getByTeacher(Yii::$app->user->identity->id);
        } else if ($isStudent) {
            $school = School::getByStudent(Yii::$app->user->identity->id);
        }
        Yii::$app->view->params['school'] = $school;
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
    }
}
