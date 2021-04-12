<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\Chat;
use app\models\Users;
use app\models\SchoolTeacher;

class ChatRoom extends Widget
{

    public $sourcePath = '@vendor/assets';
    public $css = [];
    public $js = [
        'js/custom.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $models;
    public $url;
    public $userModel;
    public $model;
    public $recipientId;

    public function init()
    {
        $this->model = new Chat();
        if ($this->userModel === NULL) {
            $this->userModel = Yii::$app->getUser()->identityClass;
        }

        $this->model->userModel = $this->userModel;

        $this->recipientId = Users::isCurrentUserTeacher() ? Chat::findFirstRecipient() : SchoolTeacher::getByCurrentStudent()['user_id'];

        parent::init();
    }

    public function run()
    {
        parent::init();
        $model = new Chat();
        $model->userModel = $this->userModel;
        $data = $model->data($this->recipientId, false);

        return $this->render('index', [
            'data' => $data,
            'url' => $this->url,
            'userModel' => $this->userModel,
            'recipientId' => $this->recipientId,
        ]);
    }

    public static function sendChat($post)
    {
        if (isset($post['message']))
            $message = $post['message'];
        if (isset($post['model']))
            $userModel = $post['model'];
        else
            $userModel = Yii::$app->getUser()->identityClass;

        if (isset($post['recipient_id']))
            $recipient_id = $post['recipient_id'];

        $model = new Chat;
        $model->userModel = $userModel;

        if ($message) {
            $model->message = $message;
            $model->author_id = Yii::$app->user->identity->id;
            $model->recipient_id = $recipient_id;

            if ($model->save()) {
                return $model->data($recipient_id);
            } else {
                print_r($model->getErrors());
                exit(0);
            }
        } else {
            return $model->data($recipient_id);
        }
    }
}
