<?php

namespace app\models;

use Yii;

class Chat extends \yii\db\ActiveRecord
{

    public $userModel;

    public static function tableName()
    {
        return 'chat';
    }

    public function rules()
    {
        return [
            [['message'], 'required'],
            [['author_id', 'recipient_id', 'status', 'lesson_id'], 'integer'],
            [['update_date', 'message', 'status'], 'safe']
        ];
    }

    public function getAuthor()
    {
        if (isset($this->userModel)) {
            return $this->hasOne($this->userModel, ['id' => 'author_id']);
        } else {
            return $this->hasOne(Users::class, ['id' => 'author_id']);
        }
    }

    public function getRecipient()
    {
        if (isset($this->userModel)) {
            return $this->hasOne($this->userModel, ['id' => 'recipient_id']);
        } else {
            return $this->hasOne(Users::class, ['id' => 'recipient_id']);
        }
    }

    public function getLesson()
    {
        return $this->hasOne(Lectures::class, ['id' => 'lesson_id']);
    }

    public function getOpenTime()
    {
        return $this->hasOne(CorrespondenceOpentimes::class, ['author_id' => 'recipient_id', 'recipient_id' => 'author_id']);
    }

    public function isUnread()
    {
        if (!$this->openTime) return true;
        return $this->openTime->opentime < $this->update_date;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'author_id' => 'Author',
            'recipient_id' => 'Recipient',
            'update_date' => 'Update Date',
        ];
    }

    public static function recordsForTwoUsers($authorId, $recipientId, $isTeacher)
    {
        return static::find()->andWhere([
            'or',
            ['author_id' => $authorId, 'recipient_id' => $recipientId],
            ['author_id' => $recipientId, 'recipient_id' => $authorId],
        ])->orderBy('update_date asc')->all();
    }

    public static function addNewMessage($message, $authorId, $recipientId, $status = 1, $lessonId = null, $createdAt = null)
    {
        // 1 - Parasta ziņa, ko nosūta čatā
        // 2 - Ziņa no sistēmas - piem. pēc noteikta nodarbības novērtējuma
        // 3 - Ziņa no skolēna par to, ka vajag palīdzību (izsaucama ar pogu nodarbībā)
        $model = new Chat;

        $model->message = $message;
        $model->author_id = $authorId;
        $model->status = $status;
        $model->recipient_id = $recipientId;
        $model->lesson_id = $lessonId;
        if ($createdAt) {
            $model->update_date = $createdAt;
        }


        return $model->save();
    }

    public function data($recipientId, $updateOpentime = true)
    {
        $output = "";
        $userList = null;
        $userContext = Yii::$app->user->identity;
        $currentUserId = $userContext->id;
        $isTeacher = $userContext->isTeacher();
        $messages = Chat::recordsForTwoUsers($currentUserId, $recipientId, $isTeacher);
        $user = Users::findOne($currentUserId);
        $latestConversations = $isTeacher ? $user->getLatestConversations() : null;


        if ($updateOpentime) {
            CorrespondenceOpentimes::updateOpentime($currentUserId, $recipientId);
        }

        if ($messages) {
            foreach ($messages as $message) {
                $isNeedHelpMessage = $message->status === 3;
                $outerClass = "chat-message";
                $needHelpPrefix = "";

                if ($isNeedHelpMessage) {
                    $outerClass .= " $outerClass--need-help";
                    $lessonTitle = $message->lesson->title;
                    $needHelpPrefix = "<p class='chat-message-help-prefix'>Vajadzīga palīdzība ar nodarbību <u>$lessonTitle</u>...</p>";
                }


                $output .= '<div class="' . $outerClass . '">
                    ' . $needHelpPrefix . ' 
                    <p class="message">
                        <a class="name" href="#">
                            <small class="text-muted pull-right" style="color:green"><i class="fa fa-clock-o"></i> ' . $message->update_date . '</small>
                            ' . $message->author->first_name . ' ' . $message->author->last_name . '                        
                        </a>
                    ' . $message->message . '
                    </p>
                </div>';
            }
        }

        if ($latestConversations) {
            foreach ($latestConversations as $conv) {
                if ($conv == NULL || $conv['user'] == null) continue;
                $uId = $conv['user']->id;
                $userFullName = Users::getFullName($conv['user']);

                $isActive = $uId == $recipientId;
                $style = $isActive ? "background-color:#b0f3fc;" : "";
                if ($conv['is_unread']) $style .= "font-weight: bold;";

                $userList .= "
                  <li class='chat-user-item' data-userid='" . $uId . "' style='" . $style . "'>
                    <span class='glyphicon glyphicon-user'></span>
                    <span>" . $userFullName . "</span>
                  </li>
                ";
            }
        }

        return [
            'content' => $output,
            'userList' => $userList
        ];
    }

    public static function isChatCooldown()
    {
        $userId = Yii::$app->user->id;
        $lastSystemMessage = self::find()->where(['recipient_id' => $userId, 'status' => 2])->orderBy('id desc')->one();
        if (!$lastSystemMessage) {
            return false;
        }

        $lastMessageTime = strtotime($lastSystemMessage->update_date);
        $now = time();
        $isCooldown = !(round(($now - $lastMessageTime) / 60, 2) >= 10);

        return $isCooldown;
    }

    public static function unreadDataForCurrentUser()
    {
        $userContext = Yii::$app->user->identity;

        $unreadMessages = self::find()
            ->where("opentime IS NULL OR update_date > opentime")
            ->andWhere(['chat.recipient_id' => $userContext->id])
            ->andWhere(['>', 'update_date', '2021-03-19 23:23:17'])
            ->joinWith('openTime')
            ->asArray()
            ->all();

        $unreadConversations = count(
            array_unique(
                array_column($unreadMessages, "author_id")
            )
        );

        return [
            'messages' => count($unreadMessages),
            'conversations' => $unreadConversations,
        ];
    }
}
