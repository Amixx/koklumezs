<?php

namespace app\models;

use Yii;

class Chat extends \yii\db\ActiveRecord {

    public $userModel;

    public static function tableName() {
        return 'chat';
    }

    public function rules() {
        return [
            [['message'], 'required'],
            [['author_id', 'recipient_id'], 'integer'],
            [['update_date', 'message'], 'safe']
        ];
    }

    public function getAuthor() {
        if (isset($this->userModel))
            return $this->hasOne($this->userModel, ['id' => 'author_id']);
        else
            return $this->hasOne(Yii::$app->getUser()->identityClass, ['id' => 'author_id']);
    }
    
    public function getRecipient() {
        if (isset($this->userModel))
            return $this->hasOne($this->userModel, ['id' => 'recipient_id']);
        else
            return $this->hasOne(Yii::$app->getUser()->identityClass, ['id' => 'recipient_id']);
    }
    
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'author_id' => 'Author',
            'recipient_id' => 'Recipient',
            'update_date' => 'Update Date',
        ];
    }

    public function beforeSave($insert) {
        $this->author_id = Yii::$app->user->id;
        return parent::beforeSave($insert);
    }

    public static function recordsForTwoUsers($authorId, $recipientId){
       
        return static::find()->andWhere([
            'or',
            ['author_id' => $authorId, 'recipient_id' => $recipientId],
            ['author_id' => $recipientId, 'recipient_id' => $authorId],
        ])->orderBy('id desc')->limit(20)->all();
    }

    public static function records() {
        return static::find()->orderBy('id desc')->limit(10)->all();
    }

    public function data($recipientId) {
        $output = '';
        $currentUserId = Yii::$app->user->identity->id;
        $messages = Chat::recordsForTwoUsers($currentUserId, $recipientId);

        if ($messages)
            foreach ($messages as $message) {
                $output .= '<div class="item">
                <p class="message">
                    <a class="name" href="#">
                        <small class="text-muted pull-right" style="color:green"><i class="fa fa-clock-o"></i> ' . $message->update_date . '</small>
                        ' . $message->author->email . '
                    </a>
                   ' . $message->message . '
                </p>
            </div>';
            }

        return $output;
    }

}
