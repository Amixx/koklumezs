<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\db\Connection;
use app\models\Difficulties;
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
use app\Models\School;
use app\Models\SchoolStudent;
use app\Models\CommentResponses;
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
                            return Users::isAdminOrTeacher(Yii::$app->user->identity->email);
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
            $currentUser = Users::getByEmail(Yii::$app->user->identity->email);
            if ($currentUser['language'] === "lv") Yii::$app->language = 'lv';
        }


        Yii::$app->language = 'lv-LV';
        echo \Yii::t('app', 'Abilities');
    }
}
