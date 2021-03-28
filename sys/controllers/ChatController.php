<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Chat;
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
        if (Yii::$app->user->isGuest){
            return 0;
        }
        return Chat::unreadCountForCurrentUser();
    }
}
