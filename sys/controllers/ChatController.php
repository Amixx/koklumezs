<?php

namespace app\controllers;

use app\models\Chat;
use Yii;
use yii\web\Controller;
use app\models\Users;
use app\widgets\ChatRoom;

class ChatController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
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
        if (Yii::$app->user->isGuest) {
            return 0;
        }

        return json_encode(Chat::unreadDataForCurrentUser());
    }
}
