<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\Chat;
use app\models\SchoolTeacher;
use yii\helpers\Url;

class ChatRoom extends Widget
{

    public $sourcePath = '@vendor/assets';
    public $css = [];
    public $js = [
        'js/custom.js',
        'js/workouts.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $models;
    public $url;
    public $userModel;
    public $model;
    public $recipientId;

    public function run()
    {
        parent::init();

        $this->model = new Chat();
        $userContext = Yii::$app->user->identity;
        $schoolTeacher = SchoolTeacher::getBySchoolId($userContext->school->id);
        $this->url = Url::to(['/chat/send-chat']);
        $this->recipientId = $schoolTeacher['user']['id'];
        $this->userModel = Users::class;
        $data = $this->model->data($this->recipientId, false);

        return $this->render('index', [
            'data' => $data,
            'url' => $this->url,
            'userModel' => $this->userModel,
            'recipientId' => $this->recipientId,
        ]);
    }

    public static function sendChat($post)
    {
        if (isset($post['message'])) {
            $message = $post['message'];
        }
        if (isset($post['model'])) {
            $userModel = $post['model'];
        } else {
            $userModel = Yii::$app->getUser()->identityClass;
        }

        if (isset($post['recipient_id'])) {
            $recipient_id = $post['recipient_id'];
        }

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
