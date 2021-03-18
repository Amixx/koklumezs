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
        ])->orderBy('id asc')->all();
    }

    public static function getUnreadCountInCorrespondence($authorId, $recipientId){
        $opentime = CorrespondenceOpentimes::getOpentimeValue($authorId, $recipientId);
        $lastOpenedChat = Yii::$app->user->identity['last_opened_chat'];
    
        $query = static::find()
            ->select(['COUNT(*) as count'])
            ->where([
                'recipient_id' => $authorId,
                'author_id' => $recipientId
            ]);

        if($opentime){
            $query->andWhere(['>', 'update_date', $opentime]);
        }
        
        // LEGACY: for transition from last_opened_chat to opentime for each correspondence. remove after a month or so (?)
        if($lastOpenedChat){
            $query->andWhere(['>', 'update_date', $lastOpenedChat]);
        }

        $data = $query->createCommand()->queryAll();

        return (int) $data[0]["count"];
    }

    public static function unreadCountForCurrentUser(){
        $currentUserId = Yii::$app->user->identity->id;

        $totalUnreadCount = 0;
        $usersWithConversations = self::getUsersWithConversations($currentUserId);
        foreach($usersWithConversations as $user){
            $totalUnreadCount += self::getUnreadCountInCorrespondence($currentUserId, $user['id']);
        }
        
        return $totalUnreadCount;
    }

    public static function hasNewChats($senderId){
        $currentUserId = Yii::$app->user->identity->id;

        return self::getUnreadCountInCorrespondence($currentUserId, $senderId);
    }

    public static function getUsersWithConversations($authorId){
        $userIdsData = static::find()
            ->select(['author_id', 'recipient_id'])
            ->andWhere([
                'or',
                ['author_id' => $authorId],
                ['recipient_id' => $authorId],
            ])
            ->orderBy('id desc')
            ->asArray()
            ->all();

        $userIds = [];
        foreach($userIdsData as $data){
            if(!in_array($data['author_id'], $userIds) && $data['author_id'] != $authorId){
                $userIds[] = $data['author_id'];
            }
            if(!in_array($data['recipient_id'], $userIds) && $data['recipient_id'] != $authorId) {
                $userIds[] = $data['recipient_id'];
            }
        }

        $users = Users::find()->where(["in", "id", $userIds])->asArray()->all();
        $usersByIds = array_column($users, NULL, 'id');
        $usersSorted = array_map(function($id)use($usersByIds){
            if(isset($usersByIds[$id])){
                return $usersByIds[$id];
            }
        }, $userIds);

        return $usersSorted;
    }

    public static function findFirstRecipient(){
        $authorId = Yii::$app->user->identity->id;
        $data = static::find()->where(['!=', 'recipient_id', $authorId])->andWhere([
                'or',
                ['author_id' => $authorId],
                ['recipient_id' => $authorId],
            ])->orderBy('id desc')
            ->limit(1)
            ->one();

        if($data) return $data['recipient_id'];
        else return null;
    }

    public function data($recipientId, $updateOpentime = true) {
        $output = "";
        $userList = null;
        $currentUserId = Yii::$app->user->identity->id;
        $isTeacher = Users::isCurrentUserTeacher();
        $messages = Chat::recordsForTwoUsers($currentUserId, $recipientId);
        $usersWithConversations = $isTeacher ? Chat::getUsersWithConversations($currentUserId) : null;

        if($updateOpentime){
            CorrespondenceOpentimes::updateOpentime($currentUserId, $recipientId);
        }

        if ($messages)
            foreach ($messages as $message) {
                $output .= '<div class="item">
                <p class="message">   
                    <a class="name" href="#">
                        <small class="text-muted pull-right" style="color:green"><i class="fa fa-clock-o"></i> ' . $message->update_date . '</small>
                         ' . $message->author->first_name .' '. $message->author->last_name . '                        
                    </a>
                   ' . $message->message . '
                </p>
            </div>';
        }

        if($usersWithConversations){
            foreach ($usersWithConversations as $user) {
                $isActive = $user['id'] == $recipientId;
                $style = $isActive ? "background-color:#b0f3fc;" : "";
                $hasNewChats = Chat::hasNewChats($user['id']);
                if($hasNewChats)
                   $style .= "font-weight: bold;";
                $userList .= "
                  <li class='chat-user-item' data-userid='" . $user['id'] . "' style='" . $style . "'>
                    <span class='glyphicon glyphicon-user'></span>
                    <span>" . $user['first_name'] . " " . $user['last_name'] . "</span>
                  </li>
                ";
            }
        }

        return [
            'content' => $output,
            'userList' => $userList
        ];
    }
}
