<?php

namespace app\controllers;

use app\models\Chat;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NeedHelpMessageController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !empty(Yii::$app->user->identity);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }


    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $userContext = Yii::$app->user->identity;
        $recipientId = $userContext->getSchool()->schoolTeacher->user->id;

        $saved = Chat::addNewMessage($post['message'], $userContext->id, $recipientId, 3, $post['lessonId']);

        return $saved;
    }
}
