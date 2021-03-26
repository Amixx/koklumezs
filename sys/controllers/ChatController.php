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
use app\models\Chat;
use app\models\LectureViews;
use app\models\SchoolStudent;
use app\models\CommentResponses;
use app\models\UserLectures;
use app\models\Userlectureevaluations;
use kartik\mpdf\Pdf;
use app\widgets\ChatRoom;

class ChatController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionSendChat()
    {
        return json_encode(ChatRoom::sendChat($_POST));
    }

    public function actionGetUnreadCount()
    {
        if (Yii::$app->user->isGuest) return 0;
        return Chat::unreadCountForCurrentUser();
    }
}
